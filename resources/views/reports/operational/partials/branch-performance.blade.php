<div class="space-y-6">
    <!-- Overall Statistics -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-6">{{ __('Branch Network Overview') }}</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="text-center">
                <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $overallStats['total_branches'] }}</p>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Total Branches') }}</p>
                <p class="text-xs text-green-600 dark:text-green-400 mt-1">{{ $overallStats['active_branches'] }} {{ __('Active') }}</p>
            </div>
            
            <div class="text-center">
                <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ number_format($overallStats['total_members']) }}</p>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Total Members') }}</p>
                <p class="text-xs text-zinc-500 dark:text-zinc-500 mt-1">{{ __('Across all branches') }}</p>
            </div>
            
            <div class="text-center">
                <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ __('KES') }} {{ number_format($overallStats['total_deposits'], 0) }}</p>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Total Deposits') }}</p>
                <p class="text-xs text-zinc-500 dark:text-zinc-500 mt-1">{{ __('Combined portfolio') }}</p>
            </div>
            
            <div class="text-center">
                <p class="text-3xl font-bold text-orange-600 dark:text-orange-400">{{ number_format($overallStats['avg_performance_score'], 1) }}%</p>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Avg Performance Score') }}</p>
                <p class="text-xs text-zinc-500 dark:text-zinc-500 mt-1">{{ __('Network average') }}</p>
            </div>
        </div>
    </div>

    <!-- Top Performers Section -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-6">{{ __('Top Performing Branches') }}</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20 rounded-lg p-4 border border-yellow-200 dark:border-yellow-800">
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <h4 class="font-medium text-yellow-800 dark:text-yellow-200">{{ __('Best Overall') }}</h4>
                </div>
                @if($overallStats['top_performing_branch'])
                    <p class="font-bold text-yellow-900 dark:text-yellow-100">{{ $overallStats['top_performing_branch']['branch']['name'] }}</p>
                    <p class="text-sm text-yellow-700 dark:text-yellow-300">{{ number_format($overallStats['top_performing_branch']['performance_score'], 1) }}% {{ __('Score') }}</p>
                @endif
            </div>
            
            <div class="bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-lg p-4 border border-green-200 dark:border-green-800">
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"/>
                    </svg>
                    <h4 class="font-medium text-green-800 dark:text-green-200">{{ __('Highest Deposits') }}</h4>
                </div>
                @if($overallStats['most_deposits_branch'])
                    <p class="font-bold text-green-900 dark:text-green-100">{{ $overallStats['most_deposits_branch']['branch']['name'] }}</p>
                    <p class="text-sm text-green-700 dark:text-green-300">{{ __('KES') }} {{ number_format($overallStats['most_deposits_branch']['total_deposits'], 0) }}</p>
                @endif
            </div>
            
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h4 class="font-medium text-blue-800 dark:text-blue-200">{{ __('Most Active') }}</h4>
                </div>
                @if($overallStats['most_active_branch'])
                    <p class="font-bold text-blue-900 dark:text-blue-100">{{ $overallStats['most_active_branch']['branch']['name'] }}</p>
                    <p class="text-sm text-blue-700 dark:text-blue-300">{{ number_format($overallStats['most_active_branch']['transaction_count']) }} {{ __('Transactions') }}</p>
                @endif
            </div>
            
            <div class="bg-gradient-to-r from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-lg p-4 border border-purple-200 dark:border-purple-800">
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                    </svg>
                    <h4 class="font-medium text-purple-800 dark:text-purple-200">{{ __('Fastest Growing') }}</h4>
                </div>
                @if($overallStats['fastest_growing_branch'])
                    <p class="font-bold text-purple-900 dark:text-purple-100">{{ $overallStats['fastest_growing_branch']['branch']['name'] }}</p>
                    <p class="text-sm text-purple-700 dark:text-purple-300">+{{ $overallStats['fastest_growing_branch']['new_members'] }} {{ __('New Members') }}</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Performance Rankings Tabs -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
        <div class="border-b border-zinc-200 dark:border-zinc-700">
            <nav class="flex space-x-8 px-6">
                <flux:button 
                    class="ranking-tab-btn border-b-2 border-blue-500 text-blue-600 dark:text-blue-400 py-4 px-1 text-sm font-medium" 
                    data-tab="performance"
                    variant="ghost"
                    size="sm"
                >
                    {{ __('Performance Ranking') }}
                </flux:button>
                <flux:button 
                    class="ranking-tab-btn border-b-2 border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300 py-4 px-1 text-sm font-medium" 
                    data-tab="deposits"
                    variant="ghost"
                    size="sm"
                >
                    {{ __('By Deposits') }}
                </flux:button>
                <flux:button 
                    class="ranking-tab-btn border-b-2 border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300 py-4 px-1 text-sm font-medium" 
                    data-tab="members"
                    variant="ghost"
                    size="sm"
                >
                    {{ __('By Members') }}
                </flux:button>
                <flux:button 
                    class="ranking-tab-btn border-b-2 border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300 py-4 px-1 text-sm font-medium" 
                    data-tab="growth"
                    variant="ghost"
                    size="sm"
                >
                    {{ __('By Growth') }}
                </flux:button>
            </nav>
        </div>

        <!-- Performance Ranking Tab -->
        <div id="performance-tab" class="ranking-tab-content p-6">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Overall Performance Ranking') }}</h3>
            <div class="space-y-3">
                @foreach($rankings['by_performance'] as $index => $branch)
                    <div class="flex items-center justify-between p-4 bg-zinc-50 dark:bg-zinc-700 rounded-lg border border-zinc-200 dark:border-zinc-600">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                @if($index === 0)
                                    <div class="w-8 h-8 bg-yellow-500 text-white rounded-full flex items-center justify-center text-sm font-bold">1</div>
                                @elseif($index === 1)
                                    <div class="w-8 h-8 bg-gray-400 text-white rounded-full flex items-center justify-center text-sm font-bold">2</div>
                                @elseif($index === 2)
                                    <div class="w-8 h-8 bg-orange-600 text-white rounded-full flex items-center justify-center text-sm font-bold">3</div>
                                @else
                                    <div class="w-8 h-8 bg-zinc-400 text-white rounded-full flex items-center justify-center text-sm font-bold">{{ $index + 1 }}</div>
                                @endif
                            </div>
                            <div>
                                <h4 class="font-medium text-zinc-900 dark:text-zinc-100">{{ $branch['branch']['name'] }}</h4>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $branch['branch']['city'] }} • {{ $branch['branch']['manager']['name'] ?? __('No Manager') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="flex items-center space-x-3">
                                <div class="w-32 bg-zinc-200 dark:bg-zinc-600 rounded-full h-2">
                                    <div class="h-2 rounded-full bg-gradient-to-r from-blue-400 to-blue-600" style="width: {{ $branch['performance_score'] }}%"></div>
                                </div>
                                <span class="text-lg font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($branch['performance_score'], 1) }}%</span>
                            </div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">{{ $branch['performance_rating'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Deposits Ranking Tab -->
        <div id="deposits-tab" class="ranking-tab-content p-6 hidden">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Ranking by Total Deposits') }}</h3>
            <div class="space-y-3">
                @foreach($rankings['by_deposits'] as $index => $branch)
                    <div class="flex items-center justify-between p-4 bg-zinc-50 dark:bg-zinc-700 rounded-lg border border-zinc-200 dark:border-zinc-600">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-bold">{{ $index + 1 }}</div>
                            </div>
                            <div>
                                <h4 class="font-medium text-zinc-900 dark:text-zinc-100">{{ $branch['branch']['name'] }}</h4>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $branch['total_accounts'] }} {{ __('accounts') }} • {{ $branch['total_members'] }} {{ __('members') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xl font-bold text-green-600 dark:text-green-400">{{ __('KES') }} {{ number_format($branch['total_deposits'], 0) }}</p>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Avg per member:') }} {{ __('KES') }} {{ $branch['total_members'] > 0 ? number_format($branch['total_deposits'] / $branch['total_members'], 0) : '0' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Members Ranking Tab -->
        <div id="members-tab" class="ranking-tab-content p-6 hidden">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Ranking by Member Count') }}</h3>
            <div class="space-y-3">
                @foreach($rankings['by_members'] as $index => $branch)
                    <div class="flex items-center justify-between p-4 bg-zinc-50 dark:bg-zinc-700 rounded-lg border border-zinc-200 dark:border-zinc-600">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold">{{ $index + 1 }}</div>
                            </div>
                            <div>
                                <h4 class="font-medium text-zinc-900 dark:text-zinc-100">{{ $branch['branch']['name'] }}</h4>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $branch['branch']['city'] }} • {{ $branch['staff_count'] }} {{ __('staff members') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($branch['total_members']) }}</p>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">+{{ $branch['new_members'] }} {{ __('this period') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Growth Ranking Tab -->
        <div id="growth-tab" class="ranking-tab-content p-6 hidden">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Ranking by Member Growth') }}</h3>
            <div class="space-y-3">
                @foreach($rankings['by_growth'] as $index => $branch)
                    <div class="flex items-center justify-between p-4 bg-zinc-50 dark:bg-zinc-700 rounded-lg border border-zinc-200 dark:border-zinc-600">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-purple-500 text-white rounded-full flex items-center justify-center text-sm font-bold">{{ $index + 1 }}</div>
                            </div>
                            <div>
                                <h4 class="font-medium text-zinc-900 dark:text-zinc-100">{{ $branch['branch']['name'] }}</h4>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $branch['total_members'] }} {{ __('total members') }} • {{ number_format($branch['performance_score'], 1) }}% {{ __('score') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xl font-bold text-purple-600 dark:text-purple-400">+{{ $branch['new_members'] }}</p>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('New members this period') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Detailed Branch Performance Table -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Detailed Branch Analysis') }}</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Branch') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Performance') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Members') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Deposits') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Loans') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Transactions') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach($branchPerformance as $branch)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                            {{ $branch['branch']['name'] }}
                                        </div>
                                        <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                            {{ $branch['branch']['city'] }} • {{ $branch['branch']['code'] }}
                                        </div>
                                        <div class="text-xs text-zinc-400 dark:text-zinc-500">
                                            {{ __('Manager:') }} {{ $branch['manager']['name'] ?? __('Unassigned') }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-16 bg-zinc-200 dark:bg-zinc-600 rounded-full h-2 mr-3">
                                        <div class="h-2 rounded-full 
                                            @if($branch['performance_score'] >= 90) bg-gradient-to-r from-green-400 to-green-600
                                            @elseif($branch['performance_score'] >= 80) bg-gradient-to-r from-blue-400 to-blue-600
                                            @elseif($branch['performance_score'] >= 70) bg-gradient-to-r from-yellow-400 to-yellow-600
                                            @elseif($branch['performance_score'] >= 60) bg-gradient-to-r from-orange-400 to-orange-600
                                            @else bg-gradient-to-r from-red-400 to-red-600
                                            @endif" 
                                            style="width: {{ $branch['performance_score'] }}%"></div>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($branch['performance_score'], 1) }}%</div>
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $branch['performance_rating'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($branch['total_members']) }}</div>
                                <div class="text-sm text-zinc-500 dark:text-zinc-400">+{{ $branch['new_members'] }} {{ __('new') }}</div>
                                <div class="text-xs text-zinc-400 dark:text-zinc-500">{{ $branch['total_accounts'] }} {{ __('accounts') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ __('KES') }} {{ number_format($branch['total_deposits'], 0) }}</div>
                                <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Avg:') }} {{ __('KES') }} {{ $branch['total_members'] > 0 ? number_format($branch['total_deposits'] / $branch['total_members'], 0) : '0' }}</div>
                                <div class="text-xs 
                                    @if($branch['net_cash_flow'] > 0) text-green-600 dark:text-green-400
                                    @elseif($branch['net_cash_flow'] < 0) text-red-600 dark:text-red-400
                                    @else text-zinc-400 dark:text-zinc-500
                                    @endif">
                                    {{ __('Net flow:') }} {{ __('KES') }} {{ number_format($branch['net_cash_flow'], 0) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($branch['loans']['total_portfolio'], 0) }}</div>
                                <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $branch['loans']['active_loans'] }} {{ __('active') }}</div>
                                <div class="text-xs 
                                    @if($branch['loans']['overdue_loans'] == 0) text-green-600 dark:text-green-400
                                    @elseif($branch['loans']['overdue_loans'] <= 2) text-yellow-600 dark:text-yellow-400
                                    @else text-red-600 dark:text-red-400
                                    @endif">
                                    {{ $branch['loans']['overdue_loans'] }} {{ __('overdue') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($branch['transaction_count']) }}</div>
                                <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('KES') }} {{ number_format($branch['transaction_volume'], 0) }}</div>
                                <div class="text-xs text-zinc-400 dark:text-zinc-500">{{ __('This period') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($branch['is_active'])
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        {{ __('Active') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                        {{ ucfirst($branch['branch']['status']) }}
                                    </span>
                                @endif
                                <div class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">{{ $branch['staff_count'] }} {{ __('staff') }}</div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Tab switching functionality
    document.addEventListener('DOMContentLoaded', function() {
        const tabButtons = document.querySelectorAll('.ranking-tab-btn');
        const tabContents = document.querySelectorAll('.ranking-tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const tabName = this.getAttribute('data-tab');
                
                // Update button styles
                tabButtons.forEach(btn => {
                    btn.classList.remove('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
                    btn.classList.add('border-transparent', 'text-zinc-500', 'hover:text-zinc-700', 'dark:text-zinc-400', 'dark:hover:text-zinc-300');
                });
                this.classList.remove('border-transparent', 'text-zinc-500', 'hover:text-zinc-700', 'dark:text-zinc-400', 'dark:hover:text-zinc-300');
                this.classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
                
                // Show/hide content
                tabContents.forEach(content => {
                    content.classList.add('hidden');
                });
                document.getElementById(tabName + '-tab').classList.remove('hidden');
            });
        });
    });
</script> 