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
                    <!-- Debug Info -->
                    @if(config('app.debug'))
                    <div class="text-xs text-gray-500 mb-2">
                        Debug: User ID: {{ auth()->id() }} | 
                        Can Create: {{ auth()->user() && auth()->user()->can('create', \App\Models\Role::class) ? 'YES' : 'NO' }} |
                        Has Admin Role: {{ auth()->user() && auth()->user()->hasRole('admin') ? 'YES' : 'NO' }}
                    </div>
                    @endif
                    
                    <!-- Temporarily always show Add Role button for testing -->
                    <flux:button variant="primary" icon="plus" :href="route('roles.create')" wire:navigate>
                        {{ __('Add Role') }}
                    </flux:button>
                </div>
            </div>
        </div>

        <!-- Statistics Dashboard -->
        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <flux:icon.users class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($stats['total_users']) }}</p>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Total Users</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                            <flux:icon.shield-check class="h-6 w-6 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['total_roles'] }}</p>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Active Roles</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded-lg">
                            <flux:icon.key class="h-6 w-6 text-red-600 dark:text-red-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['admin_count'] }}</p>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Administrators</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                            <flux:icon.user-group class="h-6 w-6 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($stats['member_count']) }}</p>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Members</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <!-- Roles Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
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
                        <span class="text-sm text-{{ $color }}-600 dark:text-{{ $color }}-400 font-medium">{{ $role->users_count }} users</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-zinc-900 dark:text-zinc-100 mb-2">{{ $role->name }}</h3>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-4">{{ $role->description ?? 'No description available.' }}</p>
                        <div class="space-y-1">
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
                    </div>
                    <div class="mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-700 flex space-x-2">
                        <flux:button variant="outline" size="sm" :href="route('roles.show', $role)" wire:navigate class="flex-1">
                            {{ __('View Details') }}
                        </flux:button>
                        <!-- Temporarily always show Edit button for testing -->
                        <flux:button variant="ghost" size="sm" :href="route('roles.edit', $role)" wire:navigate>
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
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ __('User Role Assignments') }}
                        </h3>
                        <div class="flex items-center space-x-2">
                            <!-- Search and Filter -->
                            <form method="GET" class="flex items-center space-x-2">
                                <flux:input 
                                    type="search" 
                                    name="search" 
                                    placeholder="Search users..." 
                                    value="{{ $search }}"
                                    size="sm"
                                />
                                <select name="role_filter" class="rounded-md border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 text-sm">
                                    <option value="">All Roles</option>
                                    @foreach($roles as $filterRole)
                                    <option value="{{ $filterRole->slug }}" {{ $roleFilter === $filterRole->slug ? 'selected' : '' }}>
                                        {{ $filterRole->name }}
                                    </option>
                                    @endforeach
                                </select>
                                <flux:button type="submit" variant="ghost" size="sm" icon="funnel">
                                    {{ __('Filter') }}
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
                                        {{ $user->initials() }}
                                    </span>
                                </div>
                                <div>
                                    <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $user->name }}</p>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $user->email }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div class="text-right">
                                    <div class="flex flex-wrap gap-1 justify-end mb-1">
                                        @forelse($user->roles as $userRole)
                                        @php
                                            $roleColor = $colors[$userRole->slug] ?? 'gray';
                                        @endphp
                                        <span class="px-2 py-1 text-xs font-medium bg-{{ $roleColor }}-100 text-{{ $roleColor }}-800 dark:bg-{{ $roleColor }}-900/30 dark:text-{{ $roleColor }}-400 rounded-full">
                                            {{ $userRole->name }}
                                        </span>
                                        @empty
                                        <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400 rounded-full">
                                            No Roles
                                        </span>
                                        @endforelse
                                    </div>
                                    @if($user->email_verified_at)
                                    <p class="text-xs text-zinc-500 dark:text-zinc-500">
                                        Verified member
                                    </p>
                                    @else
                                    <p class="text-xs text-amber-600 dark:text-amber-400">
                                        Unverified
                                    </p>
                                    @endif
                                </div>
                                <!-- Temporarily always show user assignment dropdowns for testing -->
                                <div class="flex space-x-2">
                                    <!-- Quick Role Assignment Dropdown -->
                                    <flux:dropdown>
                                        <flux:button variant="outline" size="sm">
                                            {{ __('Assign Role') }}
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

                                    <!-- Remove Role Dropdown -->
                                    @if($user->roles->count() > 0)
                                    <flux:dropdown>
                                        <flux:button variant="ghost" size="sm" class="text-red-600 dark:text-red-400">
                                            {{ __('Remove') }}
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
                        <flux:icon.users class="mx-auto h-12 w-12 text-zinc-400 dark:text-zinc-600" />
                        <h3 class="mt-4 text-sm font-medium text-zinc-900 dark:text-zinc-100">No users found</h3>
                        <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                            {{ $search ? 'Try adjusting your search or filter criteria.' : 'Get started by creating your first user role assignment.' }}
                        </p>
                    </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($users->hasPages())
                <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
                    {{ $users->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app> 