<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanType;
use App\Models\User;
use App\Models\Account;
use App\Models\Transaction;
use App\Services\TransactionService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LoansController extends Controller
{
    protected TransactionService $transactionService;
    protected NotificationService $notificationService;

    public function __construct(TransactionService $transactionService, NotificationService $notificationService)
    {
        $this->transactionService = $transactionService;
        $this->notificationService = $notificationService;
    }

    /**
     * Display loan management dashboard
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Authorization check
        if (!in_array($user->role, ['admin', 'manager', 'staff'])) {
            abort(403, 'Unauthorized access to loan management.');
        }

        // Get filter parameters
        $search = $request->get('search');
        $status = $request->get('status');
        $loanType = $request->get('loan_type');
        $branch = $request->get('branch');

        // Build query
        $query = Loan::with(['member', 'loanType'])
            ->when($search, function ($q) use ($search) {
                $q->where(function($query) use ($search) {
                    $query->whereHas('member', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    });
                });
            })
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->when($loanType, function ($q) use ($loanType) {
                $q->where('loan_type_id', $loanType);
            });

        $loans = $query->latest()->paginate(20);

        // Get summary statistics
        $totalLoans = Loan::count();
        $activeLoans = Loan::where('status', Loan::STATUS_ACTIVE)->count();
        $pendingLoans = Loan::where('status', Loan::STATUS_PENDING)->count();
        $totalLoanAmount = Loan::whereIn('status', [Loan::STATUS_ACTIVE, Loan::STATUS_DISBURSED])->sum('amount');
        $thisMonthDisbursements = Loan::where('status', Loan::STATUS_DISBURSED)
            ->whereMonth('disbursement_date', now()->month)
            ->sum('amount');

        // Get loan types for filters
        $loanTypes = LoanType::where('status', LoanType::STATUS_ACTIVE)->get();

        return view('loans.index', compact(
            'loans',
            'totalLoans',
            'activeLoans',
            'pendingLoans',
            'totalLoanAmount',
            'thisMonthDisbursements',
            'loanTypes',
            'search',
            'status',
            'loanType'
        ));
    }

    /**
     * Display member's own loans
     */
    public function my(Request $request)
    {
        $user = Auth::user();
        
        // Get member's loans with related data
        $loans = $user->loans()
            ->with(['loanType', 'transactions'])
            ->latest()
            ->get();

        // Calculate summary statistics
        $totalBorrowed = $loans->sum('amount');
        $activeLoan = $loans->where('status', Loan::STATUS_ACTIVE)->first();
        $totalRepaid = $user->transactions()
            ->where('type', Transaction::TYPE_LOAN_REPAYMENT)
            ->where('status', Transaction::STATUS_COMPLETED)
            ->sum('amount');

        // Get available loan types
        $availableLoanTypes = LoanType::where('status', LoanType::STATUS_ACTIVE)->get();

        // Check eligibility for new loans
        $canApplyForLoan = !$activeLoan; // Simple rule: no active loan

        return view('loans.my', compact(
            'loans',
            'totalBorrowed',
            'activeLoan',
            'totalRepaid',
            'availableLoanTypes',
            'canApplyForLoan'
        ));
    }

    /**
     * Show loan details
     */
    public function show(Loan $loan)
    {
        $user = Auth::user();
        
        // Authorization check
        if ($user->role === 'member' && $loan->member_id !== $user->id) {
            abort(403, 'Unauthorized access to this loan.');
        }

        $loan->load(['member', 'loanType', 'transactions']);
        
        // Calculate loan details
        $totalRepaid = $loan->transactions()
            ->where('type', Transaction::TYPE_LOAN_REPAYMENT)
            ->where('status', Transaction::STATUS_COMPLETED)
            ->sum('amount');
        
        $remainingBalance = $loan->calculateTotalRepayment() - $totalRepaid;
        $monthlyPayment = $loan->calculateMonthlyPayment();
        
        // Get repayment schedule
        $repaymentSchedule = $this->generateRepaymentSchedule($loan);

        return view('loans.show', compact('loan', 'totalRepaid', 'remainingBalance', 'monthlyPayment', 'repaymentSchedule'));
    }

    /**
     * Show loan application form
     */
    public function create()
    {
        $user = Auth::user();
        
        if ($user->role !== 'member') {
            abort(403, 'Only members can apply for loans.');
        }

        // Check if member has active loan
        $activeLoan = $user->loans()->where('status', Loan::STATUS_ACTIVE)->first();
        if ($activeLoan) {
            return redirect()->route('loans.my')->with('error', 'You already have an active loan. Please repay it before applying for a new one.');
        }

        $loanTypes = LoanType::where('status', LoanType::STATUS_ACTIVE)->get();
        $memberAccounts = $user->accounts()->where('status', Account::STATUS_ACTIVE)->get();

        return view('loans.create', compact('loanTypes', 'memberAccounts'));
    }

    /**
     * Store loan application
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'member') {
            abort(403, 'Only members can apply for loans.');
        }

        // Check if member has active loan
        $activeLoan = $user->loans()->where('status', Loan::STATUS_ACTIVE)->first();
        if ($activeLoan) {
            return back()->with('error', 'You already have an active loan.');
        }

        $validated = $request->validate([
            'loan_type_id' => 'required|exists:loan_types,id',
            'amount' => 'required|numeric|min:1000',
            'term_period' => 'required|integer|min:1|max:60',
            'purpose' => 'required|string|max:500',
            'collateral_details' => 'nullable|array',
            'guarantor_1_name' => 'nullable|string|max:255',
            'guarantor_1_phone' => 'nullable|string|max:20',
            'guarantor_2_name' => 'nullable|string|max:255',
            'guarantor_2_phone' => 'nullable|string|max:20',
        ]);

        $loanType = LoanType::findOrFail($validated['loan_type_id']);

        // Validate loan amount against loan type limits
        if (!$loanType->isEligibleAmount($validated['amount'])) {
            return back()->withInput()->with('error', 
                "Loan amount must be between KES " . number_format($loanType->minimum_amount) . 
                " and KES " . number_format($loanType->maximum_amount)
            );
        }

        // Validate term period
        if (!$loanType->isValidTerm($validated['term_period'])) {
            return back()->withInput()->with('error', 'Invalid term period for this loan type.');
        }

        try {
            $loan = Loan::create([
                'member_id' => $user->id,
                'loan_type_id' => $validated['loan_type_id'],
                'amount' => $validated['amount'],
                'interest_rate' => $loanType->interest_rate,
                'term_period' => $validated['term_period'],
                'status' => Loan::STATUS_PENDING,
                'collateral_details' => $validated['collateral_details'] ?? [],
                'metadata' => [
                    'purpose' => $validated['purpose'],
                    'guarantors' => [
                        [
                            'name' => $validated['guarantor_1_name'] ?? null,
                            'phone' => $validated['guarantor_1_phone'] ?? null,
                        ],
                        [
                            'name' => $validated['guarantor_2_name'] ?? null,
                            'phone' => $validated['guarantor_2_phone'] ?? null,
                        ]
                    ],
                    'applied_at' => now(),
                    'processing_fee' => $loanType->calculateProcessingFee($validated['amount']),
                ]
            ]);

            // Send notification
            $this->notificationService->sendLoanNotification($loan, 'application');

            return redirect()->route('loans.show', $loan)
                ->with('success', 'Loan application submitted successfully. You will be notified once it\'s reviewed.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to submit loan application: ' . $e->getMessage());
        }
    }

    /**
     * Approve loan
     */
    public function approve(Request $request, Loan $loan)
    {
        $user = Auth::user();
        
        // Authorization check
        if (!in_array($user->role, ['admin', 'manager'])) {
            abort(403, 'Unauthorized to approve loans.');
        }

        if ($loan->status !== Loan::STATUS_PENDING) {
            return back()->with('error', 'Only pending loans can be approved.');
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
            'disbursement_account_id' => 'required|exists:accounts,id',
        ]);

        try {
            DB::beginTransaction();

            // Update loan status
            $loan->update([
                'status' => Loan::STATUS_APPROVED,
                'due_date' => now()->addMonths($loan->term_period),
                'metadata' => array_merge($loan->metadata ?? [], [
                    'approved_by' => $user->id,
                    'approved_at' => now(),
                    'approval_notes' => $validated['notes'] ?? null,
                ])
            ]);

            // Disburse loan to member's account
            $account = Account::findOrFail($validated['disbursement_account_id']);
            
            $transaction = $this->transactionService->processDeposit(
                $account,
                $loan->amount,
                "Loan disbursement - {$loan->loanType->name}",
                [
                    'transaction_type' => Transaction::TYPE_LOAN_DISBURSEMENT,
                    'loan_id' => $loan->id,
                    'disbursed_by' => $user->id
                ]
            );

            // Update loan status to disbursed
            $loan->update([
                'status' => Loan::STATUS_DISBURSED,
                'disbursement_date' => now(),
            ]);

            DB::commit();

            // Send notification
            $this->notificationService->sendLoanNotification($loan, 'approval');

            return back()->with('success', 'Loan approved and disbursed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve loan: ' . $e->getMessage());
        }
    }

    /**
     * Reject loan
     */
    public function reject(Request $request, Loan $loan)
    {
        $user = Auth::user();
        
        // Authorization check
        if (!in_array($user->role, ['admin', 'manager'])) {
            abort(403, 'Unauthorized to reject loans.');
        }

        if ($loan->status !== Loan::STATUS_PENDING) {
            return back()->with('error', 'Only pending loans can be rejected.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $loan->update([
            'status' => Loan::STATUS_REJECTED,
            'metadata' => array_merge($loan->metadata ?? [], [
                'rejected_by' => $user->id,
                'rejected_at' => now(),
                'rejection_reason' => $validated['rejection_reason'],
            ])
        ]);

        // Send notification
        $this->notificationService->sendLoanNotification($loan, 'rejection');

        return back()->with('success', 'Loan application rejected.');
    }

    /**
     * Process loan repayment
     */
    public function repayment(Request $request, Loan $loan)
    {
        $user = Auth::user();
        
        // Authorization check
        if ($user->role === 'member' && $loan->member_id !== $user->id) {
            abort(403, 'Unauthorized access to this loan.');
        }

        if (!in_array($loan->status, [Loan::STATUS_ACTIVE, Loan::STATUS_DISBURSED])) {
            return back()->with('error', 'Cannot process repayment for this loan status.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_account_id' => 'required|exists:accounts,id',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            $account = Account::findOrFail($validated['payment_account_id']);
            
            // Process withdrawal from member's account
            $transaction = $this->transactionService->processWithdrawal(
                $account,
                $validated['amount'],
                $validated['description'] ?? "Loan repayment",
                [
                    'transaction_type' => Transaction::TYPE_LOAN_REPAYMENT,
                    'loan_id' => $loan->id,
                    'processed_by' => $user->id
                ]
            );

            // Check if loan is fully repaid
            $totalRepaid = $loan->transactions()
                ->where('type', Transaction::TYPE_LOAN_REPAYMENT)
                ->where('status', Transaction::STATUS_COMPLETED)
                ->sum('amount');
            
            $totalDue = $loan->calculateTotalRepayment();
            
            if ($totalRepaid >= $totalDue) {
                $loan->update(['status' => Loan::STATUS_COMPLETED]);
                $message = 'Loan repayment processed successfully. Loan is now fully repaid!';
            } else {
                $loan->update(['status' => Loan::STATUS_ACTIVE]);
                $remaining = $totalDue - $totalRepaid;
                $message = 'Loan repayment of KES ' . number_format($validated['amount']) . ' processed successfully. Remaining balance: KES ' . number_format($remaining);
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Repayment failed: ' . $e->getMessage());
        }
    }

    /**
     * Generate repayment schedule
     */
    protected function generateRepaymentSchedule(Loan $loan): array
    {
        $schedule = [];
        $monthlyPayment = $loan->calculateMonthlyPayment();
        $startDate = $loan->disbursement_date ?? now();
        
        for ($i = 1; $i <= $loan->term_period; $i++) {
            $dueDate = $startDate->copy()->addMonths($i);
            $schedule[] = [
                'month' => $i,
                'due_date' => $dueDate,
                'amount' => $monthlyPayment,
                'status' => $dueDate->isPast() ? 'overdue' : 'pending'
            ];
        }
        
        return $schedule;
    }

    /**
     * Generate loans report
     */
    public function report(Request $request)
    {
        $user = Auth::user();
        
        // Authorization check
        if (!in_array($user->role, ['admin', 'manager'])) {
            abort(403, 'Unauthorized to generate reports.');
        }

        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());
        $loanTypeId = $request->get('loan_type_id');

        // Build query
        $query = Loan::with(['member', 'loanType']);
        
        if ($loanTypeId) {
            $query->where('loan_type_id', $loanTypeId);
        }

        $loans = $query->whereBetween('created_at', [$startDate, $endDate])->get();

        // Calculate statistics
        $stats = [
            'total_applications' => $loans->count(),
            'approved_loans' => $loans->whereIn('status', [Loan::STATUS_APPROVED, Loan::STATUS_DISBURSED, Loan::STATUS_ACTIVE])->count(),
            'rejected_loans' => $loans->where('status', Loan::STATUS_REJECTED)->count(),
            'total_disbursed' => $loans->whereIn('status', [Loan::STATUS_DISBURSED, Loan::STATUS_ACTIVE, Loan::STATUS_COMPLETED])->sum('amount'),
            'total_repaid' => Transaction::where('type', Transaction::TYPE_LOAN_REPAYMENT)
                ->where('status', Transaction::STATUS_COMPLETED)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount'),
            'active_loans' => Loan::where('status', Loan::STATUS_ACTIVE)->count(),
            'overdue_loans' => Loan::where('status', Loan::STATUS_ACTIVE)
                ->where('due_date', '<', now())
                ->count(),
        ];

        $loanTypes = LoanType::all();

        return view('loans.report', compact('loans', 'stats', 'loanTypes', 'startDate', 'endDate', 'loanTypeId'));
    }
} 