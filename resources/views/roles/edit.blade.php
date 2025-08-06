<x-layouts.app :title="'Edit ' . $role->name">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('Edit Role: :name', ['name' => $role->name]) }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Modify role permissions and settings') }}
                        </p>
                    </div>
                    <flux:button variant="ghost" :href="route('roles.show', $role)" wire:navigate>
                        {{ __('Back to Role') }}
                    </flux:button>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <div class="max-w-4xl mx-auto">
                <form method="POST" action="{{ route('roles.update', $role) }}">
                    @csrf
                    @method('PUT')
                    
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
                                            value="{{ old('name', $role->name) }}" 
                                            placeholder="e.g., Branch Manager, Senior Staff"
                                            required 
                                            {{ in_array($role->slug, ['admin', 'member', 'staff', 'manager']) ? 'readonly' : '' }}
                                        />
                                        @if(in_array($role->slug, ['admin', 'member', 'staff', 'manager']))
                                        <p class="mt-1 text-xs text-amber-600 dark:text-amber-400">
                                            {{ __('System role names cannot be changed.') }}
                                        </p>
                                        @endif
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
                                        >{{ old('description', $role->description) }}</flux:textarea>
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
                                
                                <!-- Hidden input to ensure permissions array is always sent -->
                                <input type="hidden" name="permissions_submitted" value="1">

                                @php
                                    $permissionGroups = [
                                        'Member Management' => ['view-members', 'create-members', 'edit-members', 'delete-members'],
                                        'Account Management' => ['view-accounts', 'create-accounts', 'edit-accounts', 'delete-accounts', 'process-transactions'],
                                        'Loan Management' => ['view-loans', 'create-loans', 'edit-loans', 'delete-loans', 'approve-loans', 'disburse-loans'],
                                        'Branch Management' => ['view-branches', 'manage-branches'],
                                        'Reports & Settings' => ['view-reports', 'export-reports', 'manage-settings', 'manage-roles']
                                    ];
                                    $currentPermissions = old('permissions', $role->permissions ?? []);
                                @endphp

                                <div class="space-y-6">
                                    @foreach($permissionGroups as $groupName => $groupPermissions)
                                    <div>
                                        <div class="flex items-center justify-between mb-3">
                                            <h4 class="text-md font-medium text-zinc-900 dark:text-zinc-100">
                                                {{ $groupName }}
                                            </h4>
                                            <div class="flex space-x-2">
                                                <button type="button" onclick="selectGroupPermissions('{{ strtolower(str_replace(' ', '-', $groupName)) }}')" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
                                                    Select All
                                                </button>
                                                <button type="button" onclick="deselectGroupPermissions('{{ strtolower(str_replace(' ', '-', $groupName)) }}')" class="text-xs text-gray-600 dark:text-gray-400 hover:underline">
                                                    Clear All
                                                </button>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" data-group="{{ strtolower(str_replace(' ', '-', $groupName)) }}">
                                            @foreach($groupPermissions as $permission)
                                            @if(in_array($permission, $availablePermissions))
                                            <label class="flex items-center space-x-3 p-3 rounded-lg border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 cursor-pointer">
                                                <input 
                                                    type="checkbox"
                                                    name="permissions[]" 
                                                    value="{{ $permission }}"
                                                    {{ in_array($permission, $currentPermissions) ? 'checked' : '' }}
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
                                            {{ __('Current Users') }}
                                        </p>
                                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $role->users()->count() }}</p>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400">users assigned to this role</p>
                                    </div>

                                    <div>
                                        <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                            {{ __('Quick Actions') }}
                                        </p>
                                        <div class="space-y-2">
                                            <button type="button" onclick="selectAllPermissions()" class="w-full text-left px-3 py-2 text-sm bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/50">
                                                {{ __('Select All Permissions') }}
                                            </button>
                                            <button type="button" onclick="selectNoPermissions()" class="w-full text-left px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900/30 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-900/50">
                                                {{ __('Clear All Permissions') }}
                                            </button>
                                        </div>
                                    </div>

                                    <div>
                                        <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                            {{ __('Permission Count') }}
                                        </p>
                                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100" id="permission-count">{{ count($currentPermissions) }}</p>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400">of {{ count($availablePermissions) }} total permissions</p>
                                    </div>

                                    <div class="pt-4 border-t border-zinc-200 dark:border-zinc-700">
                                        <flux:button type="submit" variant="primary" class="w-full">
                                            {{ __('Update Role') }}
                                        </flux:button>
                                    </div>

                                    @if(!in_array($role->slug, ['admin', 'member', 'staff', 'manager']))
                                    <div class="pt-4 border-t border-zinc-200 dark:border-zinc-700">
                                        <h4 class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                                            {{ __('Danger Zone') }}
                                        </h4>
                                        @if($role->users()->count() === 0)
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
                                                Role cannot be deleted while users are assigned to it.
                                            </p>
                                        </div>
                                        @endif
                                    </div>
                                    @endif
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

        function selectGroupPermissions(groupName) {
            console.log('Select group clicked:', groupName);
            const group = document.querySelector(`[data-group="${groupName}"]`);
            if (group) {
                const checkboxes = group.querySelectorAll('input[name="permissions[]"]');
                console.log('Found group checkboxes:', checkboxes.length);
                checkboxes.forEach(checkbox => checkbox.checked = true);
                updatePermissionCount();
            } else {
                console.error('Group not found:', groupName);
            }
        }

        function deselectGroupPermissions(groupName) {
            console.log('Deselect group clicked:', groupName);
            const group = document.querySelector(`[data-group="${groupName}"]`);
            if (group) {
                const checkboxes = group.querySelectorAll('input[name="permissions[]"]');
                console.log('Found group checkboxes:', checkboxes.length);
                checkboxes.forEach(checkbox => checkbox.checked = false);
                updatePermissionCount();
            } else {
                console.error('Group not found:', groupName);
            }
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
 <!-- Quick Save via AJAX -->
    <script>
        // Add quick save functionality
        function quickSavePermissions() {
            const form = document.querySelector('form');
            const formData = new FormData(form);
            
            // Show loading state
            const submitBtn = document.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Saving...';
            submitBtn.disabled = true;
            
            fetch(`/roles/{{ $role->id }}/permissions`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    showNotification('Permissions updated successfully!', 'success');
                } else {
                    showNotification('Error updating permissions', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error updating permissions', 'error');
            })
            .finally(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        }
        
        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
                type === 'success' ? 'bg-emerald-500 text-white' : 'bg-red-500 text-white'
            }`;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
        
        // Add quick save button
        document.addEventListener('DOMContentLoaded', function() {
            const submitBtn = document.querySelector('button[type="submit"]');
            if (submitBtn) {
                const quickSaveBtn = document.createElement('button');
                quickSaveBtn.type = 'button';
                quickSaveBtn.className = 'w-full mb-2 px-4 py-2 text-sm bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/50';
                quickSaveBtn.textContent = 'Quick Save (AJAX)';
                quickSaveBtn.onclick = quickSavePermissions;
                
                submitBtn.parentNode.insertBefore(quickSaveBtn, submitBtn);
            }
        });
    </script>