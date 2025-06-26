<x-layouts.app :title="$branch->name . ' - Staff Management'">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <flux:button variant="ghost" size="sm" icon="arrow-left" :href="route('branches.show', $branch)" wire:navigate>
                            {{ __('Back to Branch') }}
                        </flux:button>
                        <div>
                            <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                                {{ __('Staff Management') }}
                            </h1>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $branch->name }} • {{ __('Manage branch staff assignments') }}
                            </p>
                        </div>
                    </div>
                    @if($availableStaff->count() > 0)
                        <flux:button variant="primary" icon="plus" onclick="document.getElementById('assignModal').showModal()">
                            {{ __('Assign Staff') }}
                        </flux:button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Current Staff -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ __('Current Staff') }} ({{ $staff->count() }})
                            </h3>
                        </div>

                        @if($staff->isEmpty())
                            <div class="p-12 text-center">
                                <flux:icon.users class="w-12 h-12 text-zinc-400 dark:text-zinc-600 mx-auto mb-4" />
                                <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">{{ __('No staff assigned') }}</h3>
                                <p class="text-zinc-600 dark:text-zinc-400 mb-4">{{ __('This branch has no staff members assigned yet.') }}</p>
                                @if($availableStaff->count() > 0)
                                    <flux:button variant="primary" onclick="document.getElementById('assignModal').showModal()">
                                        {{ __('Assign First Staff Member') }}
                                    </flux:button>
                                @endif
                            </div>
                        @else
                            <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                @foreach($staff as $member)
                                    <div class="p-6">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-4">
                                                <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white font-medium">
                                                    {{ $member->initials() }}
                                                </div>
                                                <div>
                                                    <div class="flex items-center space-x-3">
                                                        <h4 class="font-semibold text-zinc-900 dark:text-zinc-100">
                                                            {{ $member->name }}
                                                        </h4>
                                                        @if($member->id === $branch->manager_id)
                                                            <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 rounded-full">
                                                                {{ __('Manager') }}
                                                            </span>
                                                        @endif
                                                        <span class="px-2 py-1 text-xs font-medium bg-zinc-100 text-zinc-800 dark:bg-zinc-900/30 dark:text-zinc-400 rounded-full">
                                                            {{ ucfirst($member->role) }}
                                                        </span>
                                                    </div>
                                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                                        {{ $member->email }}
                                                    </p>
                                                    @if($member->phone_number)
                                                        <p class="text-xs text-zinc-500 dark:text-zinc-500">
                                                            {{ $member->phone_number }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                @if($member->id !== $branch->manager_id)
                                                    <form action="{{ route('branches.staff.remove', $branch) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="user_id" value="{{ $member->id }}">
                                                        <flux:button 
                                                            type="submit" 
                                                            variant="outline" 
                                                            size="sm"
                                                            onclick="return confirm('{{ __('Are you sure you want to remove this staff member from the branch?') }}')"
                                                        >
                                                            {{ __('Remove') }}
                                                        </flux:button>
                                                    </form>
                                                @else
                                                    <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Branch Manager') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Available Staff -->
                <div class="space-y-6">
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ __('Available Staff') }}
                            </h3>
                        </div>
                        <div class="p-6">
                            @if($availableStaff->isEmpty())
                                <div class="text-center py-4">
                                    <flux:icon.exclamation-triangle class="w-8 h-8 text-zinc-400 dark:text-zinc-600 mx-auto mb-2" />
                                    <p class="text-zinc-500 dark:text-zinc-400 text-sm">{{ __('No available staff to assign') }}</p>
                                </div>
                            @else
                                <div class="space-y-3">
                                    @foreach($availableStaff->take(3) as $member)
                                        <div class="flex items-center space-x-3 p-3 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                                            <div class="w-8 h-8 bg-emerald-500 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                                {{ $member->initials() }}
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100 truncate">{{ $member->name }}</p>
                                                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ ucfirst($member->role) }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                    
                                    @if($availableStaff->count() > 3)
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400 text-center">
                                            {{ __('And :count more...', ['count' => $availableStaff->count() - 3]) }}
                                        </p>
                                    @endif
                                </div>
                                
                                <flux:button variant="outline" size="sm" class="w-full mt-4" onclick="document.getElementById('assignModal').showModal()">
                                    {{ __('Assign Staff Member') }}
                                </flux:button>
                            @endif
                        </div>
                    </div>

                    <!-- Branch Info -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Branch Info') }}</h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-zinc-600 dark:text-zinc-400">{{ __('Total Staff:') }}</span>
                                <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $staff->count() }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-zinc-600 dark:text-zinc-400">{{ __('Manager:') }}</span>
                                <span class="font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $branch->manager ? $branch->manager->name : __('Unassigned') }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-zinc-600 dark:text-zinc-400">{{ __('Status:') }}</span>
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                    @if($branch->status === 'active') bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $branch->status)) }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                            <flux:button variant="outline" size="sm" class="w-full" :href="route('branches.show', $branch)" wire:navigate>
                                {{ __('View Branch Details') }}
                            </flux:button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assign Staff Modal -->
    @if($availableStaff->count() > 0)
        <flux:modal name="assignModal" class="w-full max-w-lg">
            <form action="{{ route('branches.staff.assign', $branch) }}" method="POST">
                @csrf
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                        {{ __('Assign Staff to Branch') }}
                    </h3>
                    
                    <flux:field>
                        <flux:label>{{ __('Select Staff Member') }}</flux:label>
                        <flux:select name="user_id" required>
                            <option value="">{{ __('Choose staff member...') }}</option>
                            @foreach($availableStaff as $member)
                                <option value="{{ $member->id }}">
                                    {{ $member->name }} ({{ ucfirst($member->role) }}) - {{ $member->email }}
                                </option>
                            @endforeach
                        </flux:select>
                        <flux:error name="user_id" />
                        <flux:description>{{ __('Select from available staff members who are not currently assigned to any branch') }}</flux:description>
                    </flux:field>
                </div>

                <div class="flex justify-end space-x-3 p-6 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:button type="button" variant="outline" onclick="document.getElementById('assignModal').close()">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ __('Assign Staff') }}
                    </flux:button>
                </div>
            </form>
        </flux:modal>
    @endif
</x-layouts.app> 