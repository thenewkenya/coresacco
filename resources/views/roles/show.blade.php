<x-layouts.app :title="$role->name">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        @php
                            $colors = [
                                'admin' => 'red',
                                'manager' => 'blue', 
                                'staff' => 'emerald',
                                'member' => 'purple'
                            ];
                            $color = $colors[$role->slug] ?? 'gray';
                        @endphp
                        <div class="h-12 w-12 rounded-lg bg-{{ $color }}-100 dark:bg-{{ $color }}-900/30 flex items-center justify-center">
                            <flux:icon.shield-check class="h-6 w-6 text-{{ $color }}-600 dark:text-{{ $color }}-400" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                                {{ $role->name }}
                            </h1>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $role->description ?? 'No description available.' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <flux:button variant="ghost" :href="route('roles.index')" wire:navigate>
                            {{ __('Back to Roles') }}
                        </flux:button>
                        @can('update', $role)
                        <flux:button variant="primary" :href="route('roles.edit', $role)" wire:navigate>
                            {{ __('Edit Role') }}
                        </flux:button>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <flux:icon.users class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $roleStats['user_count'] }}</p>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Total Users</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                            <flux:icon.check-badge class="h-6 w-6 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $roleStats['active_users'] }}</p>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Active Users</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                            <flux:icon.plus class="h-6 w-6 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $roleStats['recent_assignments'] }}</p>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Recent Assignments</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Permissions -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ __('Permissions') }} ({{ count($role->permissions ?? []) }})
                            </h3>
                            @can('update', $role)
                            <flux:button variant="outline" size="sm" :href="route('roles.edit', $role)" wire:navigate>
                                {{ __('Manage Permissions') }}
                            </flux:button>
                            @endcan
                        </div>

                        @if(empty($role->permissions))
                        <div class="text-center py-12">
                            <flux:icon.shield-exclamation class="mx-auto h-12 w-12 text-zinc-400 dark:text-zinc-600" />
                            <h3 class="mt-4 text-sm font-medium text-zinc-900 dark:text-zinc-100">No permissions assigned</h3>
                            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                                This role has no permissions assigned yet.
                            </p>
                        </div>
                        @else
                        @php
                            $permissionGroups = [
                                'Member Management' => ['view-members', 'create-members', 'edit-members', 'delete-members'],
                                'Account Management' => ['view-accounts', 'create-accounts', 'edit-accounts', 'delete-accounts', 'process-transactions'],
                                'Loan Management' => ['view-loans', 'create-loans', 'edit-loans', 'delete-loans', 'approve-loans', 'disburse-loans'],
                                'Branch Management' => ['view-branches', 'manage-branches'],
                                'Reports & Settings' => ['view-reports', 'export-reports', 'manage-settings', 'manage-roles']
                            ];
                        @endphp

                        <div class="space-y-6">
                            @foreach($permissionGroups as $groupName => $groupPermissions)
                            @php
                                $roleGroupPermissions = array_intersect($role->permissions, $groupPermissions);
                            @endphp
                            @if(!empty($roleGroupPermissions))
                            <div>
                                <h4 class="text-md font-medium text-zinc-900 dark:text-zinc-100 mb-3">
                                    {{ $groupName }}
                                </h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach($roleGroupPermissions as $permission)
                                    <div class="flex items-center space-x-3 p-3 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-emerald-50 dark:bg-emerald-900/20">
                                        <flux:icon.check class="h-4 w-4 text-emerald-600 dark:text-emerald-400" />
                                        <div>
                                            <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                                {{ ucwords(str_replace('-', ' ', $permission)) }}
                                            </p>
                                            <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                                @switch($permission)
                                                    @case('view-members') Can view member information @break
                                                    @case('create-members') Can register new members @break
                                                    @case('edit-members') Can modify member details @break
                                                    @case('delete-members') Can remove members @break
                                                    @case('view-accounts') Can view account details @break
                                                    @case('create-accounts') Can open new accounts @break
                                                    @case('edit-accounts') Can modify account settings @break
                                                    @case('delete-accounts') Can close accounts @break
                                                    @case('process-transactions') Can process deposits/withdrawals @break
                                                    @case('view-loans') Can view loan information @break
                                                    @case('create-loans') Can create loan applications @break
                                                    @case('edit-loans') Can modify loan details @break
                                                    @case('delete-loans') Can delete loan records @break
                                                    @case('approve-loans') Can approve/reject loans @break
                                                    @case('disburse-loans') Can disburse approved loans @break
                                                    @case('view-branches') Can view branch information @break
                                                    @case('manage-branches') Can manage branch operations @break
                                                    @case('view-reports') Can access reports @break
                                                    @case('export-reports') Can export report data @break
                                                    @case('manage-settings') Can modify system settings @break
                                                    @case('manage-roles') Can manage user roles @break
                                                    @default {{ $permission }}
                                                @endswitch
                                            </p>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Assigned Users -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ __('Assigned Users') }}
                            </h3>
                            @can('update', \App\Models\Role::class)
                            <flux:button variant="outline" size="sm" onclick="document.getElementById('assign-user-modal').showModal()">
                                {{ __('Assign User') }}
                            </flux:button>
                            @endcan
                        </div>

                        @if($role->users->isEmpty())
                        <div class="text-center py-8">
                            <flux:icon.users class="mx-auto h-8 w-8 text-zinc-400 dark:text-zinc-600" />
                            <h3 class="mt-2 text-sm font-medium text-zinc-900 dark:text-zinc-100">No users assigned</h3>
                            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                No users have this role yet.
                            </p>
                        </div>
                        @else
                        <div class="space-y-4">
                            @foreach($role->users->take(10) as $user)
                            <div class="flex items-center justify-between p-3 rounded-lg border border-zinc-200 dark:border-zinc-700">
                                <div class="flex items-center space-x-3">
                                    <div class="h-8 w-8 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                                        <span class="text-xs font-bold text-white">
                                            {{ $user->initials() }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $user->name }}</p>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $user->email }}</p>
                                    </div>
                                </div>
                                @can('update', \App\Models\Role::class)
                                <form method="POST" action="{{ route('roles.remove') }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                    <input type="hidden" name="role_id" value="{{ $role->id }}">
                                    <flux:button type="submit" variant="ghost" size="sm" class="text-red-600 dark:text-red-400" onclick="return confirm('Remove this user from the role?')">
                                        <flux:icon.x-mark class="h-4 w-4" />
                                    </flux:button>
                                </form>
                                @endcan
                            </div>
                            @endforeach

                            @if($role->users->count() > 10)
                            <div class="text-center pt-4 border-t border-zinc-200 dark:border-zinc-700">
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                    and {{ $role->users->count() - 10 }} more users...
                                </p>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>

                    <!-- Role Actions -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 mt-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                            {{ __('Role Actions') }}
                        </h3>
                        <div class="space-y-3">
                            @can('update', $role)
                            <flux:button variant="outline" size="sm" :href="route('roles.edit', $role)" wire:navigate class="w-full">
                                {{ __('Edit Role') }}
                            </flux:button>
                            @endcan
                            
                            @can('delete', $role)
                            @if(!in_array($role->slug, ['admin', 'member', 'staff', 'manager']))
                            <form method="POST" action="{{ route('roles.destroy', $role) }}" class="w-full">
                                @csrf
                                @method('DELETE')
                                <flux:button type="submit" variant="danger" size="sm" class="w-full" onclick="return confirm('Are you sure you want to delete this role? This action cannot be undone.')">
                                    {{ __('Delete Role') }}
                                </flux:button>
                            </form>
                            @else
                            <div class="p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg">
                                <p class="text-xs text-amber-700 dark:text-amber-300">
                                    System roles cannot be deleted.
                                </p>
                            </div>
                            @endif
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @can('update', \App\Models\Role::class)
    <!-- Assign User Modal -->
    <dialog id="assign-user-modal" class="modal rounded-xl border-0 shadow-2xl backdrop:bg-black/50">
        <form method="POST" action="{{ route('roles.assign') }}" class="modal-box bg-white dark:bg-zinc-800 max-w-md">
            @csrf
            <h3 class="font-bold text-lg text-zinc-900 dark:text-zinc-100 mb-4">Assign User to {{ $role->name }}</h3>
            
            <input type="hidden" name="role_id" value="{{ $role->id }}">
            
            <div class="form-control mb-4">
                <flux:label for="user_id">Select User</flux:label>
                <select name="user_id" required class="w-full rounded-md border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100">
                    <option value="">Choose a user...</option>
                    @foreach(\App\Models\User::whereDoesntHave('roles', function($query) use ($role) { $query->where('role_id', $role->id); })->get() as $availableUser)
                    <option value="{{ $availableUser->id }}">
                        {{ $availableUser->name }} ({{ $availableUser->email }})
                    </option>
                    @endforeach
                </select>
            </div>
            
            <div class="modal-action">
                <flux:button type="button" variant="ghost" onclick="document.getElementById('assign-user-modal').close()">
                    Cancel
                </flux:button>
                <flux:button type="submit" variant="primary">
                    Assign Role
                </flux:button>
            </div>
        </form>
    </dialog>
    @endcan
</x-layouts.app> 