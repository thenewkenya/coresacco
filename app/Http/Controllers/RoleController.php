<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        // Authorization is now handled by RolePolicy in individual methods
    }

    /**
     * Display roles management dashboard
     */
    public function index(Request $request)
    {
        // Check if user has permission to view roles
        if (!Auth::user()->hasAnyRole(['admin', 'manager'])) {
            abort(403, 'Unauthorized to access role management.');
        }

        $search = $request->get('search');
        $roleFilter = $request->get('role_filter');

        // Get roles with user counts
        $roles = Role::withCount('users')->get();

        // Get users with role information for assignment section
        $usersQuery = User::with('roles')
            ->when($search, function ($q) use ($search) {
                $q->where(function($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($roleFilter, function ($q) use ($roleFilter) {
                $q->whereHas('roles', function($query) use ($roleFilter) {
                    $query->where('slug', $roleFilter);
                });
            });

        $users = $usersQuery->latest()->paginate(20);

        // Statistics
        $stats = [
            'total_users' => User::count(),
            'total_roles' => Role::count(),
            'admin_count' => User::whereHas('roles', fn($q) => $q->where('slug', 'admin'))->count(),
            'member_count' => User::whereHas('roles', fn($q) => $q->where('slug', 'member'))->count(),
        ];

        return view('roles.index', compact('roles', 'users', 'stats', 'search', 'roleFilter'));
    }

    /**
     * Show the form for creating a new role
     */
    public function create()
    {
        return view('roles.create', [
            'availablePermissions' => Role::PERMISSIONS
        ]);
    }

    /**
     * Store a newly created role
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'description' => 'nullable|string|max:1000',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|in:' . implode(',', Role::PERMISSIONS),
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        
        // Ensure slug is unique
        $originalSlug = $validated['slug'];
        $counter = 1;
        while (Role::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        Role::create($validated);

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified role
     */
    public function show(Role $role)
    {
        $role->load(['users' => function($query) {
            $query->latest();
        }]);

        $roleStats = [
            'user_count' => $role->users_count ?? $role->users->count(),
            'active_users' => $role->users->where('email_verified_at', '!=', null)->count(),
            'recent_assignments' => $role->users()->wherePivot('created_at', '>=', now()->subDays(30))->count(),
        ];

        return view('roles.show', compact('role', 'roleStats'));
    }

    /**
     * Show the form for editing the specified role
     */
    public function edit(Role $role)
    {
        // Prevent editing admin role for safety
        if ($role->slug === 'admin' && !Auth::user()->hasRole('admin')) {
            abort(403, 'Cannot edit admin role.');
        }

        return view('roles.edit', [
            'role' => $role,
            'availablePermissions' => Role::PERMISSIONS
        ]);
    }

    /**
     * Update the specified role
     */
    public function update(Request $request, Role $role)
    {
        // Prevent editing admin role for safety
        if ($role->slug === 'admin' && !Auth::user()->hasRole('admin')) {
            abort(403, 'Cannot edit admin role.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role)],
            'description' => 'nullable|string|max:1000',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|in:' . implode(',', Role::PERMISSIONS),
        ]);

        // Only update slug if name changed and it's not a protected role
        if ($role->name !== $validated['name'] && !in_array($role->slug, ['admin', 'member', 'staff', 'manager'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $role->update($validated);

        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified role
     */
    public function destroy(Role $role)
    {
        // Prevent deletion of system roles
        if (in_array($role->slug, ['admin', 'member', 'staff', 'manager'])) {
            return back()->withErrors(['delete' => 'Cannot delete system roles.']);
        }

        // Check if role has users
        if ($role->users()->count() > 0) {
            return back()->withErrors(['delete' => 'Cannot delete role with assigned users. Please reassign users first.']);
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    /**
     * Assign role to user
     */
    public function assignRole(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $role = Role::findOrFail($validated['role_id']);

        // Check if user already has this role
        if ($user->roles()->where('role_id', $role->id)->exists()) {
            return back()->withErrors(['assignment' => 'User already has this role.']);
        }

        $user->roles()->attach($role);

        return back()->with('success', "Role '{$role->name}' assigned to {$user->name} successfully.");
    }

    /**
     * Remove role from user
     */
    public function removeRole(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $role = Role::findOrFail($validated['role_id']);

        // Prevent removing last admin role
        if ($role->slug === 'admin') {
            $adminCount = User::whereHas('roles', fn($q) => $q->where('slug', 'admin'))->count();
            if ($adminCount <= 1) {
                return back()->withErrors(['removal' => 'Cannot remove the last admin user.']);
            }
        }

        // Prevent users from removing their own admin role
        if ($user->id === Auth::id() && $role->slug === 'admin') {
            return back()->withErrors(['removal' => 'You cannot remove your own admin role.']);
        }

        $user->roles()->detach($role);

        return back()->with('success', "Role '{$role->name}' removed from {$user->name} successfully.");
    }

    /**
     * Bulk assign roles to multiple users
     */
    public function bulkAssign(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        $role = Role::findOrFail($validated['role_id']);
        $users = User::whereIn('id', $validated['user_ids'])->get();

        $assigned = 0;
        foreach ($users as $user) {
            if (!$user->roles()->where('role_id', $role->id)->exists()) {
                $user->roles()->attach($role);
                $assigned++;
            }
        }

        return back()->with('success', "Role '{$role->name}' assigned to {$assigned} users successfully.");
    }

    /**
     * Get role permissions for AJAX
     */
    public function permissions(Role $role)
    {
        return response()->json([
            'role' => $role->name,
            'permissions' => $role->permissions ?? [],
            'all_permissions' => Role::PERMISSIONS
        ]);
    }

    /**
     * Update role permissions
     */
    public function updatePermissions(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|in:' . implode(',', Role::PERMISSIONS),
        ]);

        $role->update(['permissions' => $validated['permissions'] ?? []]);

        return response()->json([
            'success' => true,
            'message' => 'Permissions updated successfully.'
        ]);
    }

    /**
     * Get users without specific role for AJAX
     */
    public function availableUsers(Role $role)
    {
        $users = User::whereDoesntHave('roles', function($query) use ($role) {
            $query->where('role_id', $role->id);
        })->select('id', 'name', 'email')->get();

        return response()->json($users);
    }
} 