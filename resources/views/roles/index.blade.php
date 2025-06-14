<x-layouts.app :title="__('Role Management')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('Role Management') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Manage user roles and permissions') }}
                        </p>
                    </div>
                    <flux:button variant="primary" icon="plus">
                        {{ __('Add Role') }}
                    </flux:button>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <!-- Roles Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                @foreach([
                    [
                        'name' => 'Admin',
                        'description' => 'Full system access and management',
                        'users' => 3,
                        'color' => 'red',
                        'permissions' => ['manage_users', 'manage_settings', 'view_reports', 'all_access']
                    ],
                    [
                        'name' => 'Manager',
                        'description' => 'Branch and operational management',
                        'users' => 8,
                        'color' => 'blue',
                        'permissions' => ['manage_staff', 'view_reports', 'approve_loans', 'manage_branches']
                    ],
                    [
                        'name' => 'Staff',
                        'description' => 'Day-to-day operations and member service',
                        'users' => 34,
                        'color' => 'emerald',
                        'permissions' => ['process_payments', 'view_members', 'approve_small_loans']
                    ],
                    [
                        'name' => 'Member',
                        'description' => 'Standard member access',
                        'users' => 2847,
                        'color' => 'purple',
                        'permissions' => ['view_own_account', 'make_payments', 'apply_for_loans']
                    ]
                ] as $role)
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-{{ $role['color'] }}-100 dark:bg-{{ $role['color'] }}-900/30 rounded-lg">
                            <flux:icon.user-group class="w-6 h-6 text-{{ $role['color'] }}-600 dark:text-{{ $role['color'] }}-400" />
                        </div>
                        <span class="text-sm text-{{ $role['color'] }}-600 dark:text-{{ $role['color'] }}-400 font-medium">{{ $role['users'] }} users</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-zinc-900 dark:text-zinc-100 mb-2">{{ $role['name'] }}</h3>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-4">{{ $role['description'] }}</p>
                        <div class="space-y-1">
                            @foreach(array_slice($role['permissions'], 0, 3) as $permission)
                            <div class="flex items-center text-xs text-zinc-500 dark:text-zinc-500">
                                <flux:icon.check class="w-3 h-3 mr-2" />
                                {{ str_replace('_', ' ', ucfirst($permission)) }}
                            </div>
                            @endforeach
                            @if(count($role['permissions']) > 3)
                            <div class="text-xs text-zinc-400 dark:text-zinc-500">
                                +{{ count($role['permissions']) - 3 }} more permissions
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                        <flux:button variant="outline" size="sm" class="w-full">
                            {{ __('Manage Role') }}
                        </flux:button>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- User Assignment -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ __('User Role Assignments') }}
                        </h3>
                        <div class="flex items-center space-x-2">
                            <flux:button variant="ghost" size="sm" icon="funnel">
                                {{ __('Filter') }}
                            </flux:button>
                        </div>
                    </div>
                </div>

                <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach([
                        ['name' => 'John Admin', 'email' => 'admin@saccocore.co.ke', 'role' => 'Admin', 'last_login' => '2024-12-15 10:30'],
                        ['name' => 'Sarah Manager', 'email' => 'sarah@saccocore.co.ke', 'role' => 'Manager', 'last_login' => '2024-12-15 09:15'],
                        ['name' => 'David Staff', 'email' => 'david@saccocore.co.ke', 'role' => 'Staff', 'last_login' => '2024-12-15 11:20'],
                        ['name' => 'Mary Member', 'email' => 'mary@example.com', 'role' => 'Member', 'last_login' => '2024-12-14 16:45']
                    ] as $user)
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                                    <span class="text-sm font-bold text-white">
                                        {{ collect(explode(' ', $user['name']))->map(fn($n) => substr($n, 0, 1))->implode('') }}
                                    </span>
                                </div>
                                <div>
                                    <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $user['name'] }}</p>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $user['email'] }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div class="text-right">
                                    <span class="px-3 py-1 text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 rounded-full">
                                        {{ $user['role'] }}
                                    </span>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-500 mt-1">
                                        Last login: {{ \Carbon\Carbon::parse($user['last_login'])->format('M d, g:i A') }}
                                    </p>
                                </div>
                                <flux:button variant="outline" size="sm">
                                    {{ __('Change Role') }}
                                </flux:button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-layouts.app> 