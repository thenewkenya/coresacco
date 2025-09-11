<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public $quickStats = [];
    public $recentReports = [];

    public function mount()
    {
        $this->loadReportsData();
    }

    public function loadReportsData()
    {
        // Mock data for demonstration - in real app, this would come from the controller
        $this->quickStats = [
            'total_members' => 1250,
            'total_assets' => 45000000,
            'active_loans' => 340,
            'pending_transactions' => 23
        ];

        $this->recentReports = [
            [
                'name' => 'Monthly Financial Summary - December 2024',
                'type' => 'Financial',
                'generated' => '2024-12-15 10:30',
                'size' => '2.4 MB',
                'format' => 'PDF'
            ],
            [
                'name' => 'Loan Portfolio Analysis - Q4 2024',
                'type' => 'Operational',
                'generated' => '2024-12-14 14:15',
                'size' => '1.8 MB',
                'format' => 'Excel'
            ],
            [
                'name' => 'Member Growth Report - November 2024',
                'type' => 'Membership',
                'generated' => '2024-12-13 09:45',
                'size' => '892 KB',
                'format' => 'PDF'
            ],
            [
                'name' => 'Daily Transaction Summary - December 12, 2024',
                'type' => 'Operational',
                'generated' => '2024-12-12 18:00',
                'size' => '456 KB',
                'format' => 'Excel'
            ]
        ];
    }
}

?><div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" class="!text-zinc-900 dark:!text-zinc-100">Reports</flux:heading>
            <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">Generate comprehensive reports and export data</flux:subheading>
        </div>
        <div class="flex items-center space-x-3">
            <flux:button variant="outline" icon="calendar">
                Schedule Report
            </flux:button>
            <flux:button variant="primary" icon="document-text">
                New Report
            </flux:button>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:heading size="base" class="dark:text-zinc-100">Total Members</flux:heading>
                    <flux:subheading class="dark:text-zinc-400">Active members</flux:subheading>
                </div>
                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <flux:icon.users class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($quickStats['total_members']) }}</div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:heading size="base" class="dark:text-zinc-100">Total Assets</flux:heading>
                    <flux:subheading class="dark:text-zinc-400">SACCO assets</flux:subheading>
                </div>
                <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                    <flux:icon.banknotes class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KSh {{ number_format($quickStats['total_assets'], 2) }}</div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:heading size="base" class="dark:text-zinc-100">Active Loans</flux:heading>
                    <flux:subheading class="dark:text-zinc-400">Current loans</flux:subheading>
                </div>
                <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                    <flux:icon.credit-card class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($quickStats['active_loans']) }}</div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:heading size="base" class="dark:text-zinc-100">Pending Transactions</flux:heading>
                    <flux:subheading class="dark:text-zinc-400">Awaiting approval</flux:subheading>
                </div>
                <div class="p-2 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                    <flux:icon.clock class="w-5 h-5 text-orange-600 dark:text-orange-400" />
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($quickStats['pending_transactions']) }}</div>
            </div>
        </div>
    </div>

    <!-- Quick Reports -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <flux:icon.users class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <flux:button variant="ghost" size="sm" :href="route('reports.members')" icon="arrow-right">
                    Generate
                </flux:button>
            </div>
            <flux:heading size="base" class="dark:text-zinc-100 mb-2">Member Report</flux:heading>
            <flux:subheading class="dark:text-zinc-400 mb-4">Member registration, demographics, and activity</flux:subheading>
            <div class="text-xs text-zinc-500 dark:text-zinc-500">
                Generate member analytics and summaries
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                    <flux:icon.banknotes class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                </div>
                <flux:button variant="ghost" size="sm" :href="route('reports.financial')" icon="arrow-right">
                    Generate
                </flux:button>
            </div>
            <flux:heading size="base" class="dark:text-zinc-100 mb-2">Financial Report</flux:heading>
            <flux:subheading class="dark:text-zinc-400 mb-4">Assets, liabilities, and income statement</flux:subheading>
            <div class="text-xs text-zinc-500 dark:text-zinc-500">
                Generate financial statements and analysis
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                    <flux:icon.credit-card class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                </div>
                <flux:button variant="ghost" size="sm" :href="route('reports.loans')" icon="arrow-right">
                    Generate
                </flux:button>
            </div>
            <flux:heading size="base" class="dark:text-zinc-100 mb-2">Loan Portfolio</flux:heading>
            <flux:subheading class="dark:text-zinc-400 mb-4">Loan performance, arrears, and collections</flux:subheading>
            <div class="text-xs text-zinc-500 dark:text-zinc-500">
                Generate loan portfolio analysis
            </div>
        </div>
    </div>

    <!-- Report Categories -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Financial Reports -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
            <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                        <flux:icon.banknotes class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <div>
                        <flux:heading size="base" class="dark:text-zinc-100">Financial Reports</flux:heading>
                        <flux:subheading class="dark:text-zinc-400">Financial statements and analysis</flux:subheading>
                    </div>
                </div>
            </div>
            <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @foreach([
                    ['name' => 'Trial Balance', 'description' => 'Complete trial balance report', 'format' => 'PDF, Excel', 'type' => 'trial_balance'],
                    ['name' => 'Income Statement', 'description' => 'Profit and loss statement', 'format' => 'PDF, Excel', 'type' => 'income_statement'],
                    ['name' => 'Balance Sheet', 'description' => 'Assets, liabilities and equity', 'format' => 'PDF, Excel', 'type' => 'balance_sheet'],
                    ['name' => 'Cash Flow', 'description' => 'Cash flow statement and analysis', 'format' => 'PDF, Excel', 'type' => 'cash_flow']
                ] as $report)
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $report['name'] }}</p>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $report['description'] }}</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-500">{{ $report['format'] }}</p>
                        </div>
                        <flux:button variant="outline" size="sm" :href="route('reports.financial', ['type' => $report['type']])" icon="arrow-right">
                            Generate
                        </flux:button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Operational Reports -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
            <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                        <flux:icon.cog class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <flux:heading size="base" class="dark:text-zinc-100">Operational Reports</flux:heading>
                        <flux:subheading class="dark:text-zinc-400">Operational metrics and analysis</flux:subheading>
                    </div>
                </div>
            </div>
            <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @foreach([
                    ['name' => 'Member Activity', 'description' => 'Member transaction summary', 'format' => 'PDF, Excel', 'route' => 'reports.members', 'type' => 'activity'],
                    ['name' => 'Loan Arrears', 'description' => 'Overdue loans and collections', 'format' => 'PDF, Excel', 'route' => 'reports.loans', 'type' => 'arrears'],
                    ['name' => 'Daily Transactions', 'description' => 'Daily transaction summary', 'format' => 'PDF, Excel', 'route' => 'reports.operational', 'type' => 'daily_summary'],
                    ['name' => 'Branch Performance', 'description' => 'Branch-wise performance metrics', 'format' => 'PDF, Excel', 'route' => 'reports.operational', 'type' => 'branch_performance']
                ] as $report)
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $report['name'] }}</p>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $report['description'] }}</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-500">{{ $report['format'] }}</p>
                        </div>
                        <flux:button variant="outline" size="sm" :href="route($report['route'], ['type' => $report['type']])" icon="arrow-right">
                            Generate
                        </flux:button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent Reports -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-gray-100 dark:bg-gray-900/30 rounded-lg">
                        <flux:icon.clock class="w-5 h-5 text-gray-600 dark:text-gray-400" />
                    </div>
                    <div>
                        <flux:heading size="base" class="dark:text-zinc-100">Recent Reports</flux:heading>
                        <flux:subheading class="dark:text-zinc-400">Recently generated reports</flux:subheading>
                    </div>
                </div>
                <flux:button variant="ghost" size="sm" icon="eye">
                    View All
                </flux:button>
            </div>
        </div>

        <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
            @foreach($recentReports as $report)
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <flux:icon.document-text class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div>
                            <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $report['name'] }}</p>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $report['type'] }} • {{ $report['format'] }} • {{ $report['size'] }}
                            </p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-500">
                                {{ \Carbon\Carbon::parse($report['generated'])->format('M d, Y g:i A') }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <flux:button variant="ghost" size="sm" icon="eye">
                            View
                        </flux:button>
                        <flux:button variant="outline" size="sm" icon="arrow-down-tray">
                            Download
                        </flux:button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

