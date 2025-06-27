<x-layouts.app :title="__('Create Role')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('Create New Role') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Define a new role with specific permissions') }}
                        </p>
                    </div>
                    <flux:button variant="ghost" :href="route('roles.index')" wire:navigate>
                        {{ __('Back to Roles') }}
                    </flux:button>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <div class="max-w-4xl mx-auto">
                <form method="POST" action="{{ route('roles.store') }}">
                    @csrf
                    
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Role Information -->
                        <div class="lg:col-span-2">
                            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-6">
                                    {{ __('Role Information') }}
                                </h3>

                                <div class="space-y-6">
                                    <div>
                                        <flux:label for="name">{{ __('Role Name') }}</flux:label>
                                        <flux:input 
                                            id="name" 
                                            name="name" 
                                            type="text" 
                                            value="{{ old('name') }}" 
                                            placeholder="e.g., Branch Manager, Senior Staff"
                                            required 
                                        />
                                        @error('name')
                                        <flux:error>{{ $message }}</flux:error>
                                        @enderror
                                    </div>

                                    <div>
                                        <flux:label for="description">{{ __('Description') }}</flux:label>
                                        <flux:textarea 
                                            id="description" 
                                            name="description" 
                                            rows="3"
                                            placeholder="Describe what this role does and its responsibilities..."
                                        >{{ old('description') }}</flux:textarea>
                                        @error('description')
                                        <flux:error>{{ $message }}</flux:error>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Permissions -->
                            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 mt-6">
                                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-6">
                                    {{ __('Permissions') }}
                                </h3>

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
                                    <div>
                                        <h4 class="text-md font-medium text-zinc-900 dark:text-zinc-100 mb-3">
                                            {{ $groupName }}
                                        </h4>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            @foreach($groupPermissions as $permission)
                                            @if(in_array($permission, $availablePermissions))
                                            <label class="flex items-center space-x-3 p-3 rounded-lg border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 cursor-pointer">
                                                <input 
                                                    type="checkbox"
                                                    name="permissions[]" 
                                                    value="{{ $permission }}"
                                                    {{ in_array($permission, old('permissions', [])) ? 'checked' : '' }}
                                                    class="rounded border-zinc-300 dark:border-zinc-600 text-blue-600 focus:ring-blue-500 dark:focus:ring-blue-400"
                                                />
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
                                            </label>
                                            @endif
                                            @endforeach
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                @error('permissions')
                                <flux:error class="mt-4">{{ $message }}</flux:error>
                                @enderror
                            </div>
                        </div>

                        <!-- Summary Sidebar -->
                        <div class="lg:col-span-1">
                            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 sticky top-8">
                                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                                    {{ __('Role Summary') }}
                                </h3>

                                <div class="space-y-4">
                                    <div>
                                        <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                            {{ __('Quick Actions') }}
                                        </p>
                                        <div class="space-y-2">
                                            <flux:button 
                                                type="button" 
                                                onclick="selectAllPermissions()" 
                                                variant="outline"
                                                size="sm"
                                                class="w-full"
                                            >
                                                Select All
                                            </flux:button>
                                            <flux:button 
                                                type="button" 
                                                onclick="selectNoPermissions()" 
                                                variant="ghost"
                                                size="sm" 
                                                class="w-full"
                                            >
                                                Clear All
                                            </flux:button>
                                        </div>
                                    </div>

                                    <div>
                                        <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                            {{ __('Permission Count') }}
                                        </p>
                                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100" id="permission-count">0</p>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400">of {{ count($availablePermissions) }} total permissions</p>
                                    </div>

                                    <div class="pt-4 border-t border-zinc-200 dark:border-zinc-700">
                                        <flux:button type="submit" variant="primary" class="w-full">
                                            {{ __('Create Role') }}
                                        </flux:button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function selectAllPermissions() {
            console.log('Select All clicked');
            const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
            console.log('Found checkboxes:', checkboxes.length);
            checkboxes.forEach(checkbox => checkbox.checked = true);
            updatePermissionCount();
        }

        function selectNoPermissions() {
            console.log('Clear All clicked');
            const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
            console.log('Found checkboxes:', checkboxes.length);
            checkboxes.forEach(checkbox => checkbox.checked = false);
            updatePermissionCount();
        }

        function updatePermissionCount() {
            const checkedBoxes = document.querySelectorAll('input[name="permissions[]"]:checked');
            const countElement = document.getElementById('permission-count');
            if (countElement) {
                countElement.textContent = checkedBoxes.length;
                console.log('Updated count to:', checkedBoxes.length);
            } else {
                console.error('Permission count element not found');
            }
        }

        // Update count on page load and when checkboxes change
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing permission management');
            updatePermissionCount();
            
            const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
            console.log('Setting up event listeners for', checkboxes.length, 'checkboxes');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updatePermissionCount);
            });
        });
    </script>
    @endpush
</x-layouts.app> 