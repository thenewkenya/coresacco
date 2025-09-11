<?php

use App\Models\Loan;
use App\Models\Transaction;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public Loan $loan;
    public $totalRepaid = 0;
    public $remainingBalance = 0;
    public $monthlyPayment = 0;
    public $repaymentSchedule = [];
    public $showRepaymentModal = false;
    public $showGuarantorsModal = false;
    public $repaymentAmount = '';
    public $repaymentMethod = '';

    public function mount(Loan $loan)
    {
        $user = auth()->user();
        
        // Authorization check
        if ($user->role === 'member' && $loan->member_id !== $user->id) {
            abort(403, 'Unauthorized access to this loan.');
        }

        $this->loan = $loan->load(['member', 'loanType', 'transactions', 'guarantors']);
        
        // Calculate loan details
        $this->totalRepaid = $loan->transactions()
            ->where('type', Transaction::TYPE_LOAN_REPAYMENT)
            ->where('status', Transaction::STATUS_COMPLETED)
            ->sum('amount');
        
        $this->remainingBalance = $loan->calculateTotalRepayment() - $this->totalRepaid;
        $this->monthlyPayment = $loan->calculateMonthlyPayment();
        
        // Generate repayment schedule
        $this->repaymentSchedule = $this->generateRepaymentSchedule($loan);
    }

    public function generateRepaymentSchedule($loan)
    {
        $schedule = [];
        $principal = $loan->amount;
        $rate = $loan->interest_rate / 100 / 12; // Monthly rate
        $months = $loan->term_period;
        
        if ($rate > 0) {
            $monthlyPayment = $principal * ($rate * pow(1 + $rate, $months)) / (pow(1 + $rate, $months) - 1);
        } else {
            $monthlyPayment = $principal / $months;
        }
        
        $balance = $loan->amount;
        $startDate = $loan->disbursement_date ?? now();
        
        for ($i = 1; $i <= $months; $i++) {
            $interestPayment = $balance * $rate;
            $principalPayment = $monthlyPayment - $interestPayment;
            $balance -= $principalPayment;
            
            $schedule[] = [
                'month' => $i,
                'date' => $startDate->copy()->addMonths($i)->format('M Y'),
                'payment' => round($monthlyPayment, 2),
                'principal' => round($principalPayment, 2),
                'interest' => round($interestPayment, 2),
                'balance' => round(max($balance, 0), 2),
            ];
        }
        
        return $schedule;
    }

    public function getStatusColor()
    {
        return match($this->loan->status) {
            'pending' => 'yellow',
            'approved' => 'blue',
            'active' => 'green',
            'completed' => 'gray',
            'defaulted' => 'red',
            'rejected' => 'red',
            default => 'gray'
        };
    }

    public function getStatusLabel()
    {
        return match($this->loan->status) {
            'pending' => 'Pending Review',
            'approved' => 'Approved',
            'active' => 'Active',
            'completed' => 'Completed',
            'defaulted' => 'Defaulted',
            'rejected' => 'Rejected',
            default => ucfirst($this->loan->status)
        };
    }

    public function openRepaymentModal()
    {
        $this->repaymentAmount = $this->monthlyPayment;
        $this->showRepaymentModal = true;
    }

    public function closeRepaymentModal()
    {
        $this->showRepaymentModal = false;
        $this->repaymentAmount = '';
        $this->repaymentMethod = '';
    }

    public function openGuarantorsModal()
    {
        $this->showGuarantorsModal = true;
    }

    public function closeGuarantorsModal()
    {
        $this->showGuarantorsModal = false;
    }
}

?><div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" class="!text-zinc-900 dark:!text-zinc-100">Loan Details</flux:heading>
            <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">{{ $loan->loanType->name }} • {{ $loan->member->name }}</flux:subheading>
        </div>
        <div class="flex items-center space-x-4">
            @if(in_array($loan->status, ['active', 'disbursed']))
                <flux:button variant="primary" icon="credit-card" wire:click="openRepaymentModal">
                    Make Repayment
                </flux:button>
            @endif
            <flux:button variant="ghost" :href="route('loans.index')" icon="arrow-left">
                Back to Loans
            </flux:button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:heading size="base" class="dark:text-zinc-100">Loan Amount</flux:heading>
                    <flux:subheading class="dark:text-zinc-400">Total borrowed</flux:subheading>
                </div>
                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <flux:icon.banknotes class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KES {{ number_format($loan->amount) }}</div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:heading size="base" class="dark:text-zinc-100">Total Repaid</flux:heading>
                    <flux:subheading class="dark:text-zinc-400">Amount paid back</flux:subheading>
                </div>
                <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                    <flux:icon.check-circle class="w-5 h-5 text-green-600 dark:text-green-400" />
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KES {{ number_format($totalRepaid) }}</div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:heading size="base" class="dark:text-zinc-100">Remaining Balance</flux:heading>
                    <flux:subheading class="dark:text-zinc-400">Amount still owed</flux:subheading>
                </div>
                <div class="p-2 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                    <flux:icon.exclamation-triangle class="w-5 h-5 text-orange-600 dark:text-orange-400" />
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KES {{ number_format($remainingBalance) }}</div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:heading size="base" class="dark:text-zinc-100">Monthly Payment</flux:heading>
                    <flux:subheading class="dark:text-zinc-400">Regular payment</flux:subheading>
                </div>
                <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                    <flux:icon.calendar class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">KES {{ number_format($monthlyPayment) }}</div>
            </div>
        </div>
    </div>

    <!-- Loan Information -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Loan Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                        <flux:icon.information-circle class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <flux:heading size="base" class="dark:text-zinc-100">Loan Information</flux:heading>
                        <flux:subheading class="dark:text-zinc-400">Basic loan details and terms</flux:subheading>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <flux:subheading class="dark:text-zinc-400">Borrower</flux:subheading>
                        <div class="mt-1">
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $loan->member->name }}</div>
                            <div class="text-sm text-zinc-600 dark:text-zinc-400">{{ $loan->member->email }}</div>
                        </div>
                    </div>

                    <div>
                        <flux:subheading class="dark:text-zinc-400">Loan Type</flux:subheading>
                        <div class="mt-1 font-medium text-zinc-900 dark:text-zinc-100">{{ $loan->loanType->name }}</div>
                    </div>

                    <div>
                        <flux:subheading class="dark:text-zinc-400">Interest Rate</flux:subheading>
                        <div class="mt-1 font-medium text-zinc-900 dark:text-zinc-100">{{ $loan->interest_rate }}% per annum</div>
                    </div>

                    <div>
                        <flux:subheading class="dark:text-zinc-400">Term Period</flux:subheading>
                        <div class="mt-1 font-medium text-zinc-900 dark:text-zinc-100">{{ $loan->term_period }} months</div>
                    </div>

                    <div>
                        <flux:subheading class="dark:text-zinc-400">Purpose</flux:subheading>
                        <div class="mt-1 font-medium text-zinc-900 dark:text-zinc-100">{{ $loan->purpose ?? 'Not specified' }}</div>
                    </div>

                    <div>
                        <flux:subheading class="dark:text-zinc-400">Status</flux:subheading>
                        <div class="mt-1">
                            <flux:badge variant="{{ $this->getStatusColor() }}">{{ $this->getStatusLabel() }}</flux:badge>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Repayment Schedule -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                            <flux:icon.calendar class="w-5 h-5 text-green-600 dark:text-green-400" />
                        </div>
                        <div>
                            <flux:heading size="base" class="dark:text-zinc-100">Repayment Schedule</flux:heading>
                            <flux:subheading class="dark:text-zinc-400">Monthly payment breakdown</flux:subheading>
                        </div>
                    </div>
                    <flux:button variant="ghost" size="sm" icon="eye">
                        View All
                    </flux:button>
                </div>

                <div class="space-y-3">
                    @foreach(array_slice($repaymentSchedule, 0, 6) as $payment)
                        <div class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                                    <span class="text-xs font-medium text-blue-600 dark:text-blue-400">{{ $payment['month'] }}</span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $payment['date'] }}</div>
                                    <div class="text-xs text-zinc-600 dark:text-zinc-400">Principal: KES {{ number_format($payment['principal']) }} • Interest: KES {{ number_format($payment['interest']) }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-medium text-zinc-900 dark:text-zinc-100">KES {{ number_format($payment['payment']) }}</div>
                                <div class="text-xs text-zinc-600 dark:text-zinc-400">Balance: KES {{ number_format($payment['balance']) }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                        <flux:icon.bolt class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                    </div>
                    <flux:heading size="base" class="dark:text-zinc-100">Quick Actions</flux:heading>
                </div>

                <div class="space-y-3">
                    @if(in_array($loan->status, ['active', 'disbursed']))
                        <flux:button variant="primary" class="w-full" wire:click="openRepaymentModal">
                            Make Repayment
                        </flux:button>
                    @endif

                    <flux:button variant="outline" class="w-full" wire:click="openGuarantorsModal">
                        View Guarantors
                    </flux:button>

                    <flux:button variant="outline" class="w-full" :href="route('loans.my')">
                        View All Loans
                    </flux:button>
                </div>
            </div>

            <!-- Loan Progress -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                        <flux:icon.chart-bar class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <flux:heading size="base" class="dark:text-zinc-100">Progress</flux:heading>
                </div>

                @php
                    $totalRepayment = $loan->calculateTotalRepayment();
                    $progressPercentage = $totalRepayment > 0 ? ($totalRepaid / $totalRepayment) * 100 : 0;
                @endphp

                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-zinc-600 dark:text-zinc-400">Repayment Progress</span>
                            <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($progressPercentage, 1) }}%</span>
                        </div>
                        <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2">
                            <div class="bg-emerald-500 h-2 rounded-full transition-all duration-300" style="width: {{ $progressPercentage }}%"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <div class="text-zinc-600 dark:text-zinc-400">Total Repayment</div>
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">KES {{ number_format($totalRepayment) }}</div>
                        </div>
                        <div>
                            <div class="text-zinc-600 dark:text-zinc-400">Remaining</div>
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">KES {{ number_format($remainingBalance) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Repayment Modal -->
    <flux:modal wire:model="showRepaymentModal" class="md:w-2xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg" class="dark:text-zinc-100">Make Repayment</flux:heading>
                <flux:subheading class="dark:text-zinc-400">Loan: KES {{ number_format($loan->amount) }}</flux:subheading>
            </div>

            <div class="space-y-4">
                <flux:field>
                    <flux:label>Repayment Amount (KES)</flux:label>
                    <flux:input type="number" wire:model="repaymentAmount" min="1" step="100" />
                </flux:field>

                <flux:field>
                    <flux:label>Payment Method</flux:label>
                    <flux:select wire:model="repaymentMethod">
                        <option value="">Select payment method...</option>
                        <option value="cash">Cash</option>
                        <option value="mpesa">M-Pesa</option>
                        <option value="bank_transfer">Bank Transfer</option>
                    </flux:select>
                </flux:field>
            </div>

            <div class="flex items-center justify-end space-x-4">
                <flux:button variant="ghost" wire:click="closeRepaymentModal">
                    Cancel
                </flux:button>
                <flux:button variant="primary" icon="credit-card">
                    Process Payment
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Guarantors Modal -->
    <flux:modal wire:model="showGuarantorsModal" class="md:w-4xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg" class="dark:text-zinc-100">Loan Guarantors</flux:heading>
                <flux:subheading class="dark:text-zinc-400">People who guaranteed this loan</flux:subheading>
            </div>

            <div class="space-y-4">
                @forelse($loan->guarantors as $guarantor)
                    <div class="bg-zinc-50 dark:bg-zinc-700 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $guarantor->full_name }}</div>
                                <div class="text-sm text-zinc-600 dark:text-zinc-400">{{ $guarantor->relationship_to_borrower }} • {{ $guarantor->phone_number }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">KES {{ number_format($guarantor->pivot->guarantee_amount) }}</div>
                                <flux:badge variant="blue">{{ ucfirst($guarantor->pivot->status) }}</flux:badge>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <flux:icon.user-group class="w-12 h-12 text-zinc-400 mx-auto mb-4" />
                        <div class="text-zinc-600 dark:text-zinc-400">No guarantors found</div>
                    </div>
                @endforelse
            </div>

            <div class="flex items-center justify-end">
                <flux:button variant="ghost" wire:click="closeGuarantorsModal">
                    Close
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>

