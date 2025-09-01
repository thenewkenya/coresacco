<x-layouts.app :title="__('Loan Applications')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('Loan Applications') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Manage loan applications with borrowing criteria') }}
                        </p>
                    </div>
                    <flux:button variant="primary" :href="route('loan-applications.create')" icon="plus">
                        {{ __('New Application') }}
                    </flux:button>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <!-- Filters -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <flux:field>
                        <flux:label>{{ __('Status') }}</flux:label>
                        <flux:select id="statusFilter">
                            <option value="">{{ __('All Statuses') }}</option>
                            <option value="pending">{{ __('Pending') }}</option>
                            <option value="approved">{{ __('Approved') }}</option>
                            <option value="rejected">{{ __('Rejected') }}</option>
                        </flux:select>
                    </flux:field>
                    <flux:field>
                        <flux:label>{{ __('Member') }}</flux:label>
                        <flux:input type="text" id="memberFilter" placeholder="{{ __('Search by member name...') }}" />
                    </flux:field>
                    <flux:field>
                        <flux:label>{{ __('Date Range') }}</flux:label>
                        <flux:input type="date" id="dateFrom" />
                    </flux:field>
                    <flux:field>
                        <flux:label>{{ __('To') }}</flux:label>
                        <flux:input type="date" id="dateTo" />
                    </flux:field>
                </div>
            </div>

            <!-- Applications Table -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                        <thead class="bg-zinc-50 dark:bg-zinc-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    {{ __('Application') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    {{ __('Member') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    {{ __('Amount') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    {{ __('Status') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    {{ __('Criteria') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    {{ __('Guarantors') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    {{ __('Date') }}
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    {{ __('Actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                            @forelse($loans as $loan)
                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                            #{{ $loan->id }}
                                        </div>
                                        <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                            {{ $loan->loanType->name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                            {{ $loan->member->name }}
                                        </div>
                                        <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                            {{ $loan->member->email }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                            KES {{ number_format($loan->amount, 2) }}
                                        </div>
                                        <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                            {{ $loan->term_period }} months
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($loan->status === 'approved') bg-green-100 text-green-800
                                            @elseif($loan->status === 'rejected') bg-red-100 text-red-800
                                            @else bg-yellow-100 text-yellow-800 @endif">
                                            {{ ucfirst($loan->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex space-x-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                @if($loan->meets_savings_criteria) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                                                {{ $loan->meets_savings_criteria ? '✓' : '✗' }}
                                            </span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                @if($loan->meets_membership_criteria) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                                                {{ $loan->meets_membership_criteria ? '✓' : '✗' }}
                                            </span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                @if($loan->meets_guarantor_criteria) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                                                {{ $loan->meets_guarantor_criteria ? '✓' : '✗' }}
                                            </span>
                                        </div>
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                                            S/M/G
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                            {{ $loan->approved_guarantors }}/{{ $loan->required_guarantors }}
                                        </div>
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                            {{ __('Approved') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                        {{ $loan->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <flux:button variant="ghost" size="sm" :href="route('loan-applications.show', $loan)">
                                            {{ __('View') }}
                                        </flux:button>
                                        @if($loan->status === 'pending')
                                            <flux:button variant="ghost" size="sm" onclick="approveLoan({{ $loan->id }})">
                                                {{ __('Approve') }}
                                            </flux:button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <div class="text-zinc-500 dark:text-zinc-400">
                                            <svg class="mx-auto h-12 w-12 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <h3 class="mt-2 text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ __('No loan applications') }}</h3>
                                            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Get started by creating a new loan application.') }}</p>
                                            <div class="mt-6">
                                                <flux:button variant="primary" :href="route('loan-applications.create')" icon="plus">
                                                    {{ __('New Application') }}
                                                </flux:button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($loans->hasPages())
                    <div class="bg-white dark:bg-zinc-800 px-4 py-3 border-t border-zinc-200 dark:border-zinc-700 sm:px-6">
                        {{ $loans->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Filter functionality
        document.addEventListener('DOMContentLoaded', function() {
            const statusFilter = document.getElementById('statusFilter');
            const memberFilter = document.getElementById('memberFilter');
            const dateFrom = document.getElementById('dateFrom');
            const dateTo = document.getElementById('dateTo');

            function applyFilters() {
                const params = new URLSearchParams(window.location.search);
                
                if (statusFilter.value) params.set('status', statusFilter.value);
                if (memberFilter.value) params.set('member', memberFilter.value);
                if (dateFrom.value) params.set('date_from', dateFrom.value);
                if (dateTo.value) params.set('date_to', dateTo.value);

                window.location.search = params.toString();
            }

            statusFilter.addEventListener('change', applyFilters);
            memberFilter.addEventListener('input', debounce(applyFilters, 500));
            dateFrom.addEventListener('change', applyFilters);
            dateTo.addEventListener('change', applyFilters);

            // Set current filter values from URL
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('status')) statusFilter.value = urlParams.get('status');
            if (urlParams.get('member')) memberFilter.value = urlParams.get('member');
            if (urlParams.get('date_from')) dateFrom.value = urlParams.get('date_from');
            if (urlParams.get('date_to')) dateTo.value = urlParams.get('date_to');
        });

        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        function approveLoan(loanId) {
            if (confirm('Are you sure you want to approve this loan application?')) {
                fetch(`/api/loan-applications/${loanId}/approve`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Loan approved successfully', 'success');
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        showNotification(data.message || 'Failed to approve loan', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred while approving the loan', 'error');
                });
            }
        }

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transform transition-all duration-300 translate-x-full ${
                type === 'error' ? 'bg-red-500 text-white' : 
                type === 'success' ? 'bg-green-500 text-white' : 'bg-blue-500 text-white'
            }`;
            
            notification.innerHTML = `
                <div class="flex items-start space-x-3">
                    <div class="flex-1">
                        <div class="font-medium">${type === 'error' ? 'Error' : type === 'success' ? 'Success' : 'Info'}</div>
                        <div class="text-sm opacity-90 mt-1">${message}</div>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="text-white hover:text-gray-200 p-1 flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);
            
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => notification.remove(), 300);
            }, 5000);
        }
    </script>
    @endpush
</x-layouts.app>
