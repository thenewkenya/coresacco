<x-layouts.app :title="__('Analytics')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Sticky Header with Navigation -->
        <div class="sticky top-0 z-50 bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700 shadow-sm">
            <div class="px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div class="flex-1">
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('Analytics Dashboard') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Comprehensive insights and analytics for SACCO operations') }}
                        </p>
                    </div>
                    
                    <!-- Quick Navigation -->
                    <div class="flex items-center space-x-1 overflow-x-auto scrollbar-hide">
                        <flux:button variant="ghost" size="sm" class="whitespace-nowrap transition-colors" onclick="document.getElementById('overview').scrollIntoView({behavior: 'smooth'})">
                            {{ __('Overview') }}
                        </flux:button>
                        <flux:button variant="ghost" size="sm" class="whitespace-nowrap transition-colors" onclick="document.getElementById('insights').scrollIntoView({behavior: 'smooth'})">
                            {{ __('Insights') }}
                        </flux:button>
                        <flux:button variant="ghost" size="sm" class="whitespace-nowrap transition-colors" onclick="document.getElementById('charts').scrollIntoView({behavior: 'smooth'})">
                            {{ __('Charts') }}
                        </flux:button>
                        <flux:button variant="ghost" size="sm" class="whitespace-nowrap transition-colors" onclick="document.getElementById('advanced').scrollIntoView({behavior: 'smooth'})">
                            {{ __('Advanced') }}
                        </flux:button>
                    </div>

                    <!-- Controls -->
                    <div class="flex items-center space-x-3">
                        <flux:select name="period" class="min-w-[140px]">
                            <option value="1month" {{ $period == '1month' ? 'selected' : '' }}>{{ __('Last Month') }}</option>
                            <option value="3months" {{ $period == '3months' ? 'selected' : '' }}>{{ __('Last 3 Months') }}</option>
                            <option value="6months" {{ $period == '6months' ? 'selected' : '' }}>{{ __('Last 6 Months') }}</option>
                            <option value="12months" {{ $period == '12months' ? 'selected' : '' }}>{{ __('Last 12 Months') }}</option>
                            <option value="all" {{ $period == 'all' ? 'selected' : '' }}>{{ __('All Time') }}</option>
                        </flux:select>

                        <flux:button variant="outline" size="sm" icon="arrow-path" onclick="window.location.reload()">
                            <span class="hidden sm:inline">{{ __('Refresh') }}</span>
                        </flux:button>
                        
                        <flux:dropdown>
                            <flux:button variant="primary" size="sm" icon="arrow-down-tray">
                                <span class="hidden sm:inline">{{ __('Export') }}</span>
                            </flux:button>
                            <flux:menu>
                                <flux:menu.item icon="document" :href="route('analytics.export', ['format' => 'pdf', 'period' => $period])">
                                    {{ __('Export as PDF') }}
                                </flux:menu.item>
                                <flux:menu.item icon="table-cells" :href="route('analytics.export', ['format' => 'csv', 'period' => $period])">
                                    {{ __('Export as CSV') }}
                                </flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-6 space-y-8">
            <!-- Key Metrics Overview -->
            <section id="overview">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Key Performance Indicators') }}</h2>
                    <flux:badge color="blue">{{ ucfirst($period) }}</flux:badge>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
                <!-- Active Members -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <flux:icon.users class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <span class="text-sm font-medium {{ $overview['active_members']['trend'] == 'up' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ $overview['active_members']['trend'] == 'up' ? '+' : '' }}{{ $overview['active_members']['change'] }}%
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Active Members') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($overview['active_members']['value']) }}</p>
                    </div>
                </div>

                <!-- Total Assets -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                            <flux:icon.banknotes class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <span class="text-sm font-medium {{ $overview['total_assets']['trend'] == 'up' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ $overview['total_assets']['trend'] == 'up' ? '+' : '' }}{{ $overview['total_assets']['change'] }}%
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Total Assets') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KSh {{ number_format($overview['total_assets']['value'], 2) }}</p>
                    </div>
                </div>

                <!-- Active Loans -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                            <flux:icon.credit-card class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                        </div>
                        <span class="text-sm font-medium {{ $overview['active_loans']['trend'] == 'up' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ $overview['active_loans']['trend'] == 'up' ? '+' : '' }}{{ $overview['active_loans']['change'] }}%
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Active Loans') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($overview['active_loans']['value']) }}</p>
                    </div>
                </div>

                <!-- Portfolio Performance -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                            <flux:icon.chart-bar class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                        </div>
                        <span class="text-sm font-medium {{ $overview['portfolio_performance']['trend'] == 'up' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ $overview['portfolio_performance']['trend'] == 'up' ? '+' : '' }}{{ $overview['portfolio_performance']['change'] }}%
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">{{ __('Portfolio Performance') }}</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $overview['portfolio_performance']['value'] }}%</p>
                    </div>
                </div>
            </div>

            <!-- Charts and Analytics Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Financial Trends Chart -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Financial Trends') }}</h3>
                        <flux:badge color="blue">{{ ucfirst($period) }}</flux:badge>
                    </div>
                    <div class="h-80">
                        <canvas id="financialTrendsChart"></canvas>
                    </div>
                </div>

                <!-- Account Distribution -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Account Distribution') }}</h3>
                        <flux:badge color="emerald">{{ __('Active Accounts') }}</flux:badge>
                    </div>
                    <div class="h-80">
                        <canvas id="accountDistributionChart"></canvas>
                    </div>
                </div>
            </section>

            <!-- AI Insights Section -->
            <section id="insights">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100">{{ __('AI-Powered Insights') }}</h2>
                    <flux:badge color="yellow">{{ __('Auto-refresh') }}</flux:badge>
                </div>
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <livewire:analytics.insights-widget />
                </div>
            </section>

            <!-- Charts Section -->
            <section id="charts">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Analytics Charts') }}</h2>
                    <flux:badge color="emerald">{{ __('Live Data') }}</flux:badge>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Financial Trends Chart -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-4 flex items-center">
                            <flux:icon.chart-bar class="w-5 h-5 mr-2 text-emerald-600" />
                            {{ __('Financial Trends') }}
                        </h3>
                        <div class="h-64 chart-container">
                            <canvas id="financialTrendsChart" width="400" height="200"></canvas>
                        </div>
                    </div>

                    <!-- Account Distribution Chart -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-4 flex items-center">
                            <flux:icon.chart-pie class="w-5 h-5 mr-2 text-blue-600" />
                            {{ __('Account Distribution') }}
                        </h3>
                        <div class="h-64 chart-container flex items-center justify-center">
                            <canvas id="accountDistributionChart" width="300" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Enhanced Charts Section -->
            <section id="advanced">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Analytics Dashboard') }}</h2>
                    <flux:badge color="purple">{{ __('Period: ') . ucfirst($period) }}</flux:badge>
                </div>
                
                <!-- Enhanced Charts Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Loan Portfolio Distribution -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-4 flex items-center">
                            <flux:icon.banknotes class="w-5 h-5 mr-2 text-emerald-600" />
                            {{ __('Loan Portfolio Distribution') }}
                        </h3>
                        <div class="h-64 chart-container">
                            <canvas id="loanPortfolioChart" width="400" height="200"></canvas>
                        </div>
                    </div>

                    <!-- Revenue Trends -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-4 flex items-center">
                            <flux:icon.chart-bar class="w-5 h-5 mr-2 text-blue-600" />
                            {{ __('Revenue Trends') }}
                        </h3>
                        <div class="h-64 chart-container">
                            <canvas id="revenueChart" width="400" height="200"></canvas>
                        </div>
                    </div>

                    <!-- Member Engagement -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-4 flex items-center">
                            <flux:icon.users class="w-5 h-5 mr-2 text-purple-600" />
                            {{ __('Member Engagement') }}
                        </h3>
                        <div class="h-64 chart-container">
                            <canvas id="memberEngagementChart" width="400" height="200"></canvas>
                        </div>
                    </div>

                    <!-- Transaction Volume -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-4 flex items-center">
                            <flux:icon.chart-pie class="w-5 h-5 mr-2 text-orange-600" />
                            {{ __('Transaction Volume') }}
                        </h3>
                        <div class="h-64 chart-container">
                            <canvas id="transactionVolumeChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Comprehensive Analytics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    <!-- Financial Metrics -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <h4 class="font-medium text-zinc-900 dark:text-zinc-100 mb-4 flex items-center">
                            <flux:icon.banknotes class="w-4 h-4 mr-2 text-emerald-600" />
                            {{ __('Financial Health') }}
                        </h4>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Portfolio Performance') }}</span>
                                <span class="font-medium text-emerald-600 dark:text-emerald-400">{{ $advanced['financial']['profitability_analysis']['roi_percentage'] }}%</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Default Rate') }}</span>
                                <span class="font-medium {{ $advanced['financial']['portfolio_performance']['loan_performance']['default_rate'] < 5 ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $advanced['financial']['portfolio_performance']['loan_performance']['default_rate'] }}%
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Liquidity Ratio') }}</span>
                                <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $advanced['financial']['risk_assessment']['liquidity_ratio'] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Member Growth -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <h4 class="font-medium text-zinc-900 dark:text-zinc-100 mb-4 flex items-center">
                            <flux:icon.users class="w-4 h-4 mr-2 text-blue-600" />
                            {{ __('Member Growth') }}
                        </h4>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Retention Rate') }}</span>
                                <span class="font-medium text-emerald-600 dark:text-emerald-400">{{ $advanced['member']['member_growth']['retention_rate'] }}%</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Active Users') }}</span>
                                <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($advanced['member']['engagement_metrics']['active_users']) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Loan Uptake') }}</span>
                                <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $advanced['member']['loan_utilization']['loan_uptake_rate'] }}%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Operations -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <h4 class="font-medium text-zinc-900 dark:text-zinc-100 mb-4 flex items-center">
                            <flux:icon.cog class="w-4 h-4 mr-2 text-purple-600" />
                            {{ __('Operations') }}
                        </h4>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Success Rate') }}</span>
                                <span class="font-medium text-emerald-600 dark:text-emerald-400">{{ number_format($advanced['operational']['processing_efficiency']['success_rate'], 1) }}%</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Service Uptime') }}</span>
                                <span class="font-medium text-emerald-600 dark:text-emerald-400">{{ $advanced['operational']['service_quality']['service_uptime'] }}%</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Avg Processing') }}</span>
                                <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($advanced['operational']['processing_efficiency']['avg_processing_time'] ?? 0, 1) }}min</span>
                            </div>
                        </div>
                    </div>

                    <!-- Revenue Breakdown -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <h4 class="font-medium text-zinc-900 dark:text-zinc-100 mb-4 flex items-center">
                            <flux:icon.currency-dollar class="w-4 h-4 mr-2 text-green-600" />
                            {{ __('Revenue Sources') }}
                        </h4>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Loan Interest') }}</span>
                                <span class="font-medium text-zinc-900 dark:text-zinc-100">KSh {{ number_format($financial['revenue_streams']['loan_interest'], 0) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Service Fees') }}</span>
                                <span class="font-medium text-zinc-900 dark:text-zinc-100">KSh {{ number_format($financial['revenue_streams']['fees'], 0) }}</span>
                            </div>
                            <div class="flex justify-between items-center pt-2 border-t border-zinc-200 dark:border-zinc-700">
                                <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ __('Total') }}</span>
                                <span class="font-bold text-emerald-600 dark:text-emerald-400">
                                    KSh {{ number_format($financial['revenue_streams']['loan_interest'] + $financial['revenue_streams']['fees'], 0) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>


        </div>
        
        <!-- Back to Top Button -->
        <button id="backToTop" 
                class="fixed bottom-6 right-6 bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-full shadow-lg transition-all duration-300 opacity-0 invisible z-40"
                onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
            <flux:icon.arrow-up class="w-5 h-5" />
        </button>
    </div>

    <!-- Custom Styles -->
    <style>
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        
        /* Smooth transitions for section highlights */
        section {
            scroll-margin-top: 120px;
        }
        
        /* Loading animation for charts */
        .chart-container {
            position: relative;
        }
        
        .chart-container::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 40px;
            height: 40px;
            border: 3px solid #f3f4f6;
            border-top: 3px solid #3b82f6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            z-index: 1;
        }
        
        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }
        
        /* Hide loading animation when chart is ready */
        .chart-ready::before {
            display: none;
        }
    </style>

    <!-- Scripts for Charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Financial Trends Chart
        const financialCtx = document.getElementById('financialTrendsChart').getContext('2d');
        const trendsData = @json($trends) || [];
        
        // Fallback data if no trends available
        const fallbackTrends = trendsData.length > 0 ? trendsData : [
            {month: 'Current', deposits: 0, loans: 0, new_members: 0}
        ];
        
        const financialChart = new Chart(financialCtx, {
            type: 'line',
            data: {
                labels: fallbackTrends.map(item => item.month),
                datasets: [
                    {
                        label: 'Deposits',
                        data: fallbackTrends.map(item => item.deposits || 0),
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Loans',
                        data: fallbackTrends.map(item => item.loans || 0),
                        borderColor: 'rgb(168, 85, 247)',
                        backgroundColor: 'rgba(168, 85, 247, 0.1)',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'KSh ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
        
        // Mark financial chart container as ready
        document.querySelector('#financialTrendsChart').parentElement.classList.add('chart-ready');

        // Account Distribution Chart
        const accountCtx = document.getElementById('accountDistributionChart').getContext('2d');
        const accountData = @json($financial['account_distribution']) || [];
        
        // Fallback data if no account distribution available
        const fallbackAccounts = accountData.length > 0 ? accountData : [
            {account_type: 'savings', total_balance: 0, count: 0}
        ];
        
        const accountChart = new Chart(accountCtx, {
            type: 'doughnut',
            data: {
                labels: fallbackAccounts.map(item => {
                    return item.account_type.charAt(0).toUpperCase() + item.account_type.slice(1).replace('_', ' ');
                }),
                datasets: [{
                    data: fallbackAccounts.map(item => item.total_balance || 0),
                    backgroundColor: [
                        'rgb(59, 130, 246)',
                        'rgb(34, 197, 94)',
                        'rgb(168, 85, 247)',
                        'rgb(245, 158, 11)',
                        'rgb(239, 68, 68)',
                        'rgb(6, 182, 212)',
                        'rgb(236, 72, 153)',
                        'rgb(139, 69, 19)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': KSh ' + context.parsed.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
        
        // Mark account chart container as ready
        document.querySelector('#accountDistributionChart').parentElement.classList.add('chart-ready');

        // Loan Portfolio Distribution Chart
        const loanPortfolioCtx = document.getElementById('loanPortfolioChart').getContext('2d');
        const loanPortfolioData = {
            active: {{ $financial['loan_portfolio']['active_loans'] ?? 0 }},
            completed: {{ $financial['loan_portfolio']['completed_loans'] ?? 0 }},
            defaulted: {{ $financial['loan_portfolio']['defaulted_loans'] ?? 0 }}
        };
        
        const loanPortfolioChart = new Chart(loanPortfolioCtx, {
            type: 'doughnut',
            data: {
                labels: ['Active Loans', 'Completed Loans', 'Defaulted Loans'],
                datasets: [{
                    data: [
                        loanPortfolioData.active,
                        loanPortfolioData.completed,
                        loanPortfolioData.defaulted
                    ],
                    backgroundColor: [
                        'rgb(59, 130, 246)',   // Blue for active
                        'rgb(34, 197, 94)',    // Green for completed 
                        'rgb(239, 68, 68)'     // Red for defaulted
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': KSh ' + context.parsed.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
        document.querySelector('#loanPortfolioChart').parentElement.classList.add('chart-ready');

        // Revenue Trends Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueData = {
            interest: {{ $financial['revenue_streams']['loan_interest'] ?? 0 }},
            fees: {{ $financial['revenue_streams']['fees'] ?? 0 }}
        };
        
        const revenueChart = new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: ['Loan Interest', 'Service Fees'],
                datasets: [{
                    label: 'Revenue (KSh)',
                    data: [
                        revenueData.interest,
                        revenueData.fees
                    ],
                    backgroundColor: [
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(59, 130, 246, 0.8)'
                    ],
                    borderColor: [
                        'rgb(34, 197, 94)',
                        'rgb(59, 130, 246)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'KSh ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
        document.querySelector('#revenueChart').parentElement.classList.add('chart-ready');

        // Member Engagement Chart
        const memberEngagementCtx = document.getElementById('memberEngagementChart').getContext('2d');
        const memberEngagementData = {
            savers: {{ $members['engagement']['active_savers'] ?? 0 }},
            loaners: {{ $members['engagement']['loan_members'] ?? 0 }},
            budgeters: {{ $members['engagement']['budget_users'] ?? 0 }},
            goalers: {{ $members['engagement']['goal_setters'] ?? 0 }}
        };
        
        const memberEngagementChart = new Chart(memberEngagementCtx, {
            type: 'radar',
            data: {
                labels: ['Active Savers', 'Loan Members', 'Budget Users', 'Goal Setters'],
                datasets: [{
                    label: 'Member Engagement',
                    data: [
                        memberEngagementData.savers,
                        memberEngagementData.loaners,
                        memberEngagementData.budgeters,
                        memberEngagementData.goalers
                    ],
                    borderColor: 'rgb(168, 85, 247)',
                    backgroundColor: 'rgba(168, 85, 247, 0.2)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgb(168, 85, 247)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgb(168, 85, 247)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    r: {
                        beginAtZero: true,
                        ticks: {
                            display: false
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        angleLines: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                }
            }
        });
        document.querySelector('#memberEngagementChart').parentElement.classList.add('chart-ready');

        // Transaction Volume Chart (using service utilization data)
        const transactionVolumeCtx = document.getElementById('transactionVolumeChart').getContext('2d');
        const transactionVolumeData = {
            loans: {{ $operations['service_utilization']['loan_applications'] ?? 0 }},
            accounts: {{ $operations['service_utilization']['account_openings'] ?? 0 }},
            budgets: {{ $operations['service_utilization']['budget_creations'] ?? 0 }},
            goals: {{ $operations['service_utilization']['goal_settings'] ?? 0 }}
        };
        
        const transactionVolumeChart = new Chart(transactionVolumeCtx, {
            type: 'line',
            data: {
                labels: ['Loan Apps', 'Account Opens', 'Budget Creates', 'Goal Sets'],
                datasets: [{
                    label: 'Transaction Volume',
                    data: [
                        transactionVolumeData.loans,
                        transactionVolumeData.accounts,
                        transactionVolumeData.budgets,
                        transactionVolumeData.goals
                    ],
                    borderColor: 'rgb(245, 158, 11)',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: 'rgb(245, 158, 11)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgb(245, 158, 11)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 45
                        }
                    }
                }
            }
        });
        document.querySelector('#transactionVolumeChart').parentElement.classList.add('chart-ready');

        // Period filter change handler
        document.querySelector('select[name="period"]').addEventListener('change', function() {
            const url = new URL(window.location);
            url.searchParams.set('period', this.value);
            window.location = url;
        });

        // Back to top button functionality
        window.addEventListener('scroll', function() {
            const backToTop = document.getElementById('backToTop');
            if (window.pageYOffset > 300) {
                backToTop.classList.remove('opacity-0', 'invisible');
                backToTop.classList.add('opacity-100', 'visible');
            } else {
                backToTop.classList.remove('opacity-100', 'visible');
                backToTop.classList.add('opacity-0', 'invisible');
            }
        });

        // Update URL hash on scroll for better navigation
        const sections = document.querySelectorAll('section[id]');
        const navButtons = document.querySelectorAll('[onclick*="scrollIntoView"]');
        
        window.addEventListener('scroll', function() {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (pageYOffset >= sectionTop - 200) {
                    current = section.getAttribute('id');
                }
            });
            
            // Update nav button states
            navButtons.forEach(button => {
                const target = button.getAttribute('onclick').match(/'([^']+)'/)[1];
                if (target === current) {
                    button.classList.add('bg-blue-100', 'text-blue-700', 'dark:bg-blue-900', 'dark:text-blue-300');
                } else {
                    button.classList.remove('bg-blue-100', 'text-blue-700', 'dark:bg-blue-900', 'dark:text-blue-300');
                }
            });
        });
    </script>
</x-layouts.app> 