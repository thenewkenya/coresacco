<!-- Hourly Analysis Report -->
<div class="space-y-6">
    <!-- Hourly Overview -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Transaction Activity by Hour') }}</h3>
        
        <!-- Peak Hours Summary -->
        @php
            $peakHour = collect($hourlyData)->sortByDesc('transaction_count')->first();
            $quietHour = collect($hourlyData)->where('transaction_count', '>', 0)->sortBy('transaction_count')->first();
            $totalTransactions = collect($hourlyData)->sum('transaction_count');
            $totalAmount = collect($hourlyData)->sum('total_amount');
        @endphp
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $peakHour ? $peakHour['formatted_hour'] : 'N/A' }}</p>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Peak Hour') }}</p>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $peakHour ? number_format($peakHour['transaction_count']) . ' transactions' : '' }}</p>
            </div>
            <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $quietHour ? $quietHour['formatted_hour'] : 'N/A' }}</p>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Quietest Hour') }}</p>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $quietHour ? number_format($quietHour['transaction_count']) . ' transactions' : '' }}</p>
            </div>
            <div class="text-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($totalTransactions) }}</p>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Total Transactions') }}</p>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('All hours combined') }}</p>
            </div>
            <div class="text-center p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">KES {{ number_format($totalAmount, 0) }}</p>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Total Amount') }}</p>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('All hours combined') }}</p>
            </div>
        </div>
    </div>

    <!-- Hourly Breakdown Table -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Detailed Hourly Breakdown') }}</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Hour') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Transactions') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Total Amount') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Avg Amount') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Deposits') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Withdrawals') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Loan Transactions') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Activity Level') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach($hourlyData as $hour)
                        @php
                            $activityLevel = 'Low';
                            $activityColor = 'text-zinc-500 dark:text-zinc-400';
                            $activityBg = 'bg-zinc-100 dark:bg-zinc-700';
                            
                            if ($hour['transaction_count'] > 0) {
                                $percentage = ($hour['transaction_count'] / max(1, $totalTransactions)) * 100;
                                if ($percentage >= 10) {
                                    $activityLevel = 'Very High';
                                    $activityColor = 'text-red-600 dark:text-red-400';
                                    $activityBg = 'bg-red-100 dark:bg-red-900/20';
                                } elseif ($percentage >= 7) {
                                    $activityLevel = 'High';
                                    $activityColor = 'text-orange-600 dark:text-orange-400';
                                    $activityBg = 'bg-orange-100 dark:bg-orange-900/20';
                                } elseif ($percentage >= 4) {
                                    $activityLevel = 'Medium';
                                    $activityColor = 'text-yellow-600 dark:text-yellow-400';
                                    $activityBg = 'bg-yellow-100 dark:bg-yellow-900/20';
                                } elseif ($percentage >= 1) {
                                    $activityLevel = 'Low';
                                    $activityColor = 'text-blue-600 dark:text-blue-400';
                                    $activityBg = 'bg-blue-100 dark:bg-blue-900/20';
                                }
                            }
                        @endphp
                        
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $hour['formatted_hour'] }}</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ sprintf('%02d', $hour['hour']) }}:00 - {{ sprintf('%02d', ($hour['hour'] + 1) % 24) }}:00
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($hour['transaction_count']) }}</div>
                                @if($totalTransactions > 0)
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ round(($hour['transaction_count'] / $totalTransactions) * 100, 1) }}% of total
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                KES {{ number_format($hour['total_amount'], 0) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-400">
                                KES {{ number_format($hour['avg_amount'], 0) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    {{ number_format($hour['deposits']) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    {{ number_format($hour['withdrawals']) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    {{ number_format($hour['loan_transactions']) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $activityBg }} {{ $activityColor }}">
                                    {{ $activityLevel }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Time Period Analysis -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Business Hours Analysis -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Business Hours Analysis') }}</h3>
            
            @php
                $businessHours = collect($hourlyData)->whereBetween('hour', [8, 17]); // 8 AM to 5 PM
                $afterHours = collect($hourlyData)->filter(function($hour) {
                    return $hour['hour'] < 8 || $hour['hour'] > 17;
                });
                
                $businessTransactions = $businessHours->sum('transaction_count');
                $afterHoursTransactions = $afterHours->sum('transaction_count');
                $businessAmount = $businessHours->sum('total_amount');
                $afterHoursAmount = $afterHours->sum('total_amount');
            @endphp
            
            <div class="space-y-4">
                <div class="p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Business Hours (8 AM - 5 PM)') }}</span>
                        <span class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ number_format($businessTransactions) }} transactions</span>
                    </div>
                    <div class="mt-2">
                        <p class="text-lg font-bold text-green-600 dark:text-green-400">KES {{ number_format($businessAmount, 0) }}</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                            {{ $totalTransactions > 0 ? round(($businessTransactions / $totalTransactions) * 100, 1) : 0 }}% of total transactions
                        </p>
                    </div>
                </div>
                
                <div class="p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('After Hours (6 PM - 7 AM)') }}</span>
                        <span class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ number_format($afterHoursTransactions) }} transactions</span>
                    </div>
                    <div class="mt-2">
                        <p class="text-lg font-bold text-blue-600 dark:text-blue-400">KES {{ number_format($afterHoursAmount, 0) }}</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                            {{ $totalTransactions > 0 ? round(($afterHoursTransactions / $totalTransactions) * 100, 1) : 0 }}% of total transactions
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Peak Periods -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Peak Activity Periods') }}</h3>
            
            @php
                $sortedHours = collect($hourlyData)->sortByDesc('transaction_count')->take(5);
            @endphp
            
            <div class="space-y-3">
                @foreach($sortedHours as $index => $hour)
                    @if($hour['transaction_count'] > 0)
                        <div class="flex items-center justify-between p-3 hover:bg-zinc-50 dark:hover:bg-zinc-700 rounded">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-medium text-blue-600 dark:text-blue-400">{{ $index + 1 }}</span>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $hour['formatted_hour'] }}</p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ $hour['deposits'] }} deposits, {{ $hour['withdrawals'] }} withdrawals
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($hour['transaction_count']) }}</p>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">KES {{ number_format($hour['total_amount'], 0) }}</p>
                            </div>
                        </div>
                    @endif
                @endforeach
                
                @if($sortedHours->where('transaction_count', '>', 0)->isEmpty())
                    <div class="text-center py-4 text-zinc-500 dark:text-zinc-400">
                        {{ __('No transaction activity recorded for the selected period.') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Transaction Pattern Insights -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Activity Pattern Insights') }}</h3>
        
        @php
            $morningHours = collect($hourlyData)->whereBetween('hour', [6, 11]);
            $afternoonHours = collect($hourlyData)->whereBetween('hour', [12, 17]);
            $eveningHours = collect($hourlyData)->whereBetween('hour', [18, 23]);
            $nightHours = collect($hourlyData)->filter(function($hour) {
                return $hour['hour'] >= 0 && $hour['hour'] <= 5;
            });
            
            $periods = [
                'Morning (6 AM - 11 AM)' => $morningHours,
                'Afternoon (12 PM - 5 PM)' => $afternoonHours,
                'Evening (6 PM - 11 PM)' => $eveningHours,
                'Night (12 AM - 5 AM)' => $nightHours
            ];
        @endphp
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($periods as $periodName => $periodData)
                @php
                    $periodTransactions = $periodData->sum('transaction_count');
                    $periodAmount = $periodData->sum('total_amount');
                    $periodAvg = $periodData->avg('transaction_count');
                @endphp
                
                <div class="p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg">
                    <h4 class="font-medium text-zinc-900 dark:text-zinc-100 mb-2">{{ $periodName }}</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Transactions') }}</span>
                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($periodTransactions) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Amount') }}</span>
                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">KES {{ number_format($periodAmount, 0) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Avg/Hour') }}</span>
                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($periodAvg, 1) }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div> 