<x-layouts.app :title="$member->name">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('members.index') }}" class="text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100">
                            <flux:icon.arrow-left class="w-5 h-5" />
                        </a>
                        <div class="flex items-center space-x-4">
                            <div class="h-12 w-12 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                                <span class="text-lg font-bold text-white">
                                    {{ substr($member->name, 0, 1) }}{{ substr(explode(' ', $member->name)[1] ?? '', 0, 1) }}
                                </span>
                            </div>
                            <div>
                                <h1 class="text-xl lg:text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $member->name }}</h1>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $member->member_number }} • {{ $member->email }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        @can('update', $member)
                            <a href="{{ route('members.edit', $member) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                <flux:icon.pencil class="w-4 h-4 inline mr-2" />
                                {{ __('Edit') }}
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <!-- Member Statistics -->
            <div class="mb-8">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
                    <div class="bg-white dark:bg-zinc-800 rounded-xl p-4 lg:p-6 border border-zinc-200 dark:border-zinc-700">
                        <div class="flex items-center">
                            <div class="p-2 lg:p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex-shrink-0">
                                <flux:icon.banknotes class="w-5 h-5 lg:w-6 lg:h-6 text-emerald-600 dark:text-emerald-400" />
                            </div>
                            <div class="ml-3 lg:ml-4 min-w-0 flex-1">
                                <p class="text-xs lg:text-sm text-zinc-600 dark:text-zinc-400 truncate">{{ __('Total Deposits') }}</p>
                                <p class="text-lg lg:text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                                    KES {{ number_format($stats['total_deposits'], 0) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-zinc-800 rounded-xl p-4 lg:p-6 border border-zinc-200 dark:border-zinc-700">
                        <div class="flex items-center">
                            <div class="p-2 lg:p-3 bg-red-100 dark:bg-red-900/30 rounded-lg flex-shrink-0">
                                <flux:icon.arrow-up class="w-5 h-5 lg:w-6 lg:h-6 text-red-600 dark:text-red-400" />
                            </div>
                            <div class="ml-3 lg:ml-4 min-w-0 flex-1">
                                <p class="text-xs lg:text-sm text-zinc-600 dark:text-zinc-400 truncate">{{ __('Total Withdrawals') }}</p>
                                <p class="text-lg lg:text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                                    KES {{ number_format($stats['total_withdrawals'], 0) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-zinc-800 rounded-xl p-4 lg:p-6 border border-zinc-200 dark:border-zinc-700">
                        <div class="flex items-center">
                            <div class="p-2 lg:p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex-shrink-0">
                                <flux:icon.credit-card class="w-5 h-5 lg:w-6 lg:h-6 text-blue-600 dark:text-blue-400" />
                            </div>
                            <div class="ml-3 lg:ml-4 min-w-0 flex-1">
                                <p class="text-xs lg:text-sm text-zinc-600 dark:text-zinc-400 truncate">{{ __('Accounts') }}</p>
                                <p class="text-lg lg:text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['total_accounts'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-zinc-800 rounded-xl p-4 lg:p-6 border border-zinc-200 dark:border-zinc-700">
                        <div class="flex items-center">
                            <div class="p-2 lg:p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex-shrink-0">
                                <flux:icon.document-text class="w-5 h-5 lg:w-6 lg:h-6 text-purple-600 dark:text-purple-400" />
                            </div>
                            <div class="ml-3 lg:ml-4 min-w-0 flex-1">
                                <p class="text-xs lg:text-sm text-zinc-600 dark:text-zinc-400 truncate">{{ __('Active Loans') }}</p>
                                <p class="text-lg lg:text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['active_loans'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Member Information -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                            <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Member Information') }}</h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Member Number') }}</label>
                                <p class="text-sm text-zinc-900 dark:text-zinc-100 font-mono">{{ $member->member_number }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Email') }}</label>
                                <p class="text-sm text-zinc-900 dark:text-zinc-100">{{ $member->email }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Phone') }}</label>
                                <p class="text-sm text-zinc-900 dark:text-zinc-100">{{ $member->phone_number ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('ID Number') }}</label>
                                <p class="text-sm text-zinc-900 dark:text-zinc-100">{{ $member->id_number ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Branch') }}</label>
                                <p class="text-sm text-zinc-900 dark:text-zinc-100">{{ $member->branch->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Status') }}</label>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $member->membership_status === 'active' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400' : '' }}
                                    {{ $member->membership_status === 'inactive' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : '' }}
                                    {{ $member->membership_status === 'suspended' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}">
                                    {{ ucfirst($member->membership_status) }}
                                </span>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Joined') }}</label>
                                <p class="text-sm text-zinc-900 dark:text-zinc-100">
                                    {{ $member->joining_date ? \Carbon\Carbon::parse($member->joining_date)->format('M d, Y') : 'N/A' }}
                                </p>
                            </div>
                            @if($member->address)
                                <div>
                                    <label class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Address') }}</label>
                                    <p class="text-sm text-zinc-900 dark:text-zinc-100">{{ $member->address }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Accounts and Recent Transactions -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Accounts -->
                    @if($member->accounts->count() > 0)
                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                            <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Accounts') }}</h2>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    @foreach($member->accounts as $account)
                                        <div class="flex items-center justify-between p-4 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                                            <div>
                                                <h3 class="font-medium text-zinc-900 dark:text-zinc-100">{{ $account->account_number }}</h3>
                                                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ ucfirst($account->account_type) }} Account</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-semibold text-zinc-900 dark:text-zinc-100">
                                                    KES {{ number_format($account->balance, 2) }}
                                                </p>
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                    {{ $account->status === 'active' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                                                    {{ ucfirst($account->status) }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Recent Transactions -->
                    @if($member->transactions->count() > 0)
                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                            <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Recent Transactions') }}</h2>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-zinc-50 dark:bg-zinc-700">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Date') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Type') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Description') }}</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Amount') }}</th>
                                            <th class="px-6 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                                        @foreach($member->transactions as $transaction)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-100">
                                                    {{ $transaction->created_at->format('M d, Y') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                        {{ $transaction->type === 'deposit' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400' : '' }}
                                                        {{ $transaction->type === 'withdrawal' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : '' }}
                                                        {{ $transaction->type === 'transfer' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : '' }}">
                                                        {{ ucfirst($transaction->type) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-100">
                                                    {{ $transaction->description }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                                    KES {{ number_format($transaction->amount, 2) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                        {{ $transaction->status === 'completed' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400' : '' }}
                                                        {{ $transaction->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                                                        {{ $transaction->status === 'failed' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : '' }}">
                                                        {{ ucfirst($transaction->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app> 