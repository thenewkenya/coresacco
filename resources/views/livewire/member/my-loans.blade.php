<?php

use Livewire\Volt\Component;
use App\Models\Loan;
use App\Models\Transaction;

new class extends Component
{
    public $loans;
    public $totalBorrowed;
    public $totalOutstanding;
    public $recentPayments;

    public function mount()
    {
        $this->loans = auth()->user()->hasMany(Loan::class, 'member_id')
            ->with('loanType')
            ->latest()
            ->get();
            
        $this->totalBorrowed = $this->loans->sum('amount');
        
        $this->totalOutstanding = $this->loans
            ->whereIn('status', [Loan::STATUS_ACTIVE, Loan::STATUS_DISBURSED])
            ->sum(function($loan) {
                return $loan->calculateTotalRepayment() - $this->getPaidAmount($loan);
            });
        
        $this->recentPayments = Transaction::where('member_id', auth()->id())
            ->where('type', Transaction::TYPE_LOAN_REPAYMENT)
            ->with('loan.loanType')
            ->latest()
            ->take(5)
            ->get();
    }
    
    private function getPaidAmount($loan)
    {
        return Transaction::where('member_id', $loan->member_id)
            ->where('loan_id', $loan->id)
            ->where('type', Transaction::TYPE_LOAN_REPAYMENT)
            ->sum('amount');
    }
}; ?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">My Loans</h1>
            <p class="text-gray-600 dark:text-gray-400">Track your loans and repayment progress</p>
        </div>
        <flux:button variant="primary" icon="plus">
            Apply for Loan
        </flux:button>
    </div>

    <!-- Loan Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Borrowed -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Borrowed</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        KES {{ number_format($totalBorrowed, 2) }}
                    </p>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                    <flux:icon.banknotes class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center text-sm text-blue-600 dark:text-blue-400">
                    <flux:icon.document-text class="h-4 w-4 mr-1" />
                    <span>{{ $loans->count() }} Total Loan{{ $loans->count() !== 1 ? 's' : '' }}</span>
                </div>
            </div>
        </div>

        <!-- Outstanding Amount -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Outstanding Amount</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        KES {{ number_format($totalOutstanding, 2) }}
                    </p>
                </div>
                <div class="p-3 bg-red-100 dark:bg-red-900/20 rounded-lg">
                    <flux:icon.exclamation-triangle class="h-6 w-6 text-red-600 dark:text-red-400" />
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                    <span>{{ $loans->whereIn('status', [Loan::STATUS_ACTIVE, Loan::STATUS_DISBURSED])->count() }} active loans</span>
                </div>
            </div>
        </div>

        <!-- Recent Payment -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Last Payment</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        KES {{ number_format($recentPayments->first()->amount ?? 0, 2) }}
                    </p>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900/20 rounded-lg">
                    <flux:icon.check-circle class="h-6 w-6 text-green-600 dark:text-green-400" />
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                    <span>{{ $recentPayments->first() ? $recentPayments->first()->created_at->diffForHumans() : 'No payments yet' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Loans -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">My Loans</h2>
        </div>
        <div class="p-6">
            <div class="space-y-6">
                @forelse($loans as $loan)
                    @php
                        $totalRepayment = $loan->calculateTotalRepayment();
                        $paidAmount = $this->getPaidAmount($loan);
                        $outstandingAmount = $totalRepayment - $paidAmount;
                        $progressPercentage = $totalRepayment > 0 ? ($paidAmount / $totalRepayment) * 100 : 0;
                    @endphp
                    <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $loan->loanType->name }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Loan Amount: KES {{ number_format($loan->amount, 2) }}</p>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                @if($loan->status === 'active') bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400
                                @elseif($loan->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400
                                @elseif($loan->status === 'completed') bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400
                                @elseif($loan->status === 'defaulted') bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400
                                @else bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400 @endif">
                                {{ ucfirst($loan->status) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Interest Rate</p>
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $loan->interest_rate }}%</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Term</p>
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $loan->term_period }} months</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Monthly Payment</p>
                                <p class="font-semibold text-gray-900 dark:text-white">KES {{ number_format($loan->calculateMonthlyPayment(), 2) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Due Date</p>
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $loan->due_date ? $loan->due_date->format('M j, Y') : 'N/A' }}</p>
                            </div>
                        </div>

                        @if(in_array($loan->status, [Loan::STATUS_ACTIVE, Loan::STATUS_DISBURSED]))
                            <!-- Progress Bar -->
                            <div class="mb-4">
                                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-2">
                                    <span>Repayment Progress</span>
                                    <span>{{ number_format($progressPercentage, 1) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-blue-600 dark:bg-blue-400 h-2 rounded-full transition-all duration-300" 
                                         style="width: {{ $progressPercentage }}%"></div>
                                </div>
                                <div class="flex justify-between text-sm text-gray-500 dark:text-gray-400 mt-2">
                                    <span>Paid: KES {{ number_format($paidAmount, 2) }}</span>
                                    <span>Outstanding: KES {{ number_format($outstandingAmount, 2) }}</span>
                                </div>
                            </div>
                        @endif

                        <div class="flex space-x-3">
                            @if($loan->status === 'active')
                                <flux:button variant="primary" size="sm">Make Payment</flux:button>
                            @endif
                            <flux:button variant="ghost" size="sm">View Details</flux:button>
                            <flux:button variant="ghost" size="sm">Download Statement</flux:button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <flux:icon.document-text class="h-16 w-16 text-gray-400 mx-auto mb-4" />
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Loans Yet</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-6">You haven't applied for any loans yet.</p>
                        <flux:button variant="primary">Apply for Your First Loan</flux:button>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Payments -->
    @if($recentPayments->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Payments</h2>
                <flux:button variant="ghost" size="sm">View All</flux:button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Loan Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Reference</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($recentPayments as $payment)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $payment->created_at->format('M j, Y') }}
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $payment->created_at->format('g:i A') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                {{ $payment->loan->loanType->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600 dark:text-green-400">
                                KES {{ number_format($payment->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $payment->reference_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if($payment->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400
                                    @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400
                                    @else bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400 @endif">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div> 