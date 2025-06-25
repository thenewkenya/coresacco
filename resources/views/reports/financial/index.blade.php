<x-layouts.app :title="__('Financial Reports')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('Financial Reports') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Comprehensive financial statements and analysis') }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <flux:button variant="outline" icon="arrow-down-tray" :href="route('reports.financial', array_merge(request()->all(), ['format' => 'pdf']))" wire:navigate>
                            {{ __('Export PDF') }}
                        </flux:button>
                        <flux:button variant="outline" icon="document-chart-bar" :href="route('reports.financial', array_merge(request()->all(), ['format' => 'excel']))" wire:navigate>
                            {{ __('Export Excel') }}
                        </flux:button>
                        <flux:button variant="ghost" :href="route('reports.index')" wire:navigate>
                            {{ __('Back to Reports') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <!-- Report Filters -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 mb-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <flux:field>
                            <flux:label>{{ __('Report Type') }}</flux:label>
                            <flux:select name="type">
                                <option value="income_statement" {{ request('type') === 'income_statement' ? 'selected' : '' }}>{{ __('Income Statement') }}</option>
                                <option value="balance_sheet" {{ request('type') === 'balance_sheet' ? 'selected' : '' }}>{{ __('Balance Sheet') }}</option>
                                <option value="cash_flow" {{ request('type') === 'cash_flow' ? 'selected' : '' }}>{{ __('Cash Flow') }}</option>
                                <option value="trial_balance" {{ request('type') === 'trial_balance' ? 'selected' : '' }}>{{ __('Trial Balance') }}</option>
                            </flux:select>
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>{{ __('Start Date') }}</flux:label>
                            <flux:input type="date" name="start_date" value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}" />
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>{{ __('End Date') }}</flux:label>
                            <flux:input type="date" name="end_date" value="{{ request('end_date', now()->endOfMonth()->format('Y-m-d')) }}" />
                        </flux:field>
                    </div>
                    <div class="flex items-end">
                        <flux:button type="submit" variant="primary" class="w-full">
                            {{ __('Generate Report') }}
                        </flux:button>
                    </div>
                </form>
            </div>

            @if($report_type === 'income_statement')
                <!-- Income Statement -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="text-center mb-6">
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">{{ __('Income Statement') }}</h2>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ \Carbon\Carbon::parse($start_date)->format('M j, Y') }} - {{ \Carbon\Carbon::parse($end_date)->format('M j, Y') }}
                        </p>
                    </div>

                    <div class="space-y-6">
                        <!-- Revenue -->
                        <div>
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Revenue') }}</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between py-2 border-b border-zinc-200 dark:border-zinc-700">
                                    <span class="text-zinc-700 dark:text-zinc-300">{{ __('Interest Income') }}</span>
                                    <span class="font-medium text-zinc-900 dark:text-zinc-100">KSh {{ number_format($income['loan_interest'] ?? 0, 2) }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-zinc-200 dark:border-zinc-700">
                                    <span class="text-zinc-700 dark:text-zinc-300">{{ __('Fee Income') }}</span>
                                    <span class="font-medium text-zinc-900 dark:text-zinc-100">KSh {{ number_format($income['fees'] ?? 0, 2) }}</span>
                                </div>
                                <div class="flex justify-between py-3 bg-zinc-50 dark:bg-zinc-700 px-4 rounded-lg">
                                    <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Total Revenue') }}</span>
                                    <span class="font-bold text-emerald-600 dark:text-emerald-400">KSh {{ number_format($total_income, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Expenses -->
                        <div>
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Expenses') }}</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between py-2 border-b border-zinc-200 dark:border-zinc-700">
                                    <span class="text-zinc-700 dark:text-zinc-300">{{ __('Staff Salaries') }}</span>
                                    <span class="font-medium text-zinc-900 dark:text-zinc-100">KSh {{ number_format($expenses['staff_salaries'] ?? 0, 2) }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-zinc-200 dark:border-zinc-700">
                                    <span class="text-zinc-700 dark:text-zinc-300">{{ __('Office Rent') }}</span>
                                    <span class="font-medium text-zinc-900 dark:text-zinc-100">KSh {{ number_format($expenses['office_rent'] ?? 0, 2) }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-zinc-200 dark:border-zinc-700">
                                    <span class="text-zinc-700 dark:text-zinc-300">{{ __('Utilities') }}</span>
                                    <span class="font-medium text-zinc-900 dark:text-zinc-100">KSh {{ number_format($expenses['utilities'] ?? 0, 2) }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-zinc-200 dark:border-zinc-700">
                                    <span class="text-zinc-700 dark:text-zinc-300">{{ __('Operational Expenses') }}</span>
                                    <span class="font-medium text-zinc-900 dark:text-zinc-100">KSh {{ number_format($expenses['operational_expenses'] ?? 0, 2) }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-zinc-200 dark:border-zinc-700">
                                    <span class="text-zinc-700 dark:text-zinc-300">{{ __('Bad Debt Provision') }}</span>
                                    <span class="font-medium text-zinc-900 dark:text-zinc-100">KSh {{ number_format($expenses['bad_debt_provision'] ?? 0, 2) }}</span>
                                </div>
                                <div class="flex justify-between py-3 bg-zinc-50 dark:bg-zinc-700 px-4 rounded-lg">
                                    <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Total Expenses') }}</span>
                                    <span class="font-bold text-red-600 dark:text-red-400">KSh {{ number_format($total_expenses, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Net Income -->
                        <div class="bg-gradient-to-r from-emerald-50 to-blue-50 dark:from-emerald-900/20 dark:to-blue-900/20 p-6 rounded-xl">
                            <div class="flex justify-between items-center">
                                <span class="text-xl font-bold text-zinc-900 dark:text-zinc-100">{{ __('Net Income') }}</span>
                                <span class="text-2xl font-bold {{ $net_income >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                    KSh {{ number_format($net_income, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

            @elseif($report_type === 'balance_sheet')
                <!-- Balance Sheet -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="text-center mb-6">
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">{{ __('Balance Sheet') }}</h2>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('As of') }} {{ \Carbon\Carbon::parse($end_date)->format('M j, Y') }}
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Assets Column -->
                        <div>
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Assets') }}</h3>
                            
                            <!-- Current Assets -->
                            <div class="mb-6">
                                <h4 class="text-md font-medium text-zinc-700 dark:text-zinc-300 mb-3">{{ __('Current Assets') }}</h4>
                                <div class="space-y-2">
                                    <div class="flex justify-between py-2 border-b border-zinc-200 dark:border-zinc-700">
                                        <span class="text-zinc-600 dark:text-zinc-400">{{ __('Cash and Bank') }}</span>
                                        <span class="font-medium text-zinc-900 dark:text-zinc-100">KSh {{ number_format($assets['cash_and_bank'], 2) }}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-zinc-200 dark:border-zinc-700">
                                        <span class="text-zinc-600 dark:text-zinc-400">{{ __('Loans Receivable') }}</span>
                                        <span class="font-medium text-zinc-900 dark:text-zinc-100">KSh {{ number_format($assets['loans_receivable'], 2) }}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-zinc-200 dark:border-zinc-700">
                                        <span class="text-zinc-600 dark:text-zinc-400">{{ __('Less: Loan Loss Provision') }}</span>
                                        <span class="font-medium text-red-600 dark:text-red-400">(KSh {{ number_format($assets['less_loan_loss_provision'], 2) }})</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Fixed Assets -->
                            <div class="mb-6">
                                <h4 class="text-md font-medium text-zinc-700 dark:text-zinc-300 mb-3">{{ __('Fixed Assets') }}</h4>
                                <div class="space-y-2">
                                    <div class="flex justify-between py-2 border-b border-zinc-200 dark:border-zinc-700">
                                        <span class="text-zinc-600 dark:text-zinc-400">{{ __('Office Equipment') }}</span>
                                        <span class="font-medium text-zinc-900 dark:text-zinc-100">KSh {{ number_format($assets['office_equipment'], 2) }}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-zinc-200 dark:border-zinc-700">
                                        <span class="text-zinc-600 dark:text-zinc-400">{{ __('Furniture & Fixtures') }}</span>
                                        <span class="font-medium text-zinc-900 dark:text-zinc-100">KSh {{ number_format($assets['furniture_fixtures'], 2) }}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-zinc-200 dark:border-zinc-700">
                                        <span class="text-zinc-600 dark:text-zinc-400">{{ __('Computer Equipment') }}</span>
                                        <span class="font-medium text-zinc-900 dark:text-zinc-100">KSh {{ number_format($assets['computer_equipment'], 2) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-bold text-zinc-900 dark:text-zinc-100">{{ __('Total Assets') }}</span>
                                    <span class="text-lg font-bold text-blue-600 dark:text-blue-400">KSh {{ number_format($total_assets, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Liabilities & Equity Column -->
                        <div>
                            <!-- Liabilities -->
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Liabilities') }}</h3>
                                <div class="space-y-2">
                                    <div class="flex justify-between py-2 border-b border-zinc-200 dark:border-zinc-700">
                                        <span class="text-zinc-600 dark:text-zinc-400">{{ __('Member Savings') }}</span>
                                        <span class="font-medium text-zinc-900 dark:text-zinc-100">KSh {{ number_format($liabilities['member_savings'], 2) }}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-zinc-200 dark:border-zinc-700">
                                        <span class="text-zinc-600 dark:text-zinc-400">{{ __('Member Shares') }}</span>
                                        <span class="font-medium text-zinc-900 dark:text-zinc-100">KSh {{ number_format($liabilities['member_shares'], 2) }}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-zinc-200 dark:border-zinc-700">
                                        <span class="text-zinc-600 dark:text-zinc-400">{{ __('Accrued Expenses') }}</span>
                                        <span class="font-medium text-zinc-900 dark:text-zinc-100">KSh {{ number_format($liabilities['accrued_expenses'], 2) }}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-zinc-200 dark:border-zinc-700">
                                        <span class="text-zinc-600 dark:text-zinc-400">{{ __('Other Payables') }}</span>
                                        <span class="font-medium text-zinc-900 dark:text-zinc-100">KSh {{ number_format($liabilities['other_payables'], 2) }}</span>
                                    </div>
                                </div>
                                <div class="bg-orange-50 dark:bg-orange-900/20 p-3 rounded-lg mt-4">
                                    <div class="flex justify-between items-center">
                                        <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Total Liabilities') }}</span>
                                        <span class="font-bold text-orange-600 dark:text-orange-400">KSh {{ number_format($total_liabilities, 2) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Equity -->
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Members Equity') }}</h3>
                                <div class="space-y-2">
                                    <div class="flex justify-between py-2 border-b border-zinc-200 dark:border-zinc-700">
                                        <span class="text-zinc-600 dark:text-zinc-400">{{ __('Initial Capital') }}</span>
                                        <span class="font-medium text-zinc-900 dark:text-zinc-100">KSh {{ number_format($equity['initial_capital'], 2) }}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-zinc-200 dark:border-zinc-700">
                                        <span class="text-zinc-600 dark:text-zinc-400">{{ __('Retained Earnings') }}</span>
                                        <span class="font-medium text-zinc-900 dark:text-zinc-100">KSh {{ number_format($equity['retained_earnings'], 2) }}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-zinc-200 dark:border-zinc-700">
                                        <span class="text-zinc-600 dark:text-zinc-400">{{ __('Current Year Surplus') }}</span>
                                        <span class="font-medium text-zinc-900 dark:text-zinc-100">KSh {{ number_format($equity['current_year_surplus'], 2) }}</span>
                                    </div>
                                </div>
                                <div class="bg-emerald-50 dark:bg-emerald-900/20 p-3 rounded-lg mt-4">
                                    <div class="flex justify-between items-center">
                                        <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Total Equity') }}</span>
                                        <span class="font-bold text-emerald-600 dark:text-emerald-400">KSh {{ number_format($total_equity, 2) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-bold text-zinc-900 dark:text-zinc-100">{{ __('Total Liab. & Equity') }}</span>
                                    <span class="text-lg font-bold text-blue-600 dark:text-blue-400">KSh {{ number_format($total_liabilities + $total_equity, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @elseif($report_type === 'cash_flow')
                <!-- Cash Flow Statement -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="text-center mb-6">
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">{{ __('Cash Flow Statement') }}</h2>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ \Carbon\Carbon::parse($start_date)->format('M j, Y') }} - {{ \Carbon\Carbon::parse($end_date)->format('M j, Y') }}
                        </p>
                    </div>

                    <div class="space-y-8">
                        <!-- Operating Activities -->
                        <div>
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Cash Flow from Operating Activities') }}</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between py-2 border-b border-zinc-200 dark:border-zinc-700">
                                    <span class="text-zinc-700 dark:text-zinc-300">{{ __('Cash from Member Deposits') }}</span>
                                    <span class="font-medium text-emerald-600 dark:text-emerald-400">KSh {{ number_format($operating['cash_from_deposits'], 2) }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-zinc-200 dark:border-zinc-700">
                                    <span class="text-zinc-700 dark:text-zinc-300">{{ __('Fees Collected') }}</span>
                                    <span class="font-medium text-emerald-600 dark:text-emerald-400">KSh {{ number_format($operating['fees_collected'], 2) }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-zinc-200 dark:border-zinc-700">
                                    <span class="text-zinc-700 dark:text-zinc-300">{{ __('Interest Received') }}</span>
                                    <span class="font-medium text-emerald-600 dark:text-emerald-400">KSh {{ number_format($operating['interest_received'], 2) }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-zinc-200 dark:border-zinc-700">
                                    <span class="text-zinc-700 dark:text-zinc-300">{{ __('Cash from Withdrawals') }}</span>
                                    <span class="font-medium text-red-600 dark:text-red-400">KSh {{ number_format($operating['cash_from_withdrawals'], 2) }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-zinc-200 dark:border-zinc-700">
                                    <span class="text-zinc-700 dark:text-zinc-300">{{ __('Operational Expenses') }}</span>
                                    <span class="font-medium text-red-600 dark:text-red-400">KSh {{ number_format($operating['operational_expenses'], 2) }}</span>
                                </div>
                                <div class="flex justify-between py-3 bg-blue-50 dark:bg-blue-900/20 px-4 rounded-lg">
                                    <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Net Cash from Operating Activities') }}</span>
                                    <span class="font-bold {{ $net_operating >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                        KSh {{ number_format($net_operating, 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Investing Activities -->
                        <div>
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Cash Flow from Investing Activities') }}</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between py-2 border-b border-zinc-200 dark:border-zinc-700">
                                    <span class="text-zinc-700 dark:text-zinc-300">{{ __('Loans Advanced to Members') }}</span>
                                    <span class="font-medium text-red-600 dark:text-red-400">KSh {{ number_format($investing['loans_advanced'], 2) }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-zinc-200 dark:border-zinc-700">
                                    <span class="text-zinc-700 dark:text-zinc-300">{{ __('Equipment Purchases') }}</span>
                                    <span class="font-medium text-red-600 dark:text-red-400">KSh {{ number_format($investing['equipment_purchases'], 2) }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-zinc-200 dark:border-zinc-700">
                                    <span class="text-zinc-700 dark:text-zinc-300">{{ __('Investments') }}</span>
                                    <span class="font-medium text-zinc-900 dark:text-zinc-100">KSh {{ number_format($investing['investments'], 2) }}</span>
                                </div>
                                <div class="flex justify-between py-3 bg-orange-50 dark:bg-orange-900/20 px-4 rounded-lg">
                                    <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Net Cash from Investing Activities') }}</span>
                                    <span class="font-bold {{ $net_investing >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                        KSh {{ number_format($net_investing, 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Financing Activities -->
                        <div>
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Cash Flow from Financing Activities') }}</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between py-2 border-b border-zinc-200 dark:border-zinc-700">
                                    <span class="text-zinc-700 dark:text-zinc-300">{{ __('Loan Repayments Received') }}</span>
                                    <span class="font-medium text-emerald-600 dark:text-emerald-400">KSh {{ number_format($financing['loan_repayments_received'], 2) }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-zinc-200 dark:border-zinc-700">
                                    <span class="text-zinc-700 dark:text-zinc-300">{{ __('Member Share Contributions') }}</span>
                                    <span class="font-medium text-emerald-600 dark:text-emerald-400">KSh {{ number_format($financing['member_share_contributions'], 2) }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-zinc-200 dark:border-zinc-700">
                                    <span class="text-zinc-700 dark:text-zinc-300">{{ __('Dividends Paid') }}</span>
                                    <span class="font-medium text-red-600 dark:text-red-400">KSh {{ number_format($financing['dividends_paid'], 2) }}</span>
                                </div>
                                <div class="flex justify-between py-3 bg-purple-50 dark:bg-purple-900/20 px-4 rounded-lg">
                                    <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Net Cash from Financing Activities') }}</span>
                                    <span class="font-bold {{ $net_financing >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                        KSh {{ number_format($net_financing, 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Net Cash Flow -->
                        <div class="bg-gradient-to-r from-emerald-50 to-blue-50 dark:from-emerald-900/20 dark:to-blue-900/20 p-6 rounded-xl">
                            <div class="flex justify-between items-center">
                                <span class="text-xl font-bold text-zinc-900 dark:text-zinc-100">{{ __('Net Increase in Cash') }}</span>
                                <span class="text-2xl font-bold {{ $net_cash_flow >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                    KSh {{ number_format($net_cash_flow, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

            @elseif($report_type === 'trial_balance')
                <!-- Trial Balance -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="text-center mb-6">
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">{{ __('Trial Balance') }}</h2>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('As of') }} {{ \Carbon\Carbon::parse($end_date)->format('M j, Y') }}
                        </p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-zinc-50 dark:bg-zinc-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Account Name') }}</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Account Type') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Debit') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Credit') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                @foreach($accounts as $accountName => $accountData)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                        {{ ucwords(str_replace('_', ' ', $accountName)) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                                            @if($accountData['type'] === 'asset') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                            @elseif($accountData['type'] === 'liability') bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400
                                            @elseif($accountData['type'] === 'equity') bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400
                                            @elseif($accountData['type'] === 'revenue') bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400
                                            @else bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                            @endif">
                                            {{ ucfirst($accountData['type']) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-zinc-900 dark:text-zinc-100">
                                        @if($accountData['debit'] > 0)
                                            KSh {{ number_format($accountData['debit'], 2) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-zinc-900 dark:text-zinc-100">
                                        @if($accountData['credit'] > 0)
                                            KSh {{ number_format($accountData['credit'], 2) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-zinc-50 dark:bg-zinc-700">
                                <tr>
                                    <th colspan="2" class="px-6 py-4 text-left text-sm font-bold text-zinc-900 dark:text-zinc-100">{{ __('TOTALS') }}</th>
                                    <th class="px-6 py-4 text-right text-sm font-bold text-blue-600 dark:text-blue-400">
                                        KSh {{ number_format($total_debits, 2) }}
                                    </th>
                                    <th class="px-6 py-4 text-right text-sm font-bold text-blue-600 dark:text-blue-400">
                                        KSh {{ number_format($total_credits, 2) }}
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Balance Check -->
                    <div class="mt-6 p-4 rounded-lg {{ $total_debits == $total_credits ? 'bg-emerald-50 dark:bg-emerald-900/20' : 'bg-red-50 dark:bg-red-900/20' }}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                @if($total_debits == $total_credits)
                                    <flux:icon.check-circle class="w-5 h-5 text-emerald-600 dark:text-emerald-400 mr-2" />
                                    <span class="text-sm font-medium text-emerald-700 dark:text-emerald-300">{{ __('Trial Balance is Balanced') }}</span>
                                @else
                                    <flux:icon.x-circle class="w-5 h-5 text-red-600 dark:text-red-400 mr-2" />
                                    <span class="text-sm font-medium text-red-700 dark:text-red-300">{{ __('Trial Balance is Out of Balance') }}</span>
                                @endif
                            </div>
                            <div class="text-sm">
                                <span class="text-zinc-600 dark:text-zinc-400">{{ __('Difference:') }}</span>
                                <span class="font-bold {{ abs($total_debits - $total_credits) == 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                    KSh {{ number_format(abs($total_debits - $total_credits), 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Report Summary -->
            <div class="mt-6 bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Report Information') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-zinc-600 dark:text-zinc-400">{{ __('Generated On:') }}</span>
                        <span class="font-medium text-zinc-900 dark:text-zinc-100 ml-2">{{ $generated_at->format('M j, Y g:i A') }}</span>
                    </div>
                    <div>
                        <span class="text-zinc-600 dark:text-zinc-400">{{ __('Report Type:') }}</span>
                        <span class="font-medium text-zinc-900 dark:text-zinc-100 ml-2">{{ ucfirst(str_replace('_', ' ', $report_type)) }}</span>
                    </div>
                    <div>
                        <span class="text-zinc-600 dark:text-zinc-400">{{ __('Period:') }}</span>
                        <span class="font-medium text-zinc-900 dark:text-zinc-100 ml-2">
                            {{ \Carbon\Carbon::parse($start_date)->format('M j, Y') }} - {{ \Carbon\Carbon::parse($end_date)->format('M j, Y') }}
                        </span>
                    </div>
                    <div>
                        <span class="text-zinc-600 dark:text-zinc-400">{{ __('Generated By:') }}</span>
                        <span class="font-medium text-zinc-900 dark:text-zinc-100 ml-2">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app> 