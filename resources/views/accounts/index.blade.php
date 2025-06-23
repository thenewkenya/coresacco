<x-layouts.app>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Account Management</h1>
                <p class="text-gray-600 dark:text-gray-400">Manage member accounts and view account details</p>
            </div>
            <flux:button href="{{ route('accounts.create') }}" icon="plus" variant="primary">
                Open New Account
            </flux:button>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <flux:field>
                        <flux:label>Search</flux:label>
                        <flux:input 
                            name="search" 
                            value="{{ request('search') }}" 
                            placeholder="Account number, member name, or email"
                            icon="magnifying-glass"
                        />
                    </flux:field>
                </div>
                
                <div>
                    <flux:field>
                        <flux:label>Account Type</flux:label>
                        <flux:select name="account_type">
                            <option value="">All Types</option>
                            @foreach($accountTypes as $type)
                                <option 
                                    value="{{ $type['value'] }}" 
                                    {{ request('account_type') === $type['value'] ? 'selected' : '' }}
                                >
                                    {{ $type['label'] }}
                                </option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                </div>
                
                <div>
                    <flux:field>
                        <flux:label>Status</flux:label>
                        <flux:select name="status">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="dormant" {{ request('status') === 'dormant' ? 'selected' : '' }}>Dormant</option>
                            <option value="frozen" {{ request('status') === 'frozen' ? 'selected' : '' }}>Frozen</option>
                            <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                        </flux:select>
                    </flux:field>
                </div>
                
                <div class="flex items-end space-x-2">
                    <flux:button type="submit" variant="primary" class="flex-1">
                        Filter
                    </flux:button>
                    <flux:button 
                        href="{{ route('accounts.index') }}" 
                        variant="ghost" 
                        icon="x-mark"
                        class="px-3"
                    >
                    </flux:button>
                </div>
            </form>
        </div>

        <!-- Accounts Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            @if($accounts->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Account Details
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Member
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Balance
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Created
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                            @foreach($accounts as $account)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                @php
                                                    $accountColors = [
                                                        'savings' => 'emerald', 'shares' => 'blue', 'deposits' => 'purple',
                                                        'emergency_fund' => 'red', 'holiday_savings' => 'yellow', 'retirement' => 'indigo',
                                                        'education' => 'cyan', 'development' => 'orange', 'welfare' => 'pink',
                                                        'loan_guarantee' => 'gray', 'insurance' => 'teal', 'investment' => 'amber'
                                                    ];
                                                    $accountIcons = [
                                                        'savings' => 'banknotes', 'shares' => 'building-library', 'deposits' => 'safe',
                                                        'emergency_fund' => 'shield-check', 'holiday_savings' => 'sun', 'retirement' => 'home',
                                                        'education' => 'academic-cap', 'development' => 'building-office-2', 'welfare' => 'heart',
                                                        'loan_guarantee' => 'shield-exclamation', 'insurance' => 'shield-check', 'investment' => 'chart-bar'
                                                    ];
                                                    $color = $accountColors[$account->account_type] ?? 'gray';
                                                    $icon = $accountIcons[$account->account_type] ?? 'banknotes';
                                                @endphp
                                                <div class="h-10 w-10 rounded-lg bg-{{ $color }}-100 dark:bg-{{ $color }}-900 flex items-center justify-center">
                                                    <flux:icon.{{ $icon }} class="h-5 w-5 text-{{ $color }}-600 dark:text-{{ $color }}-400" />
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $account->account_number }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $account->getDisplayName() }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $account->member->name }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $account->member->email }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            KES {{ number_format($account->balance, 2) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $statusColors = [
                                                'active' => 'green',
                                                'dormant' => 'yellow', 
                                                'frozen' => 'red',
                                                'closed' => 'gray'
                                            ];
                                            $color = $statusColors[$account->status] ?? 'gray';
                                        @endphp
                                        <flux:badge color="{{ $color }}" size="sm">
                                            {{ ucfirst($account->status) }}
                                        </flux:badge>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $account->created_at->format('M j, Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-medium">
                                        <flux:dropdown>
                                            <flux:button icon="ellipsis-horizontal" variant="ghost" size="sm" />
                                            <flux:menu>
                                                <flux:menu.item icon="eye" href="{{ route('accounts.show', $account) }}">
                                                    View Details
                                                </flux:menu.item>
                                                <flux:menu.item icon="currency-dollar" href="{{ route('transactions.deposit.create') }}">
                                                    New Transaction
                                                </flux:menu.item>
                                                @if($account->status === 'active')
                                                    <flux:menu.separator />
                                                    <flux:menu.item 
                                                        icon="pause" 
                                                        href="#"
                                                        x-data
                                                        @click="$refs.statusModal{{ $account->id }}.showModal()"
                                                    >
                                                        Change Status
                                                    </flux:menu.item>
                                                @endif
                                            </flux:menu>
                                        </flux:dropdown>

                                        <!-- Status Change Modal -->
                                        <dialog x-ref="statusModal{{ $account->id }}" class="modal">
                                            <div class="modal-box">
                                                <h3 class="font-bold text-lg">Change Account Status</h3>
                                                <p class="py-4">Change status for account {{ $account->account_number }}</p>
                                                
                                                <form method="POST" action="{{ route('accounts.update-status', $account) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    
                                                    <div class="space-y-4">
                                                        <flux:field>
                                                            <flux:label>New Status</flux:label>
                                                                                                        <flux:select name="status" required>
                                                <option value="active" {{ $account->status === 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="dormant" {{ $account->status === 'dormant' ? 'selected' : '' }}>Dormant</option>
                                                <option value="frozen" {{ $account->status === 'frozen' ? 'selected' : '' }}>Frozen</option>
                                                <option value="closed" {{ $account->status === 'closed' ? 'selected' : '' }}>Closed</option>
                                            </flux:select>
                                                        </flux:field>
                                                        
                                                        <flux:field>
                                                            <flux:label>Reason</flux:label>
                                                            <flux:textarea name="reason" placeholder="Reason for status change..." />
                                                        </flux:field>
                                                    </div>
                                                    
                                                    <div class="modal-action">
                                                        <button type="button" class="btn btn-ghost" onclick="document.getElementById('statusModal{{ $account->id }}').close()">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Update Status</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </dialog>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-3 border-t border-gray-200 dark:border-gray-600">
                    {{ $accounts->links() }}
                </div>
            @else
                <div class="p-12 text-center">
                    <flux:icon.folder-open class="mx-auto h-12 w-12 text-gray-400" />
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No accounts found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        @if(request()->hasAny(['search', 'account_type', 'status']))
                            Try adjusting your search criteria.
                        @else
                            Get started by opening a new account.
                        @endif
                    </p>
                    @if(!request()->hasAny(['search', 'account_type', 'status']))
                        <div class="mt-6">
                            <flux:button href="{{ route('accounts.create') }}" variant="primary">
                                Open New Account
                            </flux:button>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-layouts.app> 