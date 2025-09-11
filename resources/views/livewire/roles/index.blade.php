<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public $search = '';
    public $roleFilter = '';
    public $stats = [];
    public $roles = [];
    public $users = [];

    public function mount()
    {
        $this->loadRolesData();
    }

    public function loadRolesData()
    {
        // Mock data for demonstration - in real app, this would come from the controller
        $this->stats = [
            'total_users' => 1250,
            'total_roles' => 4,
            'admin_count' => 3,
            'member_count' => 1200
        ];

        $this->roles = [
            (object)[
                'id' => 1,
                'name' => 'Administrator',
                'slug' => 'admin',
                'description' => 'Full system access and management capabilities',
                'users_count' => 3,
                'permissions' => ['view-members', 'create-members', 'edit-members', 'delete-members', 'view-accounts', 'create-accounts', 'edit-accounts', 'delete-accounts', 'process-transactions', 'view-loans', 'create-loans', 'edit-loans', 'delete-loans', 'approve-loans', 'disburse-loans', 'view-branches', 'manage-branches', 'view-reports', 'export-reports', 'manage-settings', 'manage-roles']
            ],
            (object)[
                'id' => 2,
                'name' => 'Manager',
                'slug' => 'manager',
                'description' => 'Branch and team management with limited admin access',
                'users_count' => 8,
                'permissions' => ['view-members', 'create-members', 'edit-members', 'view-accounts', 'create-accounts', 'edit-accounts', 'process-transactions', 'view-loans', 'create-loans', 'edit-loans', 'approve-loans', 'view-branches', 'view-reports', 'export-reports']
            ],
            (object)[
                'id' => 3,
                'name' => 'Staff',
                'slug' => 'staff',
                'description' => 'Day-to-day operations and member service',
                'users_count' => 45,
                'permissions' => ['view-members', 'create-members', 'edit-members', 'view-accounts', 'create-accounts', 'edit-accounts', 'process-transactions', 'view-loans', 'create-loans', 'view-reports']
            ],
            (object)[
                'id' => 4,
                'name' => 'Member',
                'slug' => 'member',
                'description' => 'Basic member access to personal accounts and services',
                'users_count' => 1200,
                'permissions' => ['view-members', 'view-accounts', 'view-loans']
            ]
        ];

        $this->users = [
            (object)[
                'id' => 1,
                'name' => 'John Mwangi',
                'email' => 'john@example.com',
                'email_verified_at' => now(),
                'roles' => collect([(object)['id' => 1, 'name' => 'Administrator', 'slug' => 'admin']])
            ],
            (object)[
                'id' => 2,
                'name' => 'Sarah Hassan',
                'email' => 'sarah@example.com',
                'email_verified_at' => now(),
                'roles' => collect([(object)['id' => 2, 'name' => 'Manager', 'slug' => 'manager']])
            ],
            (object)[
                'id' => 3,
                'name' => 'Peter Ochieng',
                'email' => 'peter@example.com',
                'email_verified_at' => null,
                'roles' => collect([(object)['id' => 4, 'name' => 'Member', 'slug' => 'member']])
            ]
        ];
    }

    public function filter()
    {
        // In a real app, this would filter the users based on search and role filter
        $this->loadRolesData();
    }
}

?><div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" class="!text-zinc-900 dark:!text-zinc-100">Role Management</flux:heading>
            <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">Manage user roles and permissions</flux:subheading>
        </div>
        <flux:button variant="primary" icon="plus" :href="route('roles.create')">
            Add Role
        </flux:button>
    </div>

    <!-- Statistics Dashboard -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <flux:icon.users class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <flux:badge color="blue">Users</flux:badge>
            </div>
            <flux:heading size="base" class="dark:text-zinc-100 mb-1">Total Users</flux:heading>
            <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($stats['total_users']) }}</div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                    <flux:icon.shield-check class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                </div>
                <flux:badge color="purple">Roles</flux:badge>
            </div>
            <flux:heading size="base" class="dark:text-zinc-100 mb-1">Active Roles</flux:heading>
            <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['total_roles'] }}</div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded-lg">
                    <flux:icon.key class="w-6 h-6 text-red-600 dark:text-red-400" />
                </div>
                <flux:badge color="red">Admins</flux:badge>
            </div>
            <flux:heading size="base" class="dark:text-zinc-100 mb-1">Administrators</flux:heading>
            <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['admin_count'] }}</div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                    <flux:icon.user-group class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                </div>
                <flux:badge color="emerald">Members</flux:badge>
            </div>
            <flux:heading size="base" class="dark:text-zinc-100 mb-1">Members</flux:heading>
            <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($stats['member_count']) }}</div>
        </div>
    </div>

    <!-- Roles Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($roles as $role)
        @php
            $colors = [
                'admin' => 'red',
                'manager' => 'blue', 
                'staff' => 'emerald',
                'member' => 'purple'
            ];
            $color = $colors[$role->slug] ?? 'gray';
        @endphp
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-{{ $color }}-100 dark:bg-{{ $color }}-900/30 rounded-lg">
                    <flux:icon.user-group class="w-6 h-6 text-{{ $color }}-600 dark:text-{{ $color }}-400" />
                </div>
                <flux:badge color="{{ $color }}">{{ $role->users_count }} users</flux:badge>
            </div>
            <flux:heading size="base" class="dark:text-zinc-100 mb-2">{{ $role->name }}</flux:heading>
            <flux:subheading class="dark:text-zinc-400 mb-4">{{ $role->description ?? 'No description available.' }}</flux:subheading>
            <div class="space-y-1 mb-4">
                @foreach(array_slice($role->permissions ?? [], 0, 3) as $permission)
                <div class="flex items-center text-xs text-zinc-500 dark:text-zinc-500">
                    <flux:icon.check class="w-3 h-3 mr-2" />
                    {{ ucwords(str_replace('-', ' ', $permission)) }}
                </div>
                @endforeach
                @if(count($role->permissions ?? []) > 3)
                <div class="text-xs text-zinc-400 dark:text-zinc-500">
                    +{{ count($role->permissions ?? []) - 3 }} more permissions
                </div>
                @endif
            </div>
            <div class="flex space-x-2">
                <flux:button variant="outline" size="sm" :href="route('roles.show', $role->id)" class="flex-1">
                    View Details
                </flux:button>
                <flux:button variant="ghost" size="sm" :href="route('roles.edit', $role->id)">
                    <flux:icon.pencil class="h-4 w-4" />
                </flux:button>
            </div>
        </div>
        @endforeach
    </div>

    <!-- User Assignment -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                        <flux:icon.users class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <flux:heading size="base" class="dark:text-zinc-100">User Role Assignments</flux:heading>
                        <flux:subheading class="dark:text-zinc-400">Manage user roles and permissions</flux:subheading>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <form wire:submit="filter" class="flex items-center space-x-2">
                        <flux:input 
                            type="search" 
                            wire:model="search"
                            placeholder="Search users..." 
                            size="sm"
                        />
                        <flux:select wire:model="roleFilter" size="sm">
                            <option value="">All Roles</option>
                            @foreach($roles as $filterRole)
                            <option value="{{ $filterRole->slug }}">{{ $filterRole->name }}</option>
                            @endforeach
                        </flux:select>
                        <flux:button type="submit" variant="ghost" size="sm" icon="funnel">
                            Filter
                        </flux:button>
                    </form>
                </div>
            </div>
        </div>

        <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
            @forelse($users as $user)
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                            <span class="text-sm font-bold text-white">
                                {{ substr($user->name, 0, 2) }}
                            </span>
                        </div>
                        <div>
                            <flux:heading size="base" class="dark:text-zinc-100">{{ $user->name }}</flux:heading>
                            <flux:subheading class="dark:text-zinc-400">{{ $user->email }}</flux:subheading>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="text-right">
                            <div class="flex flex-wrap gap-1 justify-end mb-1">
                                @forelse($user->roles as $userRole)
                                @php
                                    $colors = [
                                        'admin' => 'red',
                                        'manager' => 'blue', 
                                        'staff' => 'emerald',
                                        'member' => 'purple'
                                    ];
                                    $roleColor = $colors[$userRole->slug] ?? 'gray';
                                @endphp
                                <flux:badge color="{{ $roleColor }}">
                                    {{ $userRole->name }}
                                </flux:badge>
                                @empty
                                <flux:badge color="gray">No Roles</flux:badge>
                                @endforelse
                            </div>
                            @if($user->email_verified_at)
                            <div class="text-xs text-zinc-500 dark:text-zinc-500">
                                Verified member
                            </div>
                            @else
                            <div class="text-xs text-amber-600 dark:text-amber-400">
                                Unverified
                            </div>
                            @endif
                        </div>
                        <div class="flex space-x-2">
                            <flux:dropdown>
                                <flux:button variant="outline" size="sm">
                                    Assign Role
                                </flux:button>
                                <flux:menu>
                                    @foreach($roles as $assignRole)
                                    @if(!$user->roles->contains('id', $assignRole->id))
                                    <form method="POST" action="{{ route('roles.assign') }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                                        <input type="hidden" name="role_id" value="{{ $assignRole->id }}">
                                        <flux:menu.item type="submit">
                                            {{ $assignRole->name }}
                                        </flux:menu.item>
                                    </form>
                                    @endif
                                    @endforeach
                                </flux:menu>
                            </flux:dropdown>

                            @if($user->roles->count() > 0)
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" class="text-red-600 dark:text-red-400">
                                    Remove
                                </flux:button>
                                <flux:menu>
                                    @foreach($user->roles as $userRole)
                                    <form method="POST" action="{{ route('roles.remove') }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                                        <input type="hidden" name="role_id" value="{{ $userRole->id }}">
                                        <flux:menu.item type="submit" class="text-red-600 dark:text-red-400">
                                            Remove {{ $userRole->name }}
                                        </flux:menu.item>
                                    </form>
                                    @endforeach
                                </flux:menu>
                            </flux:dropdown>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-12 text-center">
                <flux:icon.users class="mx-auto h-12 w-12 text-zinc-400 dark:text-zinc-600 mb-4" />
                <flux:heading size="base" class="dark:text-zinc-100 mb-2">No users found</flux:heading>
                <flux:subheading class="dark:text-zinc-400">
                    {{ $search ? 'Try adjusting your search or filter criteria.' : 'Get started by creating your first user role assignment.' }}
                </flux:subheading>
            </div>
            @endforelse
        </div>
    </div>
</div>
