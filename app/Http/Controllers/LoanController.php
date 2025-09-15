<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanType;
use App\Models\LoanAccount;
use App\Models\LedgerEntry;
use App\Models\Notification;
use App\Models\Account;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class LoanController extends Controller
{
    public function index(Request $request): Response
    {
        $user = Auth::user();
        
        $query = Loan::with(['member', 'loanType', 'loanAccount'])
            ->when($user->role === 'member', function ($query) use ($user) {
                return $query->where('member_id', $user->id);
            });

        // Apply filters
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('amount', 'like', '%' . $request->search . '%')
                  ->orWhereHas('member', function($memberQuery) use ($request) {
                      $memberQuery->where('name', 'like', '%' . $request->search . '%')
                                 ->orWhere('member_number', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('loan_type')) {
            $query->where('loan_type_id', $request->loan_type);
        }

        $loans = $query->orderBy('created_at', 'desc')->paginate(15);

        // Calculate statistics
        $totalLoans = Loan::when($user->role === 'member', function ($query) use ($user) {
            return $query->where('member_id', $user->id);
        })->count();

        $totalAmount = Loan::when($user->role === 'member', function ($query) use ($user) {
            return $query->where('member_id', $user->id);
        })->sum('amount');

        $activeLoans = Loan::when($user->role === 'member', function ($query) use ($user) {
            return $query->where('member_id', $user->id);
        })->whereIn('status', [Loan::STATUS_ACTIVE, Loan::STATUS_DISBURSED])->count();

        $pendingLoans = Loan::when($user->role === 'member', function ($query) use ($user) {
            return $query->where('member_id', $user->id);
        })->where('status', Loan::STATUS_PENDING)->count();

        $loanTypes = LoanType::where('status', 'active')->get();

        return Inertia::render('loans/index', [
            'loans' => $loans,
            'loanTypes' => $loanTypes,
            'stats' => [
                'totalLoans' => $totalLoans,
                'totalAmount' => $totalAmount,
                'activeLoans' => $activeLoans,
                'pendingLoans' => $pendingLoans,
            ],
            'filters' => $request->only(['search', 'status', 'loan_type']),
            'statusOptions' => [
                'pending' => 'Pending',
                'approved' => 'Approved',
                'disbursed' => 'Disbursed',
                'active' => 'Active',
                'completed' => 'Completed',
                'defaulted' => 'Defaulted',
                'rejected' => 'Rejected',
            ],
        ]);
    }

    public function create(): Response
    {
        $user = Auth::user();
        
        $loanTypes = LoanType::where('status', 'active')->get();
        
        if ($user->role === 'member') {
            // Members can only apply for loans for themselves
            $members = collect([$user]);
        } else {
            // Staff can create loans for any member
            $members = User::where('role', 'member')->with('accounts')->orderBy('name')->get();
        }

        return Inertia::render('loans/create', [
            'loanTypes' => $loanTypes,
            'members' => $members,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:users,id',
            'loan_type_id' => 'required|exists:loan_types,id',
            'amount' => 'required|numeric|min:1000',
            'term_period' => 'required|integer|min:1|max:60',
            'collateral_details' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        $member = User::findOrFail($request->member_id);
        $loanType = LoanType::findOrFail($request->loan_type_id);

        // Check permissions
        if ($user->role === 'member' && $member->id !== $user->id) {
            abort(403, 'You can only apply for loans for yourself.');
        }

        // Validate loan amount against loan type limits
        if ($request->amount < $loanType->minimum_amount || $request->amount > $loanType->maximum_amount) {
            return back()->withErrors(['amount' => "Loan amount must be between {$loanType->minimum_amount} and {$loanType->maximum_amount}"]);
        }

        try {
            DB::beginTransaction();

            // Calculate loan details
            $interestRate = $loanType->interest_rate;
            $termPeriod = $request->term_period;
            $amount = $request->amount;

            // Calculate due date
            $dueDate = now()->addMonths($termPeriod);

            // Check member's savings balance
            $memberSavingsBalance = $member->accounts()->where('account_type', 'savings')->sum('balance');
            $memberSharesBalance = $member->accounts()->where('account_type', 'shares')->sum('balance');
            $memberTotalBalance = $memberSavingsBalance + $memberSharesBalance;

            // Calculate required savings (typically 3x the loan amount)
            $requiredSavingsMultiplier = 3;
            $minimumSavingsBalance = $amount * $requiredSavingsMultiplier;

            // Check membership duration (typically 6 months minimum)
            $minimumMembershipMonths = 6;
            $memberMonthsInSacco = (int) $member->created_at->diffInMonths(now());

            // Evaluate criteria
            $meetsSavingsCriteria = $memberTotalBalance >= $minimumSavingsBalance;
            $meetsMembershipCriteria = $memberMonthsInSacco >= $minimumMembershipMonths;

            $loan = Loan::create([
                'member_id' => $member->id,
                'loan_type_id' => $loanType->id,
                'amount' => $amount,
                'interest_rate' => $interestRate,
                'term_period' => $termPeriod,
                'status' => Loan::STATUS_PENDING,
                'due_date' => $dueDate,
                'collateral_details' => $request->collateral_details,
                'required_savings_multiplier' => $requiredSavingsMultiplier,
                'minimum_savings_balance' => $minimumSavingsBalance,
                'member_savings_balance' => $memberSavingsBalance,
                'member_shares_balance' => $memberSharesBalance,
                'member_total_balance' => $memberTotalBalance,
                'minimum_membership_months' => $minimumMembershipMonths,
                'member_months_in_sacco' => $memberMonthsInSacco,
                'meets_savings_criteria' => $meetsSavingsCriteria,
                'meets_membership_criteria' => $meetsMembershipCriteria,
                'criteria_evaluation_notes' => $this->generateCriteriaNotes($meetsSavingsCriteria, $meetsMembershipCriteria, $memberTotalBalance, $minimumSavingsBalance, $memberMonthsInSacco, $minimumMembershipMonths),
            ]);

            DB::commit();

            // Send notification to member
            Notification::create([
                'user_id' => $member->id,
                'type' => Notification::TYPE_INFO,
                'title' => 'Loan Application Submitted',
                'message' => "Your loan application for {$loanType->name} (KES " . number_format($request->amount) . ") has been submitted and is under review.",
                'action_url' => "/loans/{$loan->id}",
                'action_text' => 'View Application',
                'priority' => Notification::PRIORITY_NORMAL,
                'category' => Notification::CATEGORY_LOAN,
            ]);

            // Send notification to admins/managers
            $admins = User::whereIn('role', ['admin', 'manager'])->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'type' => Notification::TYPE_ALERT,
                    'title' => 'New Loan Application',
                    'message' => "New loan application from {$member->name} for {$loanType->name} (KES " . number_format($request->amount) . ")",
                    'action_url' => "/loans/{$loan->id}",
                    'action_text' => 'Review Application',
                    'priority' => Notification::PRIORITY_HIGH,
                    'category' => Notification::CATEGORY_LOAN,
                ]);
            }

            return redirect()->route('loans.show', $loan)
                           ->with('success', 'Loan application submitted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to submit loan application: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    private function generateCriteriaNotes($meetsSavings, $meetsMembership, $totalBalance, $requiredBalance, $monthsInSacco, $requiredMonths): string
    {
        $notes = [];
        
        if ($meetsSavings) {
            $notes[] = "✓ Meets savings criteria (KSh " . number_format($totalBalance) . " >= KSh " . number_format($requiredBalance) . ")";
        } else {
            $notes[] = "✗ Does not meet savings criteria (KSh " . number_format($totalBalance) . " < KSh " . number_format($requiredBalance) . ")";
        }
        
        if ($meetsMembership) {
            $notes[] = "✓ Meets membership criteria ({$monthsInSacco} months >= {$requiredMonths} months)";
        } else {
            $notes[] = "✗ Does not meet membership criteria ({$monthsInSacco} months < {$requiredMonths} months)";
        }
        
        return implode("\n", $notes);
    }

    public function show(Loan $loan): Response
    {
        $user = Auth::user();
        
        // Check permissions
        if ($user->role === 'member' && $loan->member_id !== $user->id) {
            abort(403, 'You can only view your own loans.');
        }

        $loan->load(['member', 'loanType', 'transactions', 'loanAccount.ledgerEntries']);

        return Inertia::render('loans/show', [
            'loan' => $loan,
        ]);
    }

    public function approve(Request $request, Loan $loan)
    {
        $this->authorize('approve', $loan);

        // Validate that loan is in pending status
        if ($loan->status !== Loan::STATUS_PENDING) {
            return back()->withErrors(['error' => 'Only pending loans can be approved.']);
        }

        try {
            DB::beginTransaction();

            $loan->update([
                'status' => Loan::STATUS_APPROVED,
                'approved_at' => now(),
                'approved_by' => Auth::id(),
                'approval_notes' => $request->input('notes', ''),
            ]);

            DB::commit();

            // Send notification to member
            Notification::create([
                'user_id' => $loan->member_id,
                'type' => Notification::TYPE_INFO,
                'title' => 'Loan Approved',
                'message' => "Your loan application for {$loan->loanType->name} (KES " . number_format($loan->amount) . ") has been approved.",
                'action_url' => "/loans/{$loan->id}",
                'action_text' => 'View Loan Details',
                'priority' => Notification::PRIORITY_HIGH,
                'category' => Notification::CATEGORY_LOAN,
            ]);

            return back()->with('success', 'Loan has been approved successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to approve loan: ' . $e->getMessage()]);
        }
    }

    public function reject(Request $request, Loan $loan)
    {
        $this->authorize('reject', $loan);

        // Validate that loan is in pending status
        if ($loan->status !== Loan::STATUS_PENDING) {
            return back()->withErrors(['error' => 'Only pending loans can be rejected.']);
        }

        try {
            DB::beginTransaction();

            $loan->update([
                'status' => Loan::STATUS_REJECTED,
                'rejected_at' => now(),
                'rejected_by' => Auth::id(),
                'rejection_reason' => $request->input('reason', ''),
            ]);

            DB::commit();

            // Send notification to member
            Notification::create([
                'user_id' => $loan->member_id,
                'type' => Notification::TYPE_ALERT,
                'title' => 'Loan Rejected',
                'message' => "Your loan application for {$loan->loanType->name} (KES " . number_format($loan->amount) . ") has been rejected. Reason: {$request->input('reason', 'No reason provided')}",
                'action_url' => "/loans/{$loan->id}",
                'action_text' => 'View Loan Details',
                'priority' => Notification::PRIORITY_HIGH,
                'category' => Notification::CATEGORY_LOAN,
            ]);

            return back()->with('success', 'Loan has been rejected successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to reject loan: ' . $e->getMessage()]);
        }
    }

    public function disburse(Request $request, Loan $loan)
    {
        $this->authorize('disburse', $loan);

        // Validate that loan is in approved status
        if ($loan->status !== Loan::STATUS_APPROVED) {
            return back()->withErrors(['error' => 'Only approved loans can be disbursed.']);
        }

        // Check if loan account already exists
        if ($loan->loanAccount) {
            return back()->withErrors(['error' => 'This loan has already been disbursed.']);
        }

        try {
            DB::beginTransaction();

            $member = $loan->member;
            $loanType = $loan->loanType;

            // Determine loan type based on loan type name or other criteria
            $accountLoanType = $this->determineLoanAccountType($loanType->name);
            
            // Determine interest basis (default to flat rate for now)
            $interestBasis = LoanAccount::INTEREST_FLAT_RATE;

            // Calculate loan details
            $principalAmount = $loan->amount;
            $interestRate = $loan->interest_rate;
            $termMonths = $loan->term_period;
            
            // Calculate fees (you can make this configurable)
            $processingFee = $principalAmount * 0.02; // 2% processing fee
            $insuranceFee = $principalAmount * 0.01; // 1% insurance fee
            $otherFees = 0;

            // Create a temporary loan account instance to calculate values
            $tempLoanAccount = new LoanAccount([
                'principal_amount' => $principalAmount,
                'interest_rate' => $interestRate,
                'interest_basis' => $interestBasis,
                'term_months' => $termMonths,
            ]);

            // Calculate monthly payment and total interest
            $monthlyPayment = $tempLoanAccount->calculateMonthlyPayment();
            $totalInterest = $tempLoanAccount->calculateTotalInterest();
            $totalPayable = $principalAmount + $totalInterest + $processingFee + $insuranceFee + $otherFees;

            // Create loan account with all calculated values
            $loanAccount = LoanAccount::create([
                'member_id' => $member->id,
                'loan_id' => $loan->id,
                'account_number' => LoanAccount::generateAccountNumber(),
                'loan_type' => $accountLoanType,
                'principal_amount' => $principalAmount,
                'interest_rate' => $interestRate,
                'interest_basis' => $interestBasis,
                'term_months' => $termMonths,
                'monthly_payment' => $monthlyPayment,
                'total_payable' => $totalPayable,
                'total_interest' => $totalInterest,
                'processing_fee' => $processingFee,
                'insurance_fee' => $insuranceFee,
                'other_fees' => $otherFees,
                'amount_disbursed' => $principalAmount,
                'outstanding_principal' => $principalAmount,
                'disbursement_date' => now()->toDateString(),
                'first_payment_date' => now()->addMonth()->toDateString(),
                'maturity_date' => now()->addMonths($termMonths)->toDateString(),
                'next_payment_date' => now()->addMonth()->toDateString(),
                'status' => LoanAccount::STATUS_ACTIVE,
                'notes' => $request->input('notes', ''),
            ]);

            // Generate and store payment schedule
            $loanAccount->update([
                'payment_schedule' => $loanAccount->generatePaymentSchedule(),
            ]);

            // Create disbursement ledger entry
            LedgerEntry::create([
                'loan_account_id' => $loanAccount->id,
                'transaction_type' => LedgerEntry::TYPE_DISBURSEMENT,
                'amount' => $principalAmount,
                'principal_amount' => $principalAmount,
                'balance_before' => 0,
                'balance_after' => $principalAmount,
                'reference_number' => 'DISB-' . $loanAccount->account_number,
                'description' => "Loan disbursement - {$loanType->name}",
                'transaction_date' => now()->toDateString(),
                'processed_by' => Auth::id(),
                'metadata' => [
                    'loan_type' => $accountLoanType,
                    'interest_basis' => $interestBasis,
                    'processing_fee' => $processingFee,
                    'insurance_fee' => $insuranceFee,
                ],
            ]);

            // Create a special loan account for the disbursement
            $loanAccountRecord = $member->accounts()->create([
                'account_type' => Account::TYPE_LOAN_ACCOUNT,
                'account_number' => $loanAccount->account_number, // Use the same account number
                'balance' => $principalAmount, // Initial balance is the disbursed amount
                'status' => 'active',
            ]);

            // Create general transaction record
            $balanceBefore = 0; // Loan account starts with 0 balance
            $balanceAfter = $principalAmount; // Balance after disbursement
            
            $member->transactions()->create([
                'type' => 'loan_disbursement',
                'amount' => $principalAmount,
                'description' => "Loan disbursement - {$loanType->name} (Account: {$loanAccount->account_number})",
                'reference_number' => 'DISB-' . $loanAccount->account_number,
                'status' => 'completed',
                'account_id' => $loanAccountRecord->id, // Use the loan account
                'loan_id' => $loan->id,
                'loan_account_id' => $loanAccount->id,
                'processed_by' => Auth::id(),
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
            ]);

            // Update the loan status to disbursed
            $loan->update([
                'status' => Loan::STATUS_DISBURSED,
                'disbursement_date' => now(),
                'disbursed_by' => Auth::id(),
                'disbursement_notes' => $request->input('notes', ''),
            ]);

            DB::commit();

            // Send notification to member
            Notification::create([
                'user_id' => $loan->member_id,
                'type' => Notification::TYPE_INFO,
                'title' => 'Loan Disbursed',
                'message' => "Your loan of KES " . number_format($principalAmount) . " has been disbursed to your account. Loan Account: {$loanAccount->account_number}",
                'action_url' => "/loan-accounts/{$loanAccount->id}",
                'action_text' => 'View Loan Account',
                'priority' => Notification::PRIORITY_HIGH,
                'category' => Notification::CATEGORY_LOAN,
            ]);

            return back()->with('success', "Loan has been disbursed successfully! Loan Account: {$loanAccount->account_number}");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to disburse loan: ' . $e->getMessage()]);
        }
    }

    private function determineLoanAccountType(string $loanTypeName): string
    {
        $name = strtolower($loanTypeName);
        
        if (str_contains($name, 'salary')) {
            return LoanAccount::TYPE_SALARY_BACKED;
        } elseif (str_contains($name, 'asset')) {
            return LoanAccount::TYPE_ASSET_BACKED;
        } elseif (str_contains($name, 'group')) {
            return LoanAccount::TYPE_GROUP_LOAN;
        } elseif (str_contains($name, 'business')) {
            return LoanAccount::TYPE_BUSINESS_LOAN;
        } elseif (str_contains($name, 'emergency')) {
            return LoanAccount::TYPE_EMERGENCY;
        }
        
        // Default to salary backed
        return LoanAccount::TYPE_SALARY_BACKED;
    }
}
