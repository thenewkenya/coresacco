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
                        <flux:button variant="ghost" size="sm" :href="route('roles.edit', $role)" wire:navigate>
                            <flux:icon.pencil class="h-4 w-4" />
                        </flux:button>
                        <flux:button variant="ghost" size="sm" onclick="showPermissionsModal('{{ $role->id }}', '{{ $role->name }}', {{ json_encode($role->permissions ?? []) }})">
                            <flux:icon.eye class="h-4 w-4" />
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
                            <form method="GET" class="flex items-center space-x-2" id="search-form">
                                <flux:input 
                                    type="search" 
                                    name="search" 
                                    id="search-input"
                                    placeholder="Search users..." 
                                    value="{{ $search }}"
                                    size="sm"
                                />
                                <select name="role_filter" id="role-filter" class="rounded-md border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 text-sm">
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

                <div class="divide-y divide-zinc-200 dark:divide-zinc-700" id="user-list">
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
                <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700" id="pagination">
                    {{ $users->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let searchTimeout;
        
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-input');
            const roleFilter = document.getElementById('role-filter');
            const userList = document.getElementById('user-list');
            const paginationContainer = document.querySelector('.bg-white.dark\\:bg-zinc-800.rounded-xl.border');
            
            if (!searchInput || !roleFilter || !userList) {
                console.error('Live search elements not found');
                return;
            }
            
            console.log('Live search initialized');
            
            // Function to perform search
            function performSearch() {
                const searchTerm = searchInput.value;
                const roleFilterValue = roleFilter.value;
                
                console.log('Searching for:', searchTerm, 'with role filter:', roleFilterValue);
                
                // Show loading state
                userList.style.opacity = '0.6';
                userList.style.pointerEvents = 'none';
                
                // Create URL with parameters
                const url = new URL(window.location.href);
                url.searchParams.set('search', searchTerm);
                url.searchParams.set('role_filter', roleFilterValue);
                
                // Fetch new results
                fetch(url.toString(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    // Parse the response and extract the user list and pagination
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newUserList = doc.getElementById('user-list');
                    const newPagination = doc.getElementById('pagination');
                    
                    if (newUserList) {
                        userList.innerHTML = newUserList.innerHTML;
                        console.log('User list updated');
                        
                        // Update pagination if it exists
                        const currentPagination = document.getElementById('pagination');
                        if (newPagination && currentPagination) {
                            currentPagination.innerHTML = newPagination.innerHTML;
                            console.log('Pagination updated');
                        } else if (newPagination && !currentPagination) {
                            // Add pagination if it doesn't exist
                            const paginationDiv = document.createElement('div');
                            paginationDiv.id = 'pagination';
                            paginationDiv.className = 'px-6 py-4 border-t border-zinc-200 dark:border-zinc-700';
                            paginationDiv.innerHTML = newPagination.innerHTML;
                            userList.parentNode.appendChild(paginationDiv);
                        } else if (!newPagination && currentPagination) {
                            // Remove pagination if it's no longer needed
                            currentPagination.remove();
                        }
                    } else {
                        console.error('Could not find user list in response');
                    }
                    
                    // Remove loading state
                    userList.style.opacity = '1';
                    userList.style.pointerEvents = 'auto';
                    
                    // Update URL without reloading page
                    window.history.pushState({}, '', url.toString());
                })
                .catch(error => {
                    console.error('Search error:', error);
                    // Remove loading state on error
                    userList.style.opacity = '1';
                    userList.style.pointerEvents = 'auto';
                });
            }
            
            // Debounced search on input
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(performSearch, 300); // 300ms delay
            });
            
            // Immediate search on role filter change
            roleFilter.addEventListener('change', function() {
                clearTimeout(searchTimeout);
                performSearch();
            });
            
            // Handle browser back/forward buttons
            window.addEventListener('popstate', function() {
                const urlParams = new URLSearchParams(window.location.search);
                searchInput.value = urlParams.get('search') || '';
                roleFilter.value = urlParams.get('role_filter') || '';
                performSearch();
            });
        });
    </script>
    @endpush

    <!-- Permissions Modal -->
    <div id="permissionsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 max-w-2xl w-full mx-4 max-h-[80vh] overflow-hidden">
            <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100" id="modalRoleName">
                        Role Permissions
                    </h3>
                    <button onclick="closePermissionsModal()" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300">
                        <flux:icon.x-mark class="h-6 w-6" />
                    </button>
                </div>
            </div>
            <div class="p-6 overflow-y-auto max-h-96">
                <div id="modalPermissionsList" class="space-y-3">
                    <!-- Permissions will be populated here -->
                </div>
            </div>
            <div class="p-6 border-t border-zinc-200 dark:border-zinc-700 flex justify-end space-x-3">
                <flux:button variant="ghost" onclick="closePermissionsModal()">
                    {{ __('Close') }}
                </flux:button>
                <flux:button variant="primary" onclick="editRoleFromModal()">
                    {{ __('Edit Permissions') }}
                </flux:button>
            </div>
        </div>
    </div>

    <script>
        let currentRoleId = null;

        function showPermissionsModal(roleId, roleName, permissions) {
            currentRoleId = roleId;
            document.getElementById('modalRoleName').textContent = roleName + ' Permissions';
            
            const permissionsList = document.getElementById('modalPermissionsList');
            permissionsList.innerHTML = '';
            
            if (permissions.length === 0) {
                permissionsList.innerHTML = '<p class="text-zinc-500 dark:text-zinc-400 text-center py-4">No permissions assigned</p>';
            } else {
                const permissionGroups = {
                    'Member Management': ['view-members', 'create-members', 'edit-members', 'delete-members'],
                    'Account Management': ['view-accounts', 'create-accounts', 'edit-accounts', 'delete-accounts', 'process-transactions'],
                    'Loan Management': ['view-loans', 'create-loans', 'edit-loans', 'delete-loans', 'approve-loans', 'disburse-loans'],
                    'Branch Management': ['view-branches', 'manage-branches'],
                    'Reports & Settings': ['view-reports', 'export-reports', 'manage-settings', 'manage-roles']
                };
                
                Object.entries(permissionGroups).forEach(([groupName, groupPermissions]) => {
                    const hasPermissions = groupPermissions.some(p => permissions.includes(p));
                    if (hasPermissions) {
                        const groupDiv = document.createElement('div');
                        groupDiv.className = 'mb-4';
                        
                        const groupTitle = document.createElement('h4');
                        groupTitle.className = 'font-medium text-zinc-900 dark:text-zinc-100 mb-2';
                        groupTitle.textContent = groupName;
                        groupDiv.appendChild(groupTitle);
                        
                        const permissionsGrid = document.createElement('div');
                        permissionsGrid.className = 'grid grid-cols-1 sm:grid-cols-2 gap-2';
                        
                        groupPermissions.forEach(permission => {
                            if (permissions.includes(permission)) {
                                const permDiv = document.createElement('div');
                                permDiv.className = 'flex items-center space-x-2 text-sm text-zinc-600 dark:text-zinc-400';
                                permDiv.innerHTML = `
                                    <flux:icon.check class="h-4 w-4 text-emerald-500" />
                                    <span>${permission.replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}</span>
                                `;
                                permissionsGrid.appendChild(permDiv);
                            }
                        });
                        
                        groupDiv.appendChild(permissionsGrid);
                        permissionsList.appendChild(groupDiv);
                    }
                });
            }
            
            document.getElementById('permissionsModal').classList.remove('hidden');
        }

        function closePermissionsModal() {
            document.getElementById('permissionsModal').classList.add('hidden');
            currentRoleId = null;
        }

        function editRoleFromModal() {
            if (currentRoleId) {
                window.location.href = `/roles/${currentRoleId}/edit`;
            }
        }

        // Close modal when clicking outside
        document.getElementById('permissionsModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePermissionsModal();
            }
        });
    </script>
</x-layouts.app> 