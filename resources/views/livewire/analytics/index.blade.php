<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public $period = '3months';
    public $overview = [];
    public $financial = [];
    public $members = [];
    public $operations = [];
    public $advanced = [];
    public $trends = [];

    public function mount()
    {
        $this->loadAnalyticsData();
    }

    public function loadAnalyticsData()
    {
        // Mock data for demonstration - in real app, this would come from the controller
        $this->overview = [
            'active_members' => ['value' => 1250, 'change' => 12.5, 'trend' => 'up'],
            'total_assets' => ['value' => 45000000, 'change' => 8.3, 'trend' => 'up'],
            'active_loans' => ['value' => 340, 'change' => -2.1, 'trend' => 'down'],
            'portfolio_performance' => ['value' => 94.2, 'change' => 3.7, 'trend' => 'up'],
        ];

        $this->financial = [
            'revenue_streams' => [
                'loan_interest' => 2500000,
                'fees' => 450000
            ],
            'account_distribution' => [
                ['account_type' => 'savings', 'total_balance' => 25000000, 'count' => 800],
                ['account_type' => 'checking', 'total_balance' => 15000000, 'count' => 450],
                ['account_type' => 'fixed_deposit', 'total_balance' => 5000000, 'count' => 120]
            ],
            'loan_portfolio' => [
                'active_loans' => 340,
                'completed_loans' => 1250,
                'defaulted_loans' => 15
            ]
        ];

        $this->members = [
            'engagement' => [
                'active_savers' => 800,
                'loan_members' => 340,
                'budget_users' => 450,
                'goal_setters' => 200
            ]
        ];

        $this->operations = [
            'service_utilization' => [
                'loan_applications' => 45,
                'account_openings' => 23,
                'budget_creations' => 67,
                'goal_settings' => 34
            ]
        ];

        $this->advanced = [
            'financial' => [
                'profitability_analysis' => ['roi_percentage' => 12.5],
                'portfolio_performance' => ['loan_performance' => ['default_rate' => 3.2]],
                'risk_assessment' => ['liquidity_ratio' => 1.8]
            ],
            'member' => [
                'member_growth' => ['retention_rate' => 94.5],
                'engagement_metrics' => ['active_users' => 1250],
                'loan_utilization' => ['loan_uptake_rate' => 78.2]
            ],
            'operational' => [
                'processing_efficiency' => ['success_rate' => 98.7, 'avg_processing_time' => 2.3],
                'service_quality' => ['service_uptime' => 99.9]
            ]
        ];

        $this->trends = [
            ['month' => 'Jan', 'deposits' => 1200000, 'loans' => 800000, 'new_members' => 45],
            ['month' => 'Feb', 'deposits' => 1350000, 'loans' => 950000, 'new_members' => 52],
            ['month' => 'Mar', 'deposits' => 1420000, 'loans' => 1100000, 'new_members' => 38],
            ['month' => 'Apr', 'deposits' => 1580000, 'loans' => 1250000, 'new_members' => 41],
            ['month' => 'May', 'deposits' => 1650000, 'loans' => 1380000, 'new_members' => 47],
            ['month' => 'Jun', 'deposits' => 1720000, 'loans' => 1450000, 'new_members' => 43]
        ];
    }

    public function updatedPeriod()
    {
        $this->loadAnalyticsData();
    }
}

?><div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" class="!text-zinc-900 dark:!text-zinc-100">Analytics Dashboard</flux:heading>
            <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">Comprehensive insights and analytics for SACCO operations</flux:subheading>
        </div>
        <div class="flex items-center space-x-3">
            <flux:select wire:model.live="period" class="min-w-[140px]">
                <option value="1month">Last Month</option>
                <option value="3months">Last 3 Months</option>
                <option value="6months">Last 6 Months</option>
                <option value="12months">Last 12 Months</option>
                <option value="all">All Time</option>
            </flux:select>

            <flux:button variant="outline" size="sm" icon="arrow-path" wire:click="loadAnalyticsData">
                <span class="hidden sm:inline">Refresh</span>
            </flux:button>
            
            <flux:dropdown>
                <flux:button variant="primary" size="sm" icon="arrow-down-tray">
                    <span class="hidden sm:inline">Export</span>
                </flux:button>
                <flux:menu>
                    <flux:menu.item icon="document" :href="route('analytics.export', ['format' => 'pdf', 'period' => $period])">
                        Export as PDF
                    </flux:menu.item>
                    <flux:menu.item icon="table-cells" :href="route('analytics.export', ['format' => 'csv', 'period' => $period])">
                        Export as CSV
                    </flux:menu.item>
                </flux:menu>
            </flux:dropdown>
        </div>
    </div>

    <!-- Key Metrics Overview -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
        <div class="flex items-center space-x-3 mb-6">
            <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                <flux:icon.chart-bar class="w-5 h-5 text-blue-600 dark:text-blue-400" />
            </div>
            <div>
                <flux:heading size="base" class="dark:text-zinc-100">Key Performance Indicators</flux:heading>
                <flux:subheading class="dark:text-zinc-400">Essential metrics for SACCO performance</flux:subheading>
            </div>
            <flux:badge color="blue">{{ ucfirst($period) }}</flux:badge>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
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
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Active Members</p>
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
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Total Assets</p>
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
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Active Loans</p>
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
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">Portfolio Performance</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $overview['portfolio_performance']['value'] }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Analytics Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Financial Trends Chart -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center space-x-3 mb-6">
                <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                    <flux:icon.chart-bar class="w-5 h-5 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <flux:heading size="base" class="dark:text-zinc-100">Financial Trends</flux:heading>
                    <flux:subheading class="dark:text-zinc-400">Monthly performance overview</flux:subheading>
                </div>
                <flux:badge color="blue">{{ ucfirst($period) }}</flux:badge>
            </div>
            <div class="h-80 bg-zinc-50 dark:bg-zinc-700 rounded-lg flex items-center justify-center">
                <div class="text-center">
                    <flux:icon.chart-bar class="w-16 h-16 text-zinc-400 mx-auto mb-4" />
                    <p class="text-zinc-600 dark:text-zinc-400">Chart visualization would be here</p>
                    <p class="text-sm text-zinc-500 dark:text-zinc-500">Interactive charts with Chart.js</p>
                </div>
            </div>
        </div>

        <!-- Account Distribution -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center space-x-3 mb-6">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <flux:icon.chart-pie class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <flux:heading size="base" class="dark:text-zinc-100">Account Distribution</flux:heading>
                    <flux:subheading class="dark:text-zinc-400">Account types and balances</flux:subheading>
                </div>
                <flux:badge color="emerald">Active Accounts</flux:badge>
            </div>
            <div class="h-80 bg-zinc-50 dark:bg-zinc-700 rounded-lg flex items-center justify-center">
                <div class="text-center">
                    <flux:icon.chart-pie class="w-16 h-16 text-zinc-400 mx-auto mb-4" />
                    <p class="text-zinc-600 dark:text-zinc-400">Pie chart visualization would be here</p>
                    <p class="text-sm text-zinc-500 dark:text-zinc-500">Interactive charts with Chart.js</p>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Insights Section -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
        <div class="flex items-center space-x-3 mb-6">
            <div class="p-2 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg">
                <flux:icon.light-bulb class="w-5 h-5 text-yellow-600 dark:text-yellow-400" />
            </div>
            <div>
                <flux:heading size="base" class="dark:text-zinc-100">AI-Powered Insights</flux:heading>
                <flux:subheading class="dark:text-zinc-400">Smart recommendations and analysis</flux:subheading>
            </div>
            <flux:badge color="yellow">Auto-refresh</flux:badge>
        </div>
        <livewire:analytics.insights-widget />
    </div>

    <!-- Advanced Analytics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Financial Health -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center space-x-3 mb-4">
                <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                    <flux:icon.banknotes class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                </div>
                <div>
                    <flux:heading size="sm" class="dark:text-zinc-100">Financial Health</flux:heading>
                </div>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Portfolio Performance</span>
                    <span class="font-medium text-emerald-600 dark:text-emerald-400">{{ $advanced['financial']['profitability_analysis']['roi_percentage'] }}%</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Default Rate</span>
                    <span class="font-medium {{ $advanced['financial']['portfolio_performance']['loan_performance']['default_rate'] < 5 ? 'text-emerald-600' : 'text-red-600' }}">
                        {{ $advanced['financial']['portfolio_performance']['loan_performance']['default_rate'] }}%
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Liquidity Ratio</span>
                    <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $advanced['financial']['risk_assessment']['liquidity_ratio'] }}</span>
                </div>
            </div>
        </div>

        <!-- Member Growth -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center space-x-3 mb-4">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <flux:icon.users class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <flux:heading size="sm" class="dark:text-zinc-100">Member Growth</flux:heading>
                </div>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Retention Rate</span>
                    <span class="font-medium text-emerald-600 dark:text-emerald-400">{{ $advanced['member']['member_growth']['retention_rate'] }}%</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Active Users</span>
                    <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($advanced['member']['engagement_metrics']['active_users']) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Loan Uptake</span>
                    <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $advanced['member']['loan_utilization']['loan_uptake_rate'] }}%</span>
                </div>
            </div>
        </div>

        <!-- Operations -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center space-x-3 mb-4">
                <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                    <flux:icon.cog class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                </div>
                <div>
                    <flux:heading size="sm" class="dark:text-zinc-100">Operations</flux:heading>
                </div>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Success Rate</span>
                    <span class="font-medium text-emerald-600 dark:text-emerald-400">{{ number_format($advanced['operational']['processing_efficiency']['success_rate'], 1) }}%</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Service Uptime</span>
                    <span class="font-medium text-emerald-600 dark:text-emerald-400">{{ $advanced['operational']['service_quality']['service_uptime'] }}%</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Avg Processing</span>
                    <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($advanced['operational']['processing_efficiency']['avg_processing_time'] ?? 0, 1) }}min</span>
                </div>
            </div>
        </div>

        <!-- Revenue Sources -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center space-x-3 mb-4">
                <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                    <flux:icon.currency-dollar class="w-5 h-5 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <flux:heading size="sm" class="dark:text-zinc-100">Revenue Sources</flux:heading>
                </div>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Loan Interest</span>
                    <span class="font-medium text-zinc-900 dark:text-zinc-100">KSh {{ number_format($financial['revenue_streams']['loan_interest'], 0) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Service Fees</span>
                    <span class="font-medium text-zinc-900 dark:text-zinc-100">KSh {{ number_format($financial['revenue_streams']['fees'], 0) }}</span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-zinc-200 dark:border-zinc-700">
                    <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Total</span>
                    <span class="font-bold text-emerald-600 dark:text-emerald-400">
                        KSh {{ number_format($financial['revenue_streams']['loan_interest'] + $financial['revenue_streams']['fees'], 0) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
