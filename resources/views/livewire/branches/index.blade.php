<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public $search = '';
    public $status = '';
    public $city = '';
    public $stats = [];
    public $branchesWithAnalytics = [];
    public $cities = [];

    public function mount()
    {
        $this->loadBranchesData();
    }

    public function loadBranchesData()
    {
        // Mock data for demonstration - in real app, this would come from the controller
        $this->stats = [
            'total_branches' => 8,
            'active_branches' => 7,
            'total_staff' => 45,
            'total_members' => 1250,
            'top_performer' => (object)['name' => 'Nairobi Central']
        ];

        $this->cities = ['Nairobi', 'Mombasa', 'Kisumu', 'Nakuru', 'Eldoret'];

        $this->branchesWithAnalytics = [
            [
                'branch' => (object)[
                    'id' => 1,
                    'name' => 'Nairobi Central',
                    'code' => 'NBI001',
                    'city' => 'Nairobi',
                    'status' => 'active',
                    'manager' => (object)['name' => 'John Mwangi']
                ],
                'performance_score' => 95,
                'total_members' => 320,
                'total_staff' => 12,
                'total_deposits' => 15000000,
                'this_month_transactions' => 1250
            ],
            [
                'branch' => (object)[
                    'id' => 2,
                    'name' => 'Mombasa Port',
                    'code' => 'MOM001',
                    'city' => 'Mombasa',
                    'status' => 'active',
                    'manager' => (object)['name' => 'Sarah Hassan']
                ],
                'performance_score' => 88,
                'total_members' => 280,
                'total_staff' => 10,
                'total_deposits' => 12000000,
                'this_month_transactions' => 980
            ],
            [
                'branch' => (object)[
                    'id' => 3,
                    'name' => 'Kisumu Lake',
                    'code' => 'KIS001',
                    'city' => 'Kisumu',
                    'status' => 'active',
                    'manager' => (object)['name' => 'Peter Ochieng']
                ],
                'performance_score' => 82,
                'total_members' => 200,
                'total_staff' => 8,
                'total_deposits' => 8500000,
                'this_month_transactions' => 750
            ],
            [
                'branch' => (object)[
                    'id' => 4,
                    'name' => 'Nakuru Rift',
                    'code' => 'NAK001',
                    'city' => 'Nakuru',
                    'status' => 'under_maintenance',
                    'manager' => (object)['name' => 'Mary Wanjiku']
                ],
                'performance_score' => 65,
                'total_members' => 150,
                'total_staff' => 6,
                'total_deposits' => 6000000,
                'this_month_transactions' => 420
            ]
        ];
    }

    public function filter()
    {
        // In a real app, this would filter the branches based on search, status, and city
        $this->loadBranchesData();
    }
}

?><div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" class="!text-zinc-900 dark:!text-zinc-100">Branch Management</flux:heading>
            <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">Manage SACCO branches and their operations</flux:subheading>
        </div>
        @if(auth()->user()->hasRole('admin'))
            <flux:button variant="primary" icon="plus" :href="route('branches.create')">
                Add Branch
            </flux:button>
        @endif
    </div>

    <!-- Search and Filters -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
        <form wire:submit="filter" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <flux:field>
                <flux:label>Search branches</flux:label>
                <flux:input 
                    type="text" 
                    wire:model="search"
                    placeholder="Search branches..." 
                    icon="magnifying-glass"
                />
            </flux:field>
            
            <flux:field>
                <flux:label>Status</flux:label>
                <flux:select wire:model="status">
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="under_maintenance">Under Maintenance</option>
                </flux:select>
            </flux:field>
            
            <flux:field>
                <flux:label>City</flux:label>
                <flux:select wire:model="city">
                    <option value="">All Cities</option>
                    @foreach($cities as $cityOption)
                        <option value="{{ $cityOption }}">{{ $cityOption }}</option>
                    @endforeach
                </flux:select>
            </flux:field>
            
            <div class="flex items-end space-x-2">
                <flux:button type="submit" variant="primary" class="flex-1">
                    Filter
                </flux:button>
                <flux:button type="button" variant="outline" wire:click="$set('search', ''); $set('status', ''); $set('city', '');">
                    Clear
                </flux:button>
            </div>
        </form>
    </div>

    <!-- Branch Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <flux:icon.building-office-2 class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <flux:badge color="blue">Total</flux:badge>
            </div>
            <flux:heading size="base" class="dark:text-zinc-100 mb-1">Total Branches</flux:heading>
            <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mb-1">{{ $stats['total_branches'] }}</div>
            <flux:subheading class="dark:text-zinc-400">{{ $stats['active_branches'] }} Active</flux:subheading>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                    <flux:icon.users class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                </div>
                <flux:badge color="emerald">Staff</flux:badge>
            </div>
            <flux:heading size="base" class="dark:text-zinc-100 mb-1">Staff Members</flux:heading>
            <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mb-1">{{ number_format($stats['total_staff']) }}</div>
            <flux:subheading class="dark:text-zinc-400">All branches</flux:subheading>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                    <flux:icon.user-group class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                </div>
                <flux:badge color="purple">Members</flux:badge>
            </div>
            <flux:heading size="base" class="dark:text-zinc-100 mb-1">Total Members</flux:heading>
            <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mb-1">{{ number_format($stats['total_members']) }}</div>
            <flux:subheading class="dark:text-zinc-400">All branches</flux:subheading>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                    <flux:icon.star class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                </div>
                <flux:badge color="amber">Performance</flux:badge>
            </div>
            <flux:heading size="base" class="dark:text-zinc-100 mb-1">Top Performer</flux:heading>
            <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mb-1">{{ $stats['top_performer']->name ?? 'N/A' }}</div>
            <flux:subheading class="dark:text-zinc-400">This month</flux:subheading>
        </div>
    </div>

    <!-- Branch Listing -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                        <flux:icon.building-office-2 class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <flux:heading size="base" class="dark:text-zinc-100">All Branches</flux:heading>
                        <flux:subheading class="dark:text-zinc-400">{{ count($branchesWithAnalytics) }} branches found</flux:subheading>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <flux:button variant="ghost" size="sm" icon="map" :href="route('branches.map')">
                        Map View
                    </flux:button>
                    <flux:button variant="ghost" size="sm" icon="chart-bar" :href="route('reports.operational', ['type' => 'branch_performance'])">
                        Performance Report
                    </flux:button>
                </div>
            </div>
        </div>

        @if(empty($branchesWithAnalytics))
            <div class="p-12 text-center">
                <flux:icon.building-office-2 class="w-12 h-12 text-zinc-400 dark:text-zinc-600 mx-auto mb-4" />
                <flux:heading size="base" class="dark:text-zinc-100 mb-2">No branches found</flux:heading>
                <flux:subheading class="dark:text-zinc-400 mb-4">No branches match your current filter criteria.</flux:subheading>
                @if(auth()->user()->hasRole('admin'))
                    <flux:button variant="primary" :href="route('branches.create')">
                        Create First Branch
                    </flux:button>
                @endif
            </div>
        @else
            <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @foreach($branchesWithAnalytics as $data)
                    @php $branch = $data['branch'] @endphp
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                    <flux:icon.building-office-2 class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                                </div>
                                <div>
                                    <div class="flex items-center space-x-3 mb-2">
                                        <flux:heading size="base" class="dark:text-zinc-100">{{ $branch->name }}</flux:heading>
                                        <flux:badge 
                                            color="@if($branch->status === 'active') emerald @elseif($branch->status === 'inactive') gray @else amber @endif"
                                        >
                                            {{ ucfirst(str_replace('_', ' ', $branch->status)) }}
                                        </flux:badge>
                                        <div class="flex items-center space-x-1">
                                            <div class="w-16 bg-zinc-200 dark:bg-zinc-600 rounded-full h-1.5">
                                                <div class="h-1.5 rounded-full 
                                                    @if($data['performance_score'] >= 80) bg-emerald-500
                                                    @elseif($data['performance_score'] >= 60) bg-blue-500
                                                    @elseif($data['performance_score'] >= 40) bg-amber-500
                                                    @else bg-red-500
                                                    @endif" 
                                                    style="width: {{ $data['performance_score'] }}%"></div>
                                            </div>
                                            <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ number_format($data['performance_score'], 0) }}%</span>
                                        </div>
                                    </div>
                                    <flux:subheading class="dark:text-zinc-400 mb-1">{{ $branch->code }} • {{ $branch->city }}</flux:subheading>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-500">
                                        Manager: {{ $branch->manager->name ?? 'Unassigned' }}
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="grid grid-cols-4 gap-4 text-center mb-3">
                                    <div>
                                        <div class="text-lg font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($data['total_members']) }}</div>
                                        <div class="text-xs text-zinc-600 dark:text-zinc-400">Members</div>
                                    </div>
                                    <div>
                                        <div class="text-lg font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($data['total_staff']) }}</div>
                                        <div class="text-xs text-zinc-600 dark:text-zinc-400">Staff</div>
                                    </div>
                                    <div>
                                        <div class="text-lg font-bold text-emerald-600 dark:text-emerald-400">KES {{ number_format($data['total_deposits'] / 1000, 0) }}K</div>
                                        <div class="text-xs text-zinc-600 dark:text-zinc-400">Deposits</div>
                                    </div>
                                    <div>
                                        <div class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ number_format($data['this_month_transactions']) }}</div>
                                        <div class="text-xs text-zinc-600 dark:text-zinc-400">Transactions</div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <flux:button variant="outline" size="sm" :href="route('branches.show', $branch->id)">
                                        View Details
                                    </flux:button>
                                    @if(auth()->user()->hasRole('admin') || auth()->user()->branch_id === $branch->id)
                                        <flux:button variant="ghost" size="sm" :href="route('branches.edit', $branch->id)">
                                            Edit
                                        </flux:button>
                                    @endif
                                    @if(auth()->user()->hasAnyRole(['admin', 'manager']))
                                        <flux:button variant="ghost" size="sm" :href="route('branches.staff', $branch->id)">
                                            Staff
                                        </flux:button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
