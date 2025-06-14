<x-layouts.app :title="__('Transactions')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            @roleany('admin', 'manager', 'staff')
                                {{ __('Transaction Management') }}
                            @endroleany
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            @roleany('admin', 'manager', 'staff')
                                {{ __('Monitor and manage all member transactions') }}
                            @endroleany
                        </p>
                    </div>
                    <div class="flex items-center space-x-4">
                        @roleany('admin', 'manager', 'staff')
                        <flux:button variant="primary" icon="plus">
                            {{ __('New Transaction') }}
                        </flux:button>
                        @endroleany
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-8">
                <div class="text-center">
                    <flux:icon.arrows-right-left class="mx-auto h-12 w-12 text-zinc-400 mb-4" />
                    <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">
                        {{ __('Transaction Management') }}
                    </h3>
                    <p class="text-zinc-600 dark:text-zinc-400 mb-6">
                        {{ __('This section will display and manage all SACCO transactions including deposits, withdrawals, transfers, and loan payments.') }}
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div class="p-4 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                            <h4 class="font-medium text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Features') }}</h4>
                            <ul class="text-zinc-600 dark:text-zinc-400 space-y-1">
                                <li>• {{ __('Transaction History') }}</li>
                                <li>• {{ __('Real-time Processing') }}</li>
                                <li>• {{ __('Automated Reconciliation') }}</li>
                            </ul>
                        </div>
                        <div class="p-4 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                            <h4 class="font-medium text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Transaction Types') }}</h4>
                            <ul class="text-zinc-600 dark:text-zinc-400 space-y-1">
                                <li>• {{ __('Deposits & Withdrawals') }}</li>
                                <li>• {{ __('Loan Payments') }}</li>
                                <li>• {{ __('Internal Transfers') }}</li>
                            </ul>
                        </div>
                        <div class="p-4 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                            <h4 class="font-medium text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Reporting') }}</h4>
                            <ul class="text-zinc-600 dark:text-zinc-400 space-y-1">
                                <li>• {{ __('Daily Summaries') }}</li>
                                <li>• {{ __('Audit Trails') }}</li>
                                <li>• {{ __('Export Options') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app> 