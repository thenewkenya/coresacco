<x-layouts.app :title="__('Loan Application Details')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('Loan Application Details') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Application #') }}{{ $loan->id }} - {{ $loan->member->name }}
                        </p>
                    </div>
                    <div class="flex space-x-3">
                        <flux:button variant="ghost" :href="route('loan-applications.index')" icon="arrow-left">
                            {{ __('Back to Applications') }}
                        </flux:button>
                        @if($loan->status === 'pending')
                            <flux:button variant="outline" onclick="approveLoan()" id="approveBtn">
                                {{ __('Approve Loan') }}
                            </flux:button>
                            <flux:button variant="outline" onclick="rejectLoan()" id="rejectBtn">
                                {{ __('Reject Loan') }}
                            </flux:button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Loan Information -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                            {{ __('Loan Information') }}
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Loan Type') }}</p>
                                <p class="font-medium">{{ $loan->loanType->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Amount') }}</p>
                                <p class="font-medium">KES {{ number_format($loan->amount, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Interest Rate') }}</p>
                                <p class="font-medium">{{ $loan->interest_rate }}%</p>
                            </div>
                            <div>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Term Period') }}</p>
                                <p class="font-medium">{{ $loan->term_period }} months</p>
                            </div>
                            <div>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Status') }}</p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($loan->status === 'approved') bg-green-100 text-green-800
                                    @elseif($loan->status === 'rejected') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ ucfirst($loan->status) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Application Date') }}</p>
                                <p class="font-medium">{{ $loan->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Borrowing Criteria Evaluation -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                            {{ __('Borrowing Criteria Evaluation') }}
                        </h3>
                        
                        <!-- Savings Criteria -->
                        <div class="mb-6">
                            <h4 class="font-medium text-zinc-900 dark:text-zinc-100 mb-3">{{ __('Savings Criteria') }}</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 rounded-lg bg-gray-50 dark:bg-gray-800">
                                <div>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Member Savings Balance') }}</p>
                                    <p class="font-medium">KES {{ number_format($loan->member_savings_balance, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Member Shares Balance') }}</p>
                                    <p class="font-medium">KES {{ number_format($loan->member_shares_balance, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Total Balance') }}</p>
                                    <p class="font-medium">KES {{ number_format($loan->member_total_balance, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Max Loan Amount') }}</p>
                                    <p class="font-medium">KES {{ number_format($loan->member_savings_balance * $loan->required_savings_multiplier, 2) }}</p>
                                </div>
                                <div class="md:col-span-2">
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Status') }}</p>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($loan->meets_savings_criteria) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                                        @if($loan->meets_savings_criteria) ✓ Met @else ✗ Not Met @endif
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Membership Criteria -->
                        <div class="mb-6">
                            <h4 class="font-medium text-zinc-900 dark:text-zinc-100 mb-3">{{ __('Membership Criteria') }}</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 rounded-lg bg-gray-50 dark:bg-gray-800">
                                <div>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Months in SACCO') }}</p>
                                    <p class="font-medium">{{ $loan->member_months_in_sacco }} months</p>
                                </div>
                                <div>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Minimum Required') }}</p>
                                    <p class="font-medium">{{ $loan->minimum_membership_months }} months</p>
                                </div>
                                <div class="md:col-span-2">
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Status') }}</p>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($loan->meets_membership_criteria) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                                        @if($loan->meets_membership_criteria) ✓ Met @else ✗ Not Met @endif
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Overall Eligibility -->
                        <div class="p-4 rounded-lg @if($loan->meets_savings_criteria && $loan->meets_membership_criteria && $loan->meets_guarantor_criteria) bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 @else bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 @endif">
                            <div class="flex items-center justify-between">
                                <h4 class="font-medium @if($loan->meets_savings_criteria && $loan->meets_membership_criteria && $loan->meets_guarantor_criteria) text-green-900 dark:text-green-100 @else text-red-900 dark:text-red-100 @endif">
                                    {{ __('Overall Eligibility') }}
                                </h4>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($loan->meets_savings_criteria && $loan->meets_membership_criteria && $loan->meets_guarantor_criteria) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                                    @if($loan->meets_savings_criteria && $loan->meets_membership_criteria && $loan->meets_guarantor_criteria) ✓ Eligible @else ✗ Not Eligible @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Guarantors -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ __('Guarantors') }}
                            </h3>
                            <div class="text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $loan->approved_guarantors }}/{{ $loan->required_guarantors }} {{ __('Approved') }}
                            </div>
                        </div>

                        @if($loan->guarantors->count() > 0)
                            <div class="space-y-4">
                                @foreach($loan->guarantors as $guarantor)
                                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                        <div class="flex items-center justify-between mb-3">
                                            <h4 class="font-medium text-zinc-900 dark:text-zinc-100">{{ $guarantor->full_name }}</h4>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($guarantor->pivot->status === 'approved') bg-green-100 text-green-800
                                                @elseif($guarantor->pivot->status === 'rejected') bg-red-100 text-red-800
                                                @else bg-yellow-100 text-yellow-800 @endif">
                                                {{ ucfirst($guarantor->pivot->status) }}
                                            </span>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                            <div>
                                                <p class="text-zinc-600 dark:text-zinc-400">{{ __('ID Number') }}</p>
                                                <p class="font-medium">{{ $guarantor->id_number }}</p>
                                            </div>
                                            <div>
                                                <p class="text-zinc-600 dark:text-zinc-400">{{ __('Phone') }}</p>
                                                <p class="font-medium">{{ $guarantor->phone_number }}</p>
                                            </div>
                                            <div>
                                                <p class="text-zinc-600 dark:text-zinc-400">{{ __('Employment') }}</p>
                                                <p class="font-medium">{{ ucfirst(str_replace('_', ' ', $guarantor->employment_status)) }}</p>
                                            </div>
                                            <div>
                                                <p class="text-zinc-600 dark:text-zinc-400">{{ __('Monthly Income') }}</p>
                                                <p class="font-medium">KES {{ number_format($guarantor->monthly_income, 2) }}</p>
                                            </div>
                                            <div>
                                                <p class="text-zinc-600 dark:text-zinc-400">{{ __('Relationship') }}</p>
                                                <p class="font-medium">{{ $guarantor->relationship_to_borrower }}</p>
                                            </div>
                                            <div>
                                                <p class="text-zinc-600 dark:text-zinc-400">{{ __('Guarantee Amount') }}</p>
                                                <p class="font-medium">KES {{ number_format($guarantor->pivot->guarantee_amount, 2) }}</p>
                                            </div>
                                        </div>

                                        @if($guarantor->pivot->status === 'pending' && $loan->status === 'pending')
                                            <div class="mt-4 flex space-x-2">
                                                <flux:button size="sm" variant="outline" onclick="approveGuarantor({{ $guarantor->id }})">
                                                    {{ __('Approve') }}
                                                </flux:button>
                                                <flux:button size="sm" variant="outline" onclick="rejectGuarantor({{ $guarantor->id }})">
                                                    {{ __('Reject') }}
                                                </flux:button>
                                            </div>
                                        @endif

                                        @if($guarantor->pivot->status === 'rejected' && $guarantor->pivot->rejection_reason)
                                            <div class="mt-3 p-3 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                                                <p class="text-sm text-red-800 dark:text-red-200">
                                                    <strong>{{ __('Rejection Reason') }}:</strong> {{ $guarantor->pivot->rejection_reason }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <p class="text-zinc-600 dark:text-zinc-400">{{ __('No guarantors added yet') }}</p>
                            </div>
                        @endif

                        <!-- Guarantor Summary -->
                        <div class="mt-6 p-4 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
                            <h4 class="font-medium text-blue-900 dark:text-blue-100 mb-3">{{ __('Guarantor Summary') }}</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                <div>
                                    <p class="text-blue-700 dark:text-blue-300">{{ __('Required Guarantors') }}</p>
                                    <p class="font-medium text-blue-900 dark:text-blue-100">{{ $loan->required_guarantors }}</p>
                                </div>
                                <div>
                                    <p class="text-blue-700 dark:text-blue-300">{{ __('Approved Guarantors') }}</p>
                                    <p class="font-medium text-blue-900 dark:text-blue-100">{{ $loan->approved_guarantors }}</p>
                                </div>
                                <div>
                                    <p class="text-blue-700 dark:text-blue-300">{{ __('Total Guarantee Amount') }}</p>
                                    <p class="font-medium text-blue-900 dark:text-blue-100">KES {{ number_format($loan->total_guarantee_amount, 2) }}</p>
                                </div>
                            </div>
                            <div class="mt-3">
                                <p class="text-sm text-blue-700 dark:text-blue-300">{{ __('Status') }}</p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($loan->meets_guarantor_criteria) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                                    @if($loan->meets_guarantor_criteria) ✓ Met @else ✗ Not Met @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Member Information -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                            {{ __('Member Information') }}
                        </h3>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Name') }}</p>
                                <p class="font-medium">{{ $loan->member->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Email') }}</p>
                                <p class="font-medium">{{ $loan->member->email }}</p>
                            </div>
                            @if($loan->member->member_number)
                                <div>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Member Number') }}</p>
                                    <p class="font-medium">#{{ $loan->member->member_number }}</p>
                                </div>
                            @endif
                            <div>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Joining Date') }}</p>
                                <p class="font-medium">{{ $loan->member->joining_date->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    @if($loan->status === 'pending')
                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                                {{ __('Actions') }}
                            </h3>
                            <div class="space-y-3">
                                <flux:button variant="primary" class="w-full" onclick="approveLoan()" id="approveBtn">
                                    {{ __('Approve Loan') }}
                                </flux:button>
                                <flux:button variant="outline" class="w-full" onclick="rejectLoan()" id="rejectBtn">
                                    {{ __('Reject Loan') }}
                                </flux:button>
                            </div>
                        </div>
                    @endif

                    <!-- Notes -->
                    @if($loan->criteria_evaluation_notes)
                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                                {{ __('Notes') }}
                            </h3>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $loan->criteria_evaluation_notes }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Rejection Modal -->
    <div id="rejectionModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-zinc-800 rounded-lg max-w-md w-full p-6">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                    {{ __('Reject Loan Application') }}
                </h3>
                <form id="rejectionForm">
                    <flux:field>
                        <flux:label>{{ __('Reason for Rejection') }}</flux:label>
                        <flux:textarea name="reason" rows="3" placeholder="{{ __('Please provide a reason for rejection...') }}" required></flux:textarea>
                    </flux:field>
                    <div class="flex justify-end space-x-3 mt-6">
                        <flux:button type="button" variant="ghost" onclick="closeRejectionModal()">
                            {{ __('Cancel') }}
                        </flux:button>
                        <flux:button type="submit" variant="primary">
                            {{ __('Reject Application') }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function approveLoan() {
            if (confirm('Are you sure you want to approve this loan application?')) {
                fetch(`/api/loan-applications/{{ $loan->id }}/approve`, {
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

        function rejectLoan() {
            document.getElementById('rejectionModal').classList.remove('hidden');
        }

        function closeRejectionModal() {
            document.getElementById('rejectionModal').classList.add('hidden');
            document.getElementById('rejectionForm').reset();
        }

        function approveGuarantor(guarantorId) {
            if (confirm('Are you sure you want to approve this guarantor?')) {
                fetch(`/api/loan-applications/{{ $loan->id }}/guarantors/${guarantorId}/approve`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Guarantor approved successfully', 'success');
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        showNotification(data.message || 'Failed to approve guarantor', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred while approving the guarantor', 'error');
                });
            }
        }

        function rejectGuarantor(guarantorId) {
            const reason = prompt('Please provide a reason for rejecting this guarantor:');
            if (reason) {
                fetch(`/api/loan-applications/{{ $loan->id }}/guarantors/${guarantorId}/reject`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ reason: reason })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Guarantor rejected successfully', 'success');
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        showNotification(data.message || 'Failed to reject guarantor', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred while rejecting the guarantor', 'error');
                });
            }
        }

        // Handle rejection form submission
        document.getElementById('rejectionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const reason = formData.get('reason');

            fetch(`/api/loan-applications/{{ $loan->id }}/reject`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ reason: reason })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Loan application rejected successfully', 'success');
                    closeRejectionModal();
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showNotification(data.message || 'Failed to reject loan application', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while rejecting the loan application', 'error');
            });
        });

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
