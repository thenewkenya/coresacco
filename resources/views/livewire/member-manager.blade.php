<div>
    <div class="flex justify-between items-center mb-4">
        <div class="flex-1 max-w-sm">
            <x-form.input
                type="search"
                name="search"
                placeholder="Search members..."
                wire:model.live.debounce.300ms="search"
                :loading="$this->isLoading('fetch')"
            />
        </div>
        @if($canCreate)
            <button wire:click="openCreateModal" class="btn-primary">
                Add Member
            </button>
        @endif
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Address</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($members as $member)
                    <tr wire:key="{{ $member->id }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $member->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $member->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $member->phone_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $member->address }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            <div class="flex space-x-2">
                                @if($canEdit)
                                    <button 
                                        wire:click="editMember({{ $member->id }})" 
                                        class="text-blue-600 hover:text-blue-900 dark:hover:text-blue-400"
                                        :disabled="@js($this->isLoading('edit-load'))"
                                    >
                                        Edit
                                    </button>
                                @endif
                                @if($canDelete)
                                    <button 
                                        wire:click="deleteMember({{ $member->id }})" 
                                        class="text-red-600 hover:text-red-900 dark:hover:text-red-400"
                                        :disabled="@js($this->isLoading('delete'))"
                                    >
                                        Delete
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-300">
                            No members found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $members->links() }}
    </div>

    <!-- Create Modal -->
    <div x-data="{ show: false }" x-show="show" @open-create-modal.window="show = true" @close-create-modal.window="show = false">
        <x-modal>
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Create Member</h2>
                <form wire:submit="createMember">
                    <div class="space-y-4">
                        <x-form.input
                            name="name"
                            label="Name"
                            wire:model="name"
                            :error="$errors->first('name')"
                            required
                        />

                        <x-form.input
                            type="email"
                            name="email"
                            label="Email"
                            wire:model="email"
                            :error="$errors->first('email')"
                            required
                        />

                        <x-form.input
                            name="phone"
                            label="Phone"
                            wire:model="phone"
                            :error="$errors->first('phone')"
                            required
                        />

                        <x-form.input
                            name="address"
                            label="Address"
                            wire:model="address"
                            :error="$errors->first('address')"
                            required
                        />
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" class="btn-secondary" wire:click="$set('showCreateModal', false)">
                            Cancel
                        </button>
                        <button type="submit" class="btn-primary" :disabled="@js($this->isLoading('create'))">
                            <span wire:loading.remove wire:target="createMember">Create</span>
                            <span wire:loading wire:target="createMember">Creating...</span>
                        </button>
                    </div>
                </form>
            </div>
        </x-modal>
    </div>

    <!-- Edit Modal -->
    <div x-data="{ show: false }" x-show="show" @open-edit-modal.window="show = true" @close-edit-modal.window="show = false">
        <x-modal>
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Edit Member</h2>
                <form wire:submit="updateMember">
                    <div class="space-y-4">
                        <x-form.input
                            name="name"
                            label="Name"
                            wire:model="name"
                            :error="$errors->first('name')"
                            required
                        />

                        <x-form.input
                            type="email"
                            name="email"
                            label="Email"
                            wire:model="email"
                            :error="$errors->first('email')"
                            required
                        />

                        <x-form.input
                            name="phone"
                            label="Phone"
                            wire:model="phone"
                            :error="$errors->first('phone')"
                            required
                        />

                        <x-form.input
                            name="address"
                            label="Address"
                            wire:model="address"
                            :error="$errors->first('address')"
                            required
                        />
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" class="btn-secondary" wire:click="$set('showEditModal', false)">
                            Cancel
                        </button>
                        <button type="submit" class="btn-primary" :disabled="@js($this->isLoading('update'))">
                            <span wire:loading.remove wire:target="updateMember">Update</span>
                            <span wire:loading wire:target="updateMember">Updating...</span>
                        </button>
                    </div>
                </form>
            </div>
        </x-modal>
    </div>
</div> 