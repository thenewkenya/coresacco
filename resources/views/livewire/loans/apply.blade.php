<?php

use App\Models\LoanType;
use App\Models\User;
use App\Models\Account;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public $member_id = '';
    public $loan_type_id = '';
    public $amount = '';
    public $purpose = '';
    public $employment_status = '';
    public $employer_name = '';
    public $employer_phone = '';
    public $monthly_income = '';
    public $guarantor_name = '';
    public $guarantor_phone = '';
    public $guarantor_relationship = '';
    public $guarantor_id_number = '';
    public $guarantor_address = '';
    public $guarantor_employer = '';
    public $guarantor_income = '';
    public $guarantor2_name = '';
    public $guarantor2_phone = '';
    public $guarantor2_relationship = '';
    public $guarantor2_id_number = '';
    public $guarantor2_address = '';
    public $guarantor2_employer = '';
    public $guarantor2_income = '';
    public $emergency_contact_name = '';
    public $emergency_contact_phone = '';
    public $emergency_contact_relationship = '';
    public $declaration_agreed = false;
    public $terms_agreed = false;

    public $loanTypes;
    public $members;
    public $userAccounts;
    public $totalSavings;

    public function mount()
    {
        $user = auth()->user();
        
        // For staff/admin, show all members. For members, only show themselves
        if ($user->hasAnyRole(['admin', 'manager', 'staff'])) {
            $this->members = User::where('role', 'member')->orderBy('name')->get();
        } else {
            $this->members = collect([$user]);
            $this->member_id = $user->id;
        }
        
        $this->loanTypes = LoanType::orderBy('name')->get();
        $this->userAccounts = Account::where('member_id', $user->id)->get();
        $this->totalSavings = $this->userAccounts->where('account_type', 'savings')->sum('balance');
    }

    public function with()
    {
        return [
            'members' => $this->members,
            'loanTypes' => $this->loanTypes,
            'userAccounts' => $this->userAccounts,
            'totalSavings' => $this->totalSavings,
        ];
    }


    public function calculateMonthlyPayment()
    {
        if ($this->amount && $this->loan_type_id) {
            $loanType = $this->loanTypes->find($this->loan_type_id);
            if ($loanType) {
                $principal = $this->amount;
                $rate = $loanType->interest_rate / 100 / 12; // Monthly rate
                $months = $loanType->max_term_period;
                
                if ($rate > 0) {
                    $monthlyPayment = $principal * ($rate * pow(1 + $rate, $months)) / (pow(1 + $rate, $months) - 1);
                } else {
                    $monthlyPayment = $principal / $months;
                }
                
                return round($monthlyPayment, 2);
            }
        }
        return 0;
    }

    public function submit()
    {
        // Get loan type for validation
        $loanType = $this->loanTypes->find($this->loan_type_id);
        
        $this->validate([
            'member_id' => 'required|exists:users,id',
            'loan_type_id' => 'required|exists:loan_types,id',
            'amount' => 'required|numeric|min:' . ($loanType->minimum_amount ?? 1000) . '|max:' . ($loanType->maximum_amount ?? 1000000),
            'purpose' => 'required|string|max:500',
            'employment_status' => 'required|in:employed,self_employed,unemployed,retired',
            'employer_name' => 'required_if:employment_status,employed',
            'employer_phone' => 'required_if:employment_status,employed',
            'monthly_income' => 'required|numeric|min:0',
            'guarantor_name' => 'required|string|max:255',
            'guarantor_phone' => 'required|string|max:20',
            'guarantor_relationship' => 'required|string|max:100',
            'guarantor_id_number' => 'required|string|max:20',
            'guarantor_address' => 'required|string|max:500',
            'guarantor_employer' => 'required|string|max:255',
            'guarantor_income' => 'required|numeric|min:0',
            'guarantor2_name' => 'nullable|string|max:255',
            'guarantor2_phone' => 'nullable|string|max:20',
            'guarantor2_relationship' => 'nullable|string|max:100',
            'guarantor2_id_number' => 'nullable|string|max:20',
            'guarantor2_address' => 'nullable|string|max:500',
            'guarantor2_employer' => 'nullable|string|max:255',
            'guarantor2_income' => 'nullable|numeric|min:0',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20',
            'emergency_contact_relationship' => 'required|string|max:100',
            'declaration_agreed' => 'required|accepted',
            'terms_agreed' => 'required|accepted',
        ]);

        // Get loan type details
        $loanType = $this->loanTypes->find($this->loan_type_id);
        $termPeriod = $loanType->max_term_period ?? 12;

        // Create loan application
        $loan = \App\Models\Loan::create([
            'member_id' => $this->member_id,
            'loan_type_id' => $this->loan_type_id,
            'amount' => $this->amount,
            'term_period' => $termPeriod,
            'purpose' => $this->purpose,
            'status' => 'pending',
            'interest_rate' => $loanType->interest_rate ?? 0,
            'monthly_payment' => $this->calculateMonthlyPayment(),
            'metadata' => [
                'employment_status' => $this->employment_status,
                'employer_name' => $this->employer_name,
                'employer_phone' => $this->employer_phone,
                'monthly_income' => $this->monthly_income,
                'emergency_contact_name' => $this->emergency_contact_name,
                'emergency_contact_phone' => $this->emergency_contact_phone,
                'emergency_contact_relationship' => $this->emergency_contact_relationship,
            ],
        ]);

        // Create primary guarantor
        $guarantor1 = \App\Models\Guarantor::create([
            'full_name' => $this->guarantor_name,
            'phone_number' => $this->guarantor_phone,
            'relationship_to_borrower' => $this->guarantor_relationship,
            'id_number' => $this->guarantor_id_number,
            'address' => $this->guarantor_address,
            'employment_status' => 'employed', // Default to employed
            'monthly_income' => $this->guarantor_income,
            'status' => 'active',
        ]);

        // Attach guarantor to loan
        $loan->guarantors()->attach($guarantor1->id, [
            'guarantee_amount' => $loan->amount,
            'status' => 'pending',
        ]);

        // Create second guarantor if provided
        if ($this->guarantor2_name) {
            $guarantor2 = \App\Models\Guarantor::create([
                'full_name' => $this->guarantor2_name,
                'phone_number' => $this->guarantor2_phone,
                'relationship_to_borrower' => $this->guarantor2_relationship,
                'id_number' => $this->guarantor2_id_number,
                'address' => $this->guarantor2_address,
                'employment_status' => 'employed', // Default to employed
                'monthly_income' => $this->guarantor2_income,
                'status' => 'active',
            ]);

            // Attach second guarantor to loan
            $loan->guarantors()->attach($guarantor2->id, [
                'guarantee_amount' => $loan->amount,
                'status' => 'pending',
            ]);
        }

        session()->flash('success', 'Loan application submitted successfully! Your application will be reviewed within 2-3 business days.');
        return redirect()->route('loans.my');
    }
}

?><div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" class="!text-zinc-900 dark:!text-zinc-100">Apply for Loan</flux:heading>
            <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">Complete comprehensive loan application with all required information</flux:subheading>
        </div>
        <flux:button variant="ghost" :href="route('loans.my')" icon="arrow-left">
            Back to My Loans
        </flux:button>
    </div>

    <!-- Member Savings Summary -->
    @if(auth()->user()->role === 'member')
        <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700 rounded-xl p-6">
            <div class="flex items-start space-x-3">
                <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                    <flux:icon.banknotes class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                </div>
                <div>
                    <h3 class="font-semibold text-emerald-900 dark:text-emerald-100 mb-2">Your Savings Summary</h3>
                    <p class="text-sm text-emerald-800 dark:text-emerald-200 mb-2">
                        Total Savings: <span class="font-bold">KES {{ number_format($totalSavings) }}</span>
                    </p>
                    <p class="text-xs text-emerald-700 dark:text-emerald-300">
                        Your savings history and balance will be considered during loan evaluation.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Loan Calculator -->
    @if($amount && $loan_type_id)
        @php
            $selectedLoanType = $loanTypes->find($loan_type_id);
            $termPeriod = $selectedLoanType->max_term_period ?? 12;
        @endphp
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-xl p-6">
            <div class="flex items-start space-x-3">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <flux:icon.calculator class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <h3 class="font-semibold text-blue-900 dark:text-blue-100 mb-2">Loan Calculator</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-blue-700 dark:text-blue-300">Monthly Payment:</span>
                            <span class="font-bold text-blue-900 dark:text-blue-100">KES {{ number_format($this->calculateMonthlyPayment()) }}</span>
                        </div>
                        <div>
                            <span class="text-blue-700 dark:text-blue-300">Total Interest:</span>
                            <span class="font-bold text-blue-900 dark:text-blue-100">KES {{ number_format(($this->calculateMonthlyPayment() * $termPeriod) - $amount) }}</span>
                        </div>
                        <div>
                            <span class="text-blue-700 dark:text-blue-300">Total Repayment:</span>
                            <span class="font-bold text-blue-900 dark:text-blue-100">KES {{ number_format($this->calculateMonthlyPayment() * $termPeriod) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Application Form -->
    <form wire:submit="submit" class="space-y-6">
        @csrf

        <!-- Member Information -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center space-x-3 mb-6">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <flux:icon.user class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <flux:heading size="base" class="dark:text-zinc-100">Member Information</flux:heading>
                    <flux:subheading class="dark:text-zinc-400">Select member and verify details</flux:subheading>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if(auth()->user()->hasAnyRole(['admin', 'manager', 'staff']))
                    <flux:field>
                        <flux:label>Select Member</flux:label>
                        <flux:select wire:model="member_id" required>
                            <option value="">Choose a member...</option>
                            @foreach($members as $member)
                                <option value="{{ $member->id }}">
                                    {{ $member->name }} ({{ $member->email }})
                                    @if($member->member_number)
                                        - #{{ $member->member_number }}
                                    @endif
                                </option>
                            @endforeach
                        </flux:select>
                        @error('member_id')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>
                    <flux:field>
                        <flux:label>Member ID</flux:label>
                        <flux:input value="{{ auth()->user()->member_number ?? 'Not assigned' }}" disabled />
                    </flux:field>
                @else
                    <flux:field>
                        <flux:label>Member Name</flux:label>
                        <flux:input value="{{ auth()->user()->name }}" disabled />
                        <flux:subheading class="dark:text-zinc-400">You are applying for yourself</flux:subheading>
                    </flux:field>
                    <flux:field>
                        <flux:label>Member ID</flux:label>
                        <flux:input value="{{ auth()->user()->member_number ?? 'Not assigned' }}" disabled />
                    </flux:field>
                @endif
            </div>
        </div>

        <!-- Loan Details -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center space-x-3 mb-6">
                <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                    <flux:icon.credit-card class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                </div>
                <div>
                    <flux:heading size="base" class="dark:text-zinc-100">Loan Details</flux:heading>
                    <flux:subheading class="dark:text-zinc-400">Specify loan requirements and terms</flux:subheading>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:field>
                    <flux:label>Loan Type</flux:label>
                    <flux:select wire:model="loan_type_id" required>
                        <option value="">Choose loan type...</option>
                        @foreach($loanTypes as $loanType)
                            <option value="{{ $loanType->id }}">
                                {{ $loanType->name }} ({{ $loanType->interest_rate }}% - {{ $loanType->max_term_period }} months)
                            </option>
                        @endforeach
                    </flux:select>
                    @error('loan_type_id')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </flux:field>

                <flux:field>
                    <flux:label>Loan Amount (KES)</flux:label>
                    <flux:input type="number" wire:model="amount" min="1000" step="100" required />
                    @if($loan_type_id)
                        @php
                            $selectedLoanType = $loanTypes->find($loan_type_id);
                        @endphp
                        @if($selectedLoanType)
                            <flux:subheading class="dark:text-zinc-400">Minimum: KES {{ number_format($selectedLoanType->minimum_amount) }} | Maximum: KES {{ number_format($selectedLoanType->maximum_amount) }}</flux:subheading>
                        @endif
                    @else
                        <flux:subheading class="dark:text-zinc-400">Select a loan type to see amount limits</flux:subheading>
                    @endif
                    @error('amount')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </flux:field>
            </div>

            @if($loan_type_id)
                @php
                    $selectedLoanType = $loanTypes->find($loan_type_id);
                @endphp
                @if($selectedLoanType)
                    <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-700">
                        <h4 class="font-semibold text-blue-900 dark:text-blue-100 mb-2">Loan Terms</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="text-blue-700 dark:text-blue-300">Interest Rate:</span>
                                <span class="font-bold text-blue-900 dark:text-blue-100">{{ $selectedLoanType->interest_rate }}%</span>
                            </div>
                            <div>
                                <span class="text-blue-700 dark:text-blue-300">Term Period:</span>
                                <span class="font-bold text-blue-900 dark:text-blue-100">{{ $selectedLoanType->max_term_period }} months</span>
                            </div>
                            <div>
                                <span class="text-blue-700 dark:text-blue-300">Max Amount:</span>
                                <span class="font-bold text-blue-900 dark:text-blue-100">KES {{ number_format($selectedLoanType->max_amount) }}</span>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            <div class="mt-6">
                <flux:field>
                    <flux:label>Purpose of Loan</flux:label>
                    <flux:textarea wire:model="purpose" rows="3" placeholder="Describe the purpose of this loan..." required />
                    @error('purpose')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </flux:field>
            </div>
        </div>

        <!-- Employment & Income Information -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center space-x-3 mb-6">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <flux:icon.briefcase class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <flux:heading size="base" class="dark:text-zinc-100">Employment & Income Information</flux:heading>
                    <flux:subheading class="dark:text-zinc-400">Provide your employment and financial details</flux:subheading>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:field>
                    <flux:label>Employment Status</flux:label>
                    <flux:select wire:model="employment_status" required>
                        <option value="">Select employment status...</option>
                        <option value="employed">Employed</option>
                        <option value="self_employed">Self-Employed</option>
                        <option value="unemployed">Unemployed</option>
                        <option value="retired">Retired</option>
                    </flux:select>
                    @error('employment_status')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </flux:field>

                <flux:field>
                    <flux:label>Monthly Income (KES)</flux:label>
                    <flux:input type="number" wire:model="monthly_income" min="0" step="100" required />
                    @error('monthly_income')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </flux:field>
            </div>

            <!-- Existing Loans Summary -->
            @php
                $existingLoans = \App\Models\Loan::where('member_id', auth()->id())
                    ->whereIn('status', ['active', 'pending'])
                    ->get();
                $totalExistingPayments = $existingLoans->sum('monthly_payment');
            @endphp
            
            @if($existingLoans->count() > 0)
                <div class="mt-6 p-4 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-700">
                    <h4 class="font-semibold text-amber-900 dark:text-amber-100 mb-2">Existing Loans</h4>
                    <div class="text-sm text-amber-800 dark:text-amber-200">
                        <p>You currently have {{ $existingLoans->count() }} active/pending loan(s) with total monthly payments of <span class="font-bold">KES {{ number_format($totalExistingPayments) }}</span></p>
                    </div>
                </div>
            @endif

            @if($employment_status === 'employed')
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:field>
                        <flux:label>Employer Name</flux:label>
                        <flux:input wire:model="employer_name" required />
                        @error('employer_name')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>

                    <flux:field>
                        <flux:label>Employer Phone</flux:label>
                        <flux:input wire:model="employer_phone" required />
                        @error('employer_phone')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>
                </div>
            @endif
        </div>


        <!-- Primary Guarantor Information -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center space-x-3 mb-6">
                <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                    <flux:icon.users class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                </div>
                <div>
                    <flux:heading size="base" class="dark:text-zinc-100">Primary Guarantor Information</flux:heading>
                    <flux:subheading class="dark:text-zinc-400">Provide details of your primary guarantor</flux:subheading>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:field>
                    <flux:label>Guarantor Name</flux:label>
                    <flux:input wire:model="guarantor_name" required />
                    @error('guarantor_name')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </flux:field>

                <flux:field>
                    <flux:label>Phone Number</flux:label>
                    <flux:input wire:model="guarantor_phone" required />
                    @error('guarantor_phone')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </flux:field>

                <flux:field>
                    <flux:label>Relationship</flux:label>
                    <flux:select wire:model="guarantor_relationship" required>
                        <option value="">Select relationship...</option>
                        <option value="spouse">Spouse</option>
                        <option value="parent">Parent</option>
                        <option value="sibling">Sibling</option>
                        <option value="friend">Friend</option>
                        <option value="colleague">Colleague</option>
                        <option value="other">Other</option>
                    </flux:select>
                    @error('guarantor_relationship')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </flux:field>

                <flux:field>
                    <flux:label>ID Number</flux:label>
                    <flux:input wire:model="guarantor_id_number" required />
                    @error('guarantor_id_number')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </flux:field>

                <flux:field>
                    <flux:label>Employer</flux:label>
                    <flux:input wire:model="guarantor_employer" required />
                    @error('guarantor_employer')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </flux:field>

                <flux:field>
                    <flux:label>Monthly Income (KES)</flux:label>
                    <flux:input type="number" wire:model="guarantor_income" min="0" step="100" required />
                    @error('guarantor_income')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </flux:field>
            </div>

            <div class="mt-6">
                <flux:field>
                    <flux:label>Address</flux:label>
                    <flux:textarea wire:model="guarantor_address" rows="2" placeholder="Enter guarantor's full address..." required />
                    @error('guarantor_address')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </flux:field>
            </div>
        </div>

        <!-- Secondary Guarantor Information (Optional) -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center space-x-3 mb-6">
                <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg">
                    <flux:icon.user-plus class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                </div>
                <div>
                    <flux:heading size="base" class="dark:text-zinc-100">Secondary Guarantor Information (Optional)</flux:heading>
                    <flux:subheading class="dark:text-zinc-400">Additional guarantor for larger loan amounts</flux:subheading>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:field>
                    <flux:label>Guarantor Name</flux:label>
                    <flux:input wire:model="guarantor2_name" />
                    @error('guarantor2_name')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </flux:field>

                <flux:field>
                    <flux:label>Phone Number</flux:label>
                    <flux:input wire:model="guarantor2_phone" />
                    @error('guarantor2_phone')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </flux:field>

                <flux:field>
                    <flux:label>Relationship</flux:label>
                    <flux:select wire:model="guarantor2_relationship">
                        <option value="">Select relationship...</option>
                        <option value="spouse">Spouse</option>
                        <option value="parent">Parent</option>
                        <option value="sibling">Sibling</option>
                        <option value="friend">Friend</option>
                        <option value="colleague">Colleague</option>
                        <option value="other">Other</option>
                    </flux:select>
                    @error('guarantor2_relationship')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </flux:field>

                <flux:field>
                    <flux:label>ID Number</flux:label>
                    <flux:input wire:model="guarantor2_id_number" />
                    @error('guarantor2_id_number')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </flux:field>

                <flux:field>
                    <flux:label>Employer</flux:label>
                    <flux:input wire:model="guarantor2_employer" />
                    @error('guarantor2_employer')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </flux:field>

                <flux:field>
                    <flux:label>Monthly Income (KES)</flux:label>
                    <flux:input type="number" wire:model="guarantor2_income" min="0" step="100" />
                    @error('guarantor2_income')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </flux:field>
            </div>

            <div class="mt-6">
                <flux:field>
                    <flux:label>Address</flux:label>
                    <flux:textarea wire:model="guarantor2_address" rows="2" placeholder="Enter guarantor's full address..." />
                    @error('guarantor2_address')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </flux:field>
            </div>
        </div>


        <!-- Emergency Contact -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center space-x-3 mb-6">
                <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded-lg">
                    <flux:icon.phone class="w-5 h-5 text-red-600 dark:text-red-400" />
                </div>
                <div>
                    <flux:heading size="base" class="dark:text-zinc-100">Emergency Contact</flux:heading>
                    <flux:subheading class="dark:text-zinc-400">Provide emergency contact information</flux:subheading>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <flux:field>
                    <flux:label>Contact Name</flux:label>
                    <flux:input wire:model="emergency_contact_name" required />
                    @error('emergency_contact_name')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </flux:field>

                <flux:field>
                    <flux:label>Phone Number</flux:label>
                    <flux:input wire:model="emergency_contact_phone" required />
                    @error('emergency_contact_phone')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </flux:field>

                <flux:field>
                    <flux:label>Relationship</flux:label>
                    <flux:select wire:model="emergency_contact_relationship" required>
                        <option value="">Select relationship...</option>
                        <option value="spouse">Spouse</option>
                        <option value="parent">Parent</option>
                        <option value="sibling">Sibling</option>
                        <option value="child">Child</option>
                        <option value="friend">Friend</option>
                        <option value="other">Other</option>
                    </flux:select>
                    @error('emergency_contact_relationship')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </flux:field>
            </div>
        </div>

        <!-- Declarations and Terms -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center space-x-3 mb-6">
                <div class="p-2 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                    <flux:icon.document-text class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                </div>
                <div>
                    <flux:heading size="base" class="dark:text-zinc-100">Declarations and Terms</flux:heading>
                    <flux:subheading class="dark:text-zinc-400">Please read and accept the terms and conditions</flux:subheading>
                </div>
            </div>

            <div class="space-y-4">
                <div class="p-4 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-700">
                    <h4 class="font-semibold text-amber-900 dark:text-amber-100 mb-2">Important Declarations</h4>
                    <ul class="text-sm text-amber-800 dark:text-amber-200 space-y-1">
                        <li>• I declare that all information provided is true and accurate</li>
                        <li>• I understand that false information may result in loan rejection</li>
                        <li>• I agree to provide additional documentation if requested</li>
                        <li>• I understand the loan terms and interest rates</li>
                        <li>• I agree to repay the loan according to the agreed schedule</li>
                    </ul>
                </div>

                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <flux:checkbox wire:model="declaration_agreed" required />
                        <div>
                            <label class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                I declare that all information provided is true and accurate
                            </label>
                            <p class="text-xs text-zinc-600 dark:text-zinc-400 mt-1">
                                I understand that providing false information may result in loan rejection and legal consequences.
                            </p>
                        </div>
                    </div>
                    @error('declaration_agreed')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror

                    <div class="flex items-start space-x-3">
                        <flux:checkbox wire:model="terms_agreed" required />
                        <div>
                            <label class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                I agree to the terms and conditions of the loan
                            </label>
                            <p class="text-xs text-zinc-600 dark:text-zinc-400 mt-1">
                                I understand the loan terms, interest rates, repayment schedule, and consequences of default.
                            </p>
                        </div>
                    </div>
                    @error('terms_agreed')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end space-x-4">
            <flux:button variant="ghost" :href="route('loans.my')">
                Cancel
            </flux:button>
            <flux:button type="submit" variant="primary" icon="check">
                Submit Application
            </flux:button>
        </div>
    </form>
</div>
