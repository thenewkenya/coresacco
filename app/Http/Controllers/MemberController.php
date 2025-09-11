<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\MemberRequest;

class MemberController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of members
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $query = User::where('role', 'member')
            ->with(['branch', 'accounts', 'loans']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('member_number', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('membership_status', $request->status);
        }

        // Filter by branch
        if ($request->filled('branch')) {
            $query->where('branch_id', $request->branch);
        }

        $members = $query->latest()->paginate(15);
        $branches = Branch::all();

        // Statistics
        $stats = [
            'total_members' => User::where('role', 'member')->count(),
            'active_members' => User::where('role', 'member')->where('membership_status', 'active')->count(),
            'inactive_members' => User::where('role', 'member')->where('membership_status', 'inactive')->count(),
            'new_this_month' => User::where('role', 'member')->whereMonth('created_at', now()->month)->count(),
        ];

        return view('members.index', compact('members', 'branches', 'stats'));
    }

    /**
     * Show the form for creating a new member
     */
    public function create()
    {
        $this->authorize('create', User::class);
        
        $branches = Branch::all();
        return view('members.create', compact('branches'));
    }

    /**
     * Store a newly created member
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|string|max:20',
            'id_number' => 'required|string|max:20|unique:users,id_number',
            'address' => 'required|string|max:500',
            'branch_id' => 'required|exists:branches,id',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            DB::beginTransaction();

            // Generate member number
            $memberCount = User::where('role', 'member')->count();
            $memberNumber = 'MB' . str_pad($memberCount + 1, 6, '0', STR_PAD_LEFT);

            $member = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'member_number' => $memberNumber,
                'phone_number' => $validated['phone_number'],
                'id_number' => $validated['id_number'],
                'address' => $validated['address'],
                'branch_id' => $validated['branch_id'],
                'membership_status' => 'active',
                'joining_date' => now(),
                'role' => 'member',
            ]);

            // Assign member role
            $memberRole = \App\Models\Role::where('slug', 'member')->first();
            if ($memberRole) {
                $member->roles()->attach($memberRole);
            }

            // Create default savings account
            $member->accounts()->create([
                'account_number' => 'SA' . str_pad($member->id, 8, '0', STR_PAD_LEFT),
                'account_type' => 'savings',
                'status' => 'active',
                'balance' => 0,
            ]);

            DB::commit();

            return redirect()->route('members.index')
                ->with('success', 'Member registered successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to register member: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified member
     */
    public function show(User $member)
    {
        $this->authorize('view', $member);

        $member->load([
            'branch', 
            'accounts.transactions', 
            'loans.loanType', 
            'transactions' => function($query) {
                $query->latest()->limit(10);
            }
        ]);

        // Calculate member statistics
        $stats = [
            'total_deposits' => $member->transactions()->where('type', 'deposit')->sum('amount'),
            'total_withdrawals' => $member->transactions()->where('type', 'withdrawal')->sum('amount'),
            'active_loans' => $member->loans()->where('status', 'active')->count(),
            'total_accounts' => $member->accounts()->count(),
        ];

        return view('members.show', compact('member', 'stats'));
    }

    /**
     * Show the form for editing the specified member
     */
    public function edit(User $member)
    {
        $this->authorize('update', $member);
        
        $branches = Branch::all();
        return view('members.edit', compact('member', 'branches'));
    }

    /**
     * Update the specified member
     */
    public function update(Request $request, User $member)
    {
        $this->authorize('update', $member);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $member->id,
            'phone_number' => 'required|string|max:20',
            'id_number' => 'required|string|max:20|unique:users,id_number,' . $member->id,
            'address' => 'required|string|max:500',
            'branch_id' => 'required|exists:branches,id',
            'membership_status' => 'required|in:active,inactive,suspended',
        ]);

        try {
            $member->update($validated);

            return redirect()->route('members.show', $member)
                ->with('success', 'Member updated successfully!');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update member: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified member
     */
    public function destroy(User $member)
    {
        $this->authorize('delete', $member);

        try {
            // Check if member has active accounts or loans
            if ($member->accounts()->where('balance', '>', 0)->exists()) {
                return back()->with('error', 'Cannot delete member with active account balances.');
            }

            if ($member->loans()->where('status', 'active')->exists()) {
                return back()->with('error', 'Cannot delete member with active loans.');
            }

            $member->delete();

            return redirect()->route('members.index')
                ->with('success', 'Member deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete member: ' . $e->getMessage());
        }
    }

    /**
     * Show member profile (for authenticated member)
     */
    public function profile()
    {
        return redirect()->route('settings.profile');
    }

    /**
     * Get member's transaction history
     */
    public function transactions(User $member)
    {
        // Check if user can view this member's transactions
        $user = auth()->user();
        if ($user->hasRole('member') && $user->id !== $member->id) {
            abort(403, 'You can only view your own transaction history.');
        }
        
        if (!$user->hasRole('admin') && !$user->hasRole('staff') && !$user->hasRole('manager') && $user->id !== $member->id) {
            abort(403, 'Unauthorized access.');
        }

        $transactions = $member->transactions()
                             ->with(['account'])
                             ->latest()
                             ->paginate(20);
        
        return view('members.transactions', compact('member', 'transactions'));
    }
} 