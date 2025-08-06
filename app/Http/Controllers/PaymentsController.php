<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Loan;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PaymentsController extends Controller
{
    protected TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * Display payment processing dashboard
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Authorization check
        if (!in_array($user->role, ['admin', 'manager', 'staff'])) {
            abort(403, 'Unauthorized access to payment processing.');
        }

        // Get filter parameters
        $search = $request->get('search');
        $status = $request->get('status');
        $type = $request->get('type');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        // Build query for transactions
        $query = Transaction::with(['account.member', 'member'])
            ->when($search, function ($q) use ($search) {
                $q->where(function($query) use ($search) {
                    $query->where('reference_number', 'like', "%{$search}%")
                          ->orWhere('description', 'like', "%{$search}%")
                          ->orWhereHas('member', function ($q) use ($search) {
                              $q->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                          });
                });
            })
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->when($type, function ($q) use ($type) {
                $q->where('type', $type);
            })
            ->when($dateFrom, function ($q) use ($dateFrom) {
                $q->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function ($q) use ($dateTo) {
                $q->whereDate('created_at', '<=', $dateTo);
            });

        $transactions = $query->latest()->paginate(20);

        // Get summary statistics
        $totalTransactions = Transaction::count();
        $pendingPayments = Transaction::where('status', Transaction::STATUS_PENDING)->count();
        $todayTransactions = Transaction::whereDate('created_at', today())->count();
        $todayAmount = Transaction::whereDate('created_at', today())
            ->where('status', Transaction::STATUS_COMPLETED)
            ->sum('amount');

        // Get payment methods summary
        $paymentMethods = [
            'cash' => Transaction::where('metadata->payment_method', 'cash')->count(),
            'mobile_money' => Transaction::where('metadata->payment_method', 'mobile_money')->count(),
            'bank_transfer' => Transaction::where('metadata->payment_method', 'bank_transfer')->count(),
            'cheque' => Transaction::where('metadata->payment_method', 'cheque')->count(),
        ];

        return view('payments.index', compact(
            'transactions',
            'totalTransactions',
            'pendingPayments',
            'todayTransactions',
            'todayAmount',
            'paymentMethods',
            'search',
            'status',
            'type',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Display member's own payments
     */
    public function my(Request $request)
    {
        $user = Auth::user();
        
        // Get member's payment history
        $transactions = Transaction::where('member_id', $user->id)
            ->with(['account', 'loan'])
            ->latest()
            ->paginate(20);

        // Get payment summary
        $totalPaid = Transaction::where('member_id', $user->id)
            ->where('status', Transaction::STATUS_COMPLETED)
            ->whereIn('type', [Transaction::TYPE_DEPOSIT, Transaction::TYPE_LOAN_REPAYMENT])
            ->sum('amount');

        $thisMonthPaid = Transaction::where('member_id', $user->id)
            ->where('status', Transaction::STATUS_COMPLETED)
            ->whereMonth('created_at', now()->month)
            ->whereIn('type', [Transaction::TYPE_DEPOSIT, Transaction::TYPE_LOAN_REPAYMENT])
            ->sum('amount');

        // Get pending payments
        $pendingPayments = Transaction::where('member_id', $user->id)
            ->where('status', Transaction::STATUS_PENDING)
            ->get();

        // Get upcoming loan payments
        $upcomingPayments = [];
        $activeLoans = $user->loans()->where('status', Loan::STATUS_ACTIVE)->get();
        foreach ($activeLoans as $loan) {
            $monthlyPayment = $loan->calculateMonthlyPayment();
            $nextDueDate = now()->addMonth()->startOfMonth();
            
            $upcomingPayments[] = [
                'loan' => $loan,
                'amount' => $monthlyPayment,
                'due_date' => $nextDueDate,
                'type' => 'loan_repayment'
            ];
        }

        return view('payments.my', compact(
            'transactions',
            'totalPaid',
            'thisMonthPaid',
            'pendingPayments',
            'upcomingPayments'
        ));
    }

    /**
     * Show payment form
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $paymentType = $request->get('type', 'deposit');
        
        // Get member's accounts
        $accounts = $user->accounts()->where('status', Account::STATUS_ACTIVE)->get();
        
        // Get active loans for loan repayment
        $activeLoans = $user->loans()->where('status', Loan::STATUS_ACTIVE)->get();

        // Payment methods
        $paymentMethods = [
            'cash' => 'Cash',
            'mobile_money' => 'Mobile Money (M-Pesa, Airtel Money)',
            'bank_transfer' => 'Bank Transfer',
            'cheque' => 'Cheque',
        ];

        return view('payments.create', compact('accounts', 'activeLoans', 'paymentMethods', 'paymentType'));
    }

    /**
     * Process payment
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'payment_type' => ['required', Rule::in(['loan_repayment', 'monthly_contribution', 'share_capital', 'insurance_premium', 'loan_processing_fee', 'membership_fee'])],
            'amount' => 'required|numeric|min:1',
            'account_id' => 'required|exists:accounts,id',
            'loan_id' => 'nullable|exists:loans,id',
            'payment_method' => ['required', Rule::in(['cash', 'mobile_money', 'bank_transfer', 'cheque'])],
            'reference_number' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:255',
            
            // Mobile money specific fields
            'mobile_number' => 'required_if:payment_method,mobile_money|nullable|string|max:15',
            'mobile_provider' => 'required_if:payment_method,mobile_money|nullable|string',
            
            // Bank transfer specific fields
            'bank_name' => 'required_if:payment_method,bank_transfer|nullable|string|max:100',
            'bank_account' => 'required_if:payment_method,bank_transfer|nullable|string|max:50',
            
            // Cheque specific fields
            'cheque_number' => 'required_if:payment_method,cheque|nullable|string|max:50',
            'cheque_date' => 'required_if:payment_method,cheque|nullable|date',
        ]);

        $account = Account::findOrFail($validated['account_id']);
        
        // Authorization check
        if ($account->member_id !== $user->id) {
            abort(403, 'Unauthorized access to this account.');
        }

        try {
            $metadata = [
                'payment_method' => $validated['payment_method'],
                'processed_by' => $user->id,
                'external_reference' => $validated['reference_number'] ?? null,
            ];

            // Add payment method specific metadata
            switch ($validated['payment_method']) {
                case 'mobile_money':
                    $metadata['mobile_number'] = $validated['mobile_number'];
                    $metadata['mobile_provider'] = $validated['mobile_provider'];
                    break;
                case 'bank_transfer':
                    $metadata['bank_name'] = $validated['bank_name'];
                    $metadata['bank_account'] = $validated['bank_account'];
                    break;
                case 'cheque':
                    $metadata['cheque_number'] = $validated['cheque_number'];
                    $metadata['cheque_date'] = $validated['cheque_date'];
                    break;
            }

            // Process payment based on type
                    switch ($validated['payment_type']) {
            case 'loan_repayment':
                if (!$validated['loan_id']) {
                    return back()->withInput()->with('error', 'Loan ID is required for loan repayment.');
                }
                
                $loan = Loan::findOrFail($validated['loan_id']);
                if ($loan->member_id !== $user->id) {
                    abort(403, 'Unauthorized access to this loan.');
                }

                $metadata['transaction_type'] = Transaction::TYPE_LOAN_REPAYMENT;
                $metadata['loan_id'] = $loan->id;

                $transaction = $this->transactionService->processWithdrawal(
                    $account,
                    $validated['amount'],
                    $validated['description'] ?? "Loan repayment",
                    $metadata
                );

                // Update loan status if needed
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
                    $message = 'Loan repayment processed successfully. Remaining balance: KES ' . number_format($remaining);
                }
                break;

            case 'monthly_contribution':
                $metadata['payment_type'] = 'monthly_contribution';
                $transaction = $this->transactionService->processDeposit(
                    $account,
                    $validated['amount'],
                    $validated['description'] ?? 'Monthly savings contribution',
                    $metadata
                );
                $message = 'Monthly contribution payment processed successfully.';
                break;

            case 'share_capital':
                $metadata['payment_type'] = 'share_capital';
                $transaction = $this->transactionService->processDeposit(
                    $account,
                    $validated['amount'],
                    $validated['description'] ?? 'Share capital purchase',
                    $metadata
                );
                $message = 'Share capital payment processed successfully.';
                break;

            case 'insurance_premium':
                $metadata['payment_type'] = 'insurance_premium';
                $transaction = $this->transactionService->processWithdrawal(
                    $account,
                    $validated['amount'],
                    $validated['description'] ?? 'Insurance premium payment',
                    $metadata
                );
                $message = 'Insurance premium payment processed successfully.';
                break;

            case 'loan_processing_fee':
                if (!$validated['loan_id']) {
                    return back()->withInput()->with('error', 'Loan ID is required for loan processing fee.');
                }
                
                $loan = Loan::findOrFail($validated['loan_id']);
                if ($loan->member_id !== $user->id) {
                    abort(403, 'Unauthorized access to this loan.');
                }

                $metadata['payment_type'] = 'loan_processing_fee';
                $metadata['loan_id'] = $loan->id;
                $transaction = $this->transactionService->processWithdrawal(
                    $account,
                    $validated['amount'],
                    $validated['description'] ?? 'Loan processing fee',
                    $metadata
                );
                $message = 'Loan processing fee payment processed successfully.';
                break;

            case 'membership_fee':
                $metadata['payment_type'] = 'membership_fee';
                $transaction = $this->transactionService->processWithdrawal(
                    $account,
                    $validated['amount'],
                    $validated['description'] ?? 'Annual membership fee',
                    $metadata
                );
                $message = 'Membership fee payment processed successfully.';
                break;

            default:
                return back()->withInput()->with('error', 'Invalid payment type.');
        }

            return redirect()->route('transactions.receipt', $transaction)
                ->with('success', $message);

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Payment processing failed: ' . $e->getMessage());
        }
    }

    /**
     * Show payment details
     */
    public function show(Transaction $transaction)
    {
        $user = Auth::user();
        
        // Authorization check
        if ($user->role === 'member' && $transaction->member_id !== $user->id) {
            abort(403, 'Unauthorized access to this payment.');
        }

        $transaction->load(['account.member', 'member', 'loan.loanType']);

        return view('payments.show', compact('transaction'));
    }

    /**
     * Approve pending payment
     */
    public function approve(Request $request, Transaction $transaction)
    {
        $user = Auth::user();
        
        // Authorization check
        if (!in_array($user->role, ['admin', 'manager'])) {
            abort(403, 'Unauthorized to approve payments.');
        }

        if ($transaction->status !== Transaction::STATUS_PENDING) {
            return back()->with('error', 'Only pending payments can be approved.');
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            // Update transaction status
            $transaction->update([
                'status' => Transaction::STATUS_COMPLETED,
                'metadata' => array_merge($transaction->metadata ?? [], [
                    'approved_by' => $user->id,
                    'approved_at' => now(),
                    'approval_notes' => $validated['notes'] ?? null,
                ])
            ]);

            // Update account balance if needed
            if ($transaction->account) {
                $account = $transaction->account;
                if ($transaction->type === Transaction::TYPE_DEPOSIT) {
                    $account->balance += $transaction->amount;
                } elseif ($transaction->type === Transaction::TYPE_WITHDRAWAL) {
                    $account->balance -= $transaction->amount;
                }
                $account->save();

                // Update balance_after in transaction
                $transaction->update(['balance_after' => $account->balance]);
            }

            DB::commit();

            return back()->with('success', 'Payment approved successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve payment: ' . $e->getMessage());
        }
    }

    /**
     * Reject pending payment
     */
    public function reject(Request $request, Transaction $transaction)
    {
        $user = Auth::user();
        
        // Authorization check
        if (!in_array($user->role, ['admin', 'manager'])) {
            abort(403, 'Unauthorized to reject payments.');
        }

        if ($transaction->status !== Transaction::STATUS_PENDING) {
            return back()->with('error', 'Only pending payments can be rejected.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $transaction->update([
            'status' => Transaction::STATUS_FAILED,
            'metadata' => array_merge($transaction->metadata ?? [], [
                'rejected_by' => $user->id,
                'rejected_at' => now(),
                'rejection_reason' => $validated['rejection_reason'],
            ])
        ]);

        return back()->with('success', 'Payment rejected.');
    }

    /**
     * Reverse completed payment (Admin only)
     */
    public function reverse(Request $request, Transaction $transaction)
    {
        $user = Auth::user();
        
        // Authorization check - only admins can reverse payments
        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized to reverse payments. Admin access required.');
        }

        // Can only reverse completed transactions
        if ($transaction->status !== Transaction::STATUS_COMPLETED) {
            return back()->with('error', 'Only completed payments can be reversed.');
        }

        // Prevent reversing already reversed transactions
        if ($transaction->status === Transaction::STATUS_REVERSED) {
            return back()->with('error', 'This payment has already been reversed.');
        }

        try {
            DB::beginTransaction();

            // Create reversal transaction record
            $reversalTransaction = Transaction::create([
                'account_id' => $transaction->account_id,
                'member_id' => $transaction->member_id,
                'loan_id' => $transaction->loan_id,
                'type' => $transaction->type === Transaction::TYPE_DEPOSIT ? Transaction::TYPE_WITHDRAWAL : Transaction::TYPE_DEPOSIT,
                'amount' => $transaction->amount,
                'description' => 'REVERSAL: ' . $transaction->description,
                'reference_number' => 'REV-' . $transaction->reference_number,
                'status' => Transaction::STATUS_COMPLETED,
                'balance_before' => $transaction->account ? $transaction->account->balance : 0,
                'metadata' => array_merge($transaction->metadata ?? [], [
                    'is_reversal' => true,
                    'original_transaction_id' => $transaction->id,
                    'reversed_by' => $user->id,
                    'reversed_at' => now(),
                    'reversal_reason' => $request->input('reason', 'Administrative reversal'),
                ])
            ]);

            // Update account balance (reverse the original transaction effect)
            if ($transaction->account) {
                $account = $transaction->account;
                if ($transaction->type === Transaction::TYPE_DEPOSIT) {
                    // Original was deposit, so subtract for reversal
                    $account->balance -= $transaction->amount;
                } elseif ($transaction->type === Transaction::TYPE_WITHDRAWAL) {
                    // Original was withdrawal, so add back for reversal
                    $account->balance += $transaction->amount;
                }
                $account->save();

                // Update balance_after for reversal transaction
                $reversalTransaction->update(['balance_after' => $account->balance]);
            }

            // Mark original transaction as reversed
            $transaction->update([
                'status' => Transaction::STATUS_REVERSED,
                'metadata' => array_merge($transaction->metadata ?? [], [
                    'reversed_by' => $user->id,
                    'reversed_at' => now(),
                    'reversal_transaction_id' => $reversalTransaction->id,
                    'reversal_reason' => $request->input('reason', 'Administrative reversal'),
                ])
            ]);

            DB::commit();

            return back()->with('success', 'Payment reversed successfully. Reversal transaction: ' . $reversalTransaction->reference_number);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to reverse payment: ' . $e->getMessage());
        }
    }

    /**
     * Generate payment receipt
     */
    public function receipt(Transaction $transaction)
    {
        $user = Auth::user();
        
        // Authorization check
        if ($user->role === 'member' && $transaction->member_id !== $user->id) {
            abort(403, 'Unauthorized access to this receipt.');
        }

        $transaction->load(['account.member', 'member', 'loan.loanType']);

        return view('payments.receipt', compact('transaction'));
    }

    /**
     * Process mobile money payment
     */
    public function mobileMoney(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'mobile_number' => 'required|string|max:15',
            'provider' => ['required', Rule::in(['mpesa', 'airtel', 'tkash'])],
            'account_id' => 'required|exists:accounts,id',
            'payment_type' => ['required', Rule::in(['loan_repayment', 'monthly_contribution', 'share_capital', 'insurance_premium', 'loan_processing_fee', 'membership_fee'])],
            'loan_id' => 'nullable|exists:loans,id',
        ]);

        $account = Account::findOrFail($validated['account_id']);
        
        // Authorization check
        if ($account->member_id !== $user->id) {
            abort(403, 'Unauthorized access to this account.');
        }

        try {
            // Simulate mobile money API integration
            $referenceNumber = $this->generateMobileMoneyReference($validated['provider']);
            
            $metadata = [
                'payment_method' => 'mobile_money',
                'mobile_number' => $validated['mobile_number'],
                'mobile_provider' => $validated['provider'],
                'external_reference' => $referenceNumber,
                'processed_by' => $user->id,
                'api_response' => [
                    'status' => 'success',
                    'transaction_id' => $referenceNumber,
                    'timestamp' => now(),
                ]
            ];

            if ($validated['payment_type'] === 'deposit') {
                $transaction = $this->transactionService->processDeposit(
                    $account,
                    $validated['amount'],
                    'Mobile money deposit - ' . strtoupper($validated['provider']),
                    $metadata
                );
            } else {
                $metadata['transaction_type'] = Transaction::TYPE_LOAN_REPAYMENT;
                $metadata['loan_id'] = $validated['loan_id'];
                
                $transaction = $this->transactionService->processWithdrawal(
                    $account,
                    $validated['amount'],
                    'Mobile money loan repayment - ' . strtoupper($validated['provider']),
                    $metadata
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Mobile money payment processed successfully.',
                'transaction_id' => $transaction->id,
                'reference_number' => $referenceNumber,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Mobile money payment failed: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Generate mobile money reference number
     */
    protected function generateMobileMoneyReference(string $provider): string
    {
        $prefix = strtoupper(substr($provider, 0, 2));
        return $prefix . date('Ymd') . strtoupper(substr(uniqid(), -8));
    }

    /**
     * Generate payments report
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
        $paymentMethod = $request->get('payment_method');

        // Build query
        $query = Transaction::with(['account.member', 'member'])
            ->whereBetween('created_at', [$startDate, $endDate]);
        
        if ($paymentMethod) {
            $query->where('metadata->payment_method', $paymentMethod);
        }

        $transactions = $query->get();

        // Calculate statistics
        $stats = [
            'total_transactions' => $transactions->count(),
            'total_amount' => $transactions->where('status', Transaction::STATUS_COMPLETED)->sum('amount'),
            'pending_transactions' => $transactions->where('status', Transaction::STATUS_PENDING)->count(),
            'failed_transactions' => $transactions->where('status', Transaction::STATUS_FAILED)->count(),
            'payment_methods' => [
                'cash' => $transactions->where('metadata.payment_method', 'cash')->count(),
                'mobile_money' => $transactions->where('metadata.payment_method', 'mobile_money')->count(),
                'bank_transfer' => $transactions->where('metadata.payment_method', 'bank_transfer')->count(),
                'cheque' => $transactions->where('metadata.payment_method', 'cheque')->count(),
            ],
        ];

        return view('payments.report', compact('transactions', 'stats', 'startDate', 'endDate', 'paymentMethod'));
    }
} 