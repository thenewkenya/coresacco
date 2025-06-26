<x-layouts.app :title="__('Loan Reports')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('Loan Reports') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Comprehensive loan portfolio analysis and risk management') }}
                        </p>
                    </div>
                    <div class="flex space-x-3">
                        <flux:button variant="outline" href="{{ url()->current() }}?{{ http_build_query(array_merge(request()->all(), ['format' => 'pdf'])) }}" target="_blank">
                            {{ __('Export PDF') }}
                        </flux:button>
                        <flux:button variant="outline" href="{{ url()->current() }}?{{ http_build_query(array_merge(request()->all(), ['format' => 'excel'])) }}">
                            {{ __('Export Excel') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Controls -->
        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 mb-6">
                <form method="GET" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                        <!-- Report Type -->
                        <div>
                            <flux:select name="type" label="{{ __('Report Type') }}">
                                <option value="portfolio" {{ ($report_type ?? 'portfolio') === 'portfolio' ? 'selected' : '' }}>{{ __('Portfolio Overview') }}</option>
                                <option value="arrears" {{ ($report_type ?? '') === 'arrears' ? 'selected' : '' }}>{{ __('Arrears Analysis') }}</option>
                                <option value="performance" {{ ($report_type ?? '') === 'performance' ? 'selected' : '' }}>{{ __('Performance Metrics') }}</option>
                                <option value="collections" {{ ($report_type ?? '') === 'collections' ? 'selected' : '' }}>{{ __('Collections Report') }}</option>
                                <option value="risk_analysis" {{ ($report_type ?? '') === 'risk_analysis' ? 'selected' : '' }}>{{ __('Risk Analysis') }}</option>
                                <option value="profitability" {{ ($report_type ?? '') === 'profitability' ? 'selected' : '' }}>{{ __('Profitability Analysis') }}</option>
                            </flux:select>
                        </div>

                        <!-- Date Range -->
                        <div>
                            <flux:input type="date" name="start_date" label="{{ __('Start Date') }}" value="{{ $start_date ?? now()->startOfMonth()->format('Y-m-d') }}" />
                        </div>
                        <div>
                            <flux:input type="date" name="end_date" label="{{ __('End Date') }}" value="{{ $end_date ?? now()->endOfMonth()->format('Y-m-d') }}" />
                        </div>

                        <!-- Filters -->
                        <div>
                            <flux:select name="loan_type_id" label="{{ __('Loan Type') }}">
                                <option value="">{{ __('All Types') }}</option>
                                @foreach($loan_types as $type)
                                    <option value="{{ $type->id }}" {{ ($loan_type_id ?? '') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                @endforeach
                            </flux:select>
                        </div>

                        <div>
                            <flux:select name="branch_id" label="{{ __('Branch') }}">
                                <option value="">{{ __('All Branches') }}</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ ($branch_id ?? '') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                @endforeach
                            </flux:select>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <flux:button type="submit">{{ __('Generate Report') }}</flux:button>
                    </div>
                </form>
            </div>

            <!-- Report Content -->
            @if(isset($report_type))
                @if($report_type === 'portfolio')
                    @include('reports.loans.partials.portfolio')
                @elseif($report_type === 'arrears')
                    @include('reports.loans.partials.arrears')
                @elseif($report_type === 'performance')
                    @include('reports.loans.partials.performance')
                @elseif($report_type === 'collections')
                    @include('reports.loans.partials.collections')
                @elseif($report_type === 'risk_analysis')
                    @include('reports.loans.partials.risk-analysis')
                @elseif($report_type === 'profitability')
                    @include('reports.loans.partials.profitability')
                @endif
            @else
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-12 text-center">
                    <div class="text-zinc-400 mb-4">
                        <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">{{ __('Select Report Parameters') }}</h3>
                    <p class="text-zinc-600 dark:text-zinc-400">{{ __('Choose a report type and filters above to generate your loan analysis') }}</p>
                </div>
            @endif
        </div>
    </div>

    @if(isset($generated_at))
        <div class="fixed bottom-4 right-4 bg-white dark:bg-zinc-800 rounded-lg shadow-lg p-3 text-xs text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700">
            {{ __('Generated at') }}: {{ $generated_at->format('M j, Y g:i A') }}
        </div>
    @endif
</x-layouts.app> 