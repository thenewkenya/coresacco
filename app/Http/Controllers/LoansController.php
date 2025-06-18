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
        
        // For members, check if they have an active loan
        if ($user->role === 'member') {
            $activeLoan = $user->loans()->where('status', Loan::STATUS_ACTIVE)->first();
            if ($activeLoan) {
                return redirect()->route('loans.my')->with('error', 'You already have an active loan. Please repay it before applying for a new one.');
            }
        }

        $loanTypes = LoanType::where('status', LoanType::STATUS_ACTIVE)->get();
        $memberAccounts = $user->accounts()->where('status', Account::STATUS_ACTIVE)->get();
        
        // For staff/admin, get all members
        $members = null;
        if (in_array($user->role, ['admin', 'manager', 'staff'])) {
            $members = User::where('role', 'member')->get();
        }

        return view('loans.create', compact('loanTypes', 'memberAccounts', 'members'));
    }

    /**
     * Store loan application
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Debug logging
        \Log::info('Loan application submitted', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'request_data' => $request->all()
        ]);
        
        // Determine member ID based on user role
        $memberId = $user->role === 'member' ? $user->id : $request->member_id;
        
        // For members, check if they have an active loan
        if ($user->role === 'member') {
            $activeLoan = $user->loans()->where('status', Loan::STATUS_ACTIVE)->first();
            if ($activeLoan) {
                return back()->with('error', 'You already have an active loan.');
            }
        }

        try {
            // Different validation rules for members vs staff/admin
            $rules = [
                'member_id' => $user->role === 'member' ? 'nullable' : 'required|exists:users,id',
                'loan_type_id' => 'required|exists:loan_types,id',
                'amount' => 'required|numeric|min:1000',
                'term_period' => 'required|integer|min:1|max:60',
                'purpose' => 'required|string|max:500',
                'purpose_description' => 'required|string|max:1000',
                
                // Terms acceptance
                'terms_accepted' => 'required|accepted',
                'guarantor_consent' => 'required|accepted',
                'information_accuracy' => 'required|accepted',
            ];
            
            // For members, make guarantor info required, for staff/admin make it optional
            if ($user->role === 'member') {
                $rules = array_merge($rules, [
                    'guarantor_1_name' => 'required|string|max:255',
                    'guarantor_1_phone' => 'required|string|max:20',
                    'guarantor_1_id_number' => 'required|string|max:20',
                    'guarantor_1_relationship' => 'required|string|max:100',
                    'guarantor_2_name' => 'required|string|max:255',
                    'guarantor_2_phone' => 'required|string|max:20',
                    'guarantor_2_id_number' => 'required|string|max:20',
                    'guarantor_2_relationship' => 'required|string|max:100',
                ]);
            } else {
                $rules = array_merge($rules, [
                    'guarantor_1_name' => 'nullable|string|max:255',
                    'guarantor_1_phone' => 'nullable|string|max:20',
                    'guarantor_1_id_number' => 'nullable|string|max:20',
                    'guarantor_1_relationship' => 'nullable|string|max:100',
                    'guarantor_2_name' => 'nullable|string|max:255',
                    'guarantor_2_phone' => 'nullable|string|max:20',
                    'guarantor_2_id_number' => 'nullable|string|max:20',
                    'guarantor_2_relationship' => 'nullable|string|max:100',
                    
                    // Admin overrides
                    'custom_interest_rate' => 'nullable|numeric|min:0|max:100',
                    'custom_processing_fee' => 'nullable|numeric|min:0|max:100',
                    'priority' => 'nullable|in:normal,high,urgent',
                    'skip_approval' => 'nullable|boolean',
                ]);
            }
            
            // Collateral (conditional)
            $rules = array_merge($rules, [
                'collateral_type' => 'nullable|string|max:100',
                'collateral_description' => 'nullable|string|max:1000',
                'collateral_value' => 'nullable|numeric|min:0',
            ]);
            
            $validated = $request->validate($rules);
            
            \Log::info('Loan validation passed', ['validated_data' => $validated]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Loan validation failed', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);
            throw $e;
        }

        $loanType = LoanType::findOrFail($validated['loan_type_id']);

        // Validate loan amount against loan type limits
        if ($validated['amount'] < $loanType->minimum_amount || $validated['amount'] > $loanType->maximum_amount) {
            return back()->withInput()->with('error', 
                "Loan amount must be between KES " . number_format($loanType->minimum_amount) . 
                " and KES " . number_format($loanType->maximum_amount)
            );
        }

        // Validate term period against loan type options
        $termOptions = $loanType->term_options ?? [6, 12, 18, 24, 36, 48, 60];
        if (!in_array($validated['term_period'], $termOptions)) {
            return back()->withInput()->with('error', 'Invalid term period for this loan type.');
        }

        try {
            // Determine interest rate and processing fee
            $interestRate = $validated['custom_interest_rate'] ?? $loanType->interest_rate;
            $processingFeeRate = $validated['custom_processing_fee'] ?? $loanType->processing_fee;
            $processingFee = ($validated['amount'] * $processingFeeRate) / 100;

            // Determine initial status
            $initialStatus = Loan::STATUS_PENDING;
            if ($user->role === 'admin' && $validated['skip_approval']) {
                $initialStatus = Loan::STATUS_APPROVED;
            }

            $loan = Loan::create([
                'member_id' => $memberId,
                'loan_type_id' => $validated['loan_type_id'],
                'amount' => $validated['amount'],
                'interest_rate' => $interestRate,
                'term_period' => $validated['term_period'],
                'status' => $initialStatus,
                'collateral_details' => [
                    'type' => $validated['collateral_type'],
                    'description' => $validated['collateral_description'],
                    'value' => $validated['collateral_value'],
                ],
                'metadata' => [
                    'purpose' => $validated['purpose'],
                    'purpose_description' => $validated['purpose_description'],
                    'guarantors' => [
                        [
                            'name' => $validated['guarantor_1_name'],
                            'phone' => $validated['guarantor_1_phone'],
                            'id_number' => $validated['guarantor_1_id_number'],
                            'relationship' => $validated['guarantor_1_relationship'],
                        ],
                        [
                            'name' => $validated['guarantor_2_name'],
                            'phone' => $validated['guarantor_2_phone'],
                            'id_number' => $validated['guarantor_2_id_number'],
                            'relationship' => $validated['guarantor_2_relationship'],
                        ]
                    ],
                    'applied_at' => now(),
                    'processing_fee' => $processingFee,
                    'created_by' => $user->id,
                    'priority' => $validated['priority'] ?? 'normal',
                ]
            ]);

            // Send notification
            $this->notificationService->sendLoanNotification($loan, 'application');

            $message = $initialStatus === Loan::STATUS_APPROVED 
                ? 'Loan application created and approved successfully.'
                : 'Loan application submitted successfully. You will be notified once it\'s reviewed.';

            return redirect()->route('loans.show', $loan)->with('success', $message);

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
        $balance = $loan->calculateTotalRepayment();
        $monthlyInterest = $loan->amount * ($loan->interest_rate / 100 / 12);
        $monthlyPrincipal = $monthlyPayment - $monthlyInterest;
        
        for ($i = 1; $i <= $loan->term_period; $i++) {
            $dueDate = $startDate->copy()->addMonths($i);
            
            $schedule[] = [
                'month' => $i,
                'due_date' => $dueDate,
                'principal' => $monthlyPrincipal,
                'interest' => $monthlyInterest,
                'total_payment' => $monthlyPayment,
                'remaining_balance' => $balance - ($monthlyPayment * $i),
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