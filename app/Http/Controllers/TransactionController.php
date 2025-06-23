<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
class TransactionController extends Controller
{
    protected TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * Display transaction dashboard
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role === 'member') {
            // Member view - show only their own accounts and transactions
            $accounts = $user->accounts()->with(['transactions' => function($query) {
                $query->where('member_id', auth()->id())->latest();
            }])->get();
            
            $recentTransactions = Transaction::where('member_id', $user->id)
                ->where(function($query) {
                    $query->whereNotNull('account_id')
                          ->orWhereIn('type', [Transaction::TYPE_LOAN_DISBURSEMENT, Transaction::TYPE_LOAN_REPAYMENT]);
                })
                ->with('account')
                ->latest()
                ->limit(10)
                ->get();
            
            $summary = [];
            foreach ($accounts as $account) {
                $summary[$account->id] = $this->transactionService->getAccountTransactionSummary($account);
            }

            return view('transactions.member-dashboard', compact('accounts', 'recentTransactions', 'summary'));
        } else {
            // Staff/Admin view - show all pending transactions and overview with filtering
            
            // Get filter parameters
            $status = $request->get('status', 'pending');
            $type = $request->get('type');
            $priority = $request->get('priority');
            $dateFrom = $request->get('date_from');
            $dateTo = $request->get('date_to');

            // Build query for transactions (default to pending)
            $query = Transaction::with(['account.member', 'member']);
            
            if ($status === 'pending') {
                $query->where('status', Transaction::STATUS_PENDING);
            } else {
                $query->where('status', $status);
            }

            // Apply filters
            if ($type) {
                $query->where('type', $type);
            }

            if ($priority === 'high') {
                $query->where('amount', '>=', TransactionService::LARGE_TRANSACTION_THRESHOLD);
            } elseif ($priority === 'normal') {
                $query->where('amount', '<', TransactionService::LARGE_TRANSACTION_THRESHOLD);
            }

            if ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            }

            if ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            }

            $pendingTransactions = $query->latest()->paginate(20);
            
            // Get summary statistics
            $stats = [
                'total_pending' => Transaction::where('status', Transaction::STATUS_PENDING)->count(),
                'high_priority' => Transaction::where('status', Transaction::STATUS_PENDING)
                    ->where('amount', '>=', TransactionService::LARGE_TRANSACTION_THRESHOLD)->count(),
                'today_pending' => Transaction::where('status', Transaction::STATUS_PENDING)
                    ->whereDate('created_at', today())->count(),
                'overdue' => Transaction::where('status', Transaction::STATUS_PENDING)
                    ->where('created_at', '<', now()->subHours(24))->count(),
            ];
            
            $todayStats = [
                'total_transactions' => Transaction::whereDate('created_at', today())->count(),
                'total_amount' => Transaction::whereDate('created_at', today())
                    ->where('status', Transaction::STATUS_COMPLETED)
                    ->sum('amount'),
                'pending_approvals' => $stats['total_pending'],
                'deposits' => Transaction::whereDate('created_at', today())
                    ->where('type', Transaction::TYPE_DEPOSIT)
                    ->where('status', Transaction::STATUS_COMPLETED)
                    ->sum('amount'),
                'withdrawals' => Transaction::whereDate('created_at', today())
                    ->where('type', Transaction::TYPE_WITHDRAWAL)
                    ->where('status', Transaction::STATUS_COMPLETED)
                    ->sum('amount'),
            ];

            // Get recent approval activity
            $recentActivity = Transaction::with(['account.member', 'member'])
                ->whereIn('status', [Transaction::STATUS_COMPLETED, Transaction::STATUS_FAILED])
                ->whereNotNull('metadata->approved_by')
                ->latest()
                ->limit(10)
                ->get();

            return view('transactions.staff-dashboard', compact(
                'pendingTransactions', 
                'todayStats', 
                'stats', 
                'recentActivity',
                'status',
                'type',
                'priority',
                'dateFrom',
                'dateTo'
            ));
        }
    }

    /**
     * Show deposit form
     */
    public function createDeposit()
    {
        $user = Auth::user();
        
        if ($user->role === 'member') {
            $accounts = $user->accounts()->where('status', Account::STATUS_ACTIVE)->get();
        } else {
            // Staff can deposit to any account
            $accounts = Account::where('status', Account::STATUS_ACTIVE)
                ->with('member')
                ->get();
        }

        return view('transactions.deposit', compact('accounts'));
    }

    /**
     * Process deposit
     */
    public function storeDeposit(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:1|max:500000',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            $account = Account::findOrFail($request->account_id);
            
            // Check permissions - members can only deposit to their own accounts
            if (Auth::user()->role === 'member' && $account->member_id !== Auth::id()) {
                return back()->withErrors(['account_id' => 'You can only deposit to your own accounts.']);
            }

            $transaction = $this->transactionService->processDeposit(
                $account,
                $request->amount,
                $request->description,
                ['channel' => 'web', 'ip_address' => $request->ip()]
            );

            $message = $transaction->status === Transaction::STATUS_PENDING 
                ? 'Deposit submitted successfully. Large amounts require approval.' 
                : 'Deposit processed successfully!';

            return redirect()->route('transactions.receipt', $transaction)
                ->with('success', $message);

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Deposit failed', ['error' => $e->getMessage(), 'request' => $request->all()]);
            return back()->with('error', 'Transaction failed. Please try again.')->withInput();
        }
    }

    /**
     * Show withdrawal form
     */
    public function createWithdrawal()
    {
        $user = Auth::user();
        
        if ($user->role === 'member') {
            $accounts = $user->accounts()
                ->where('status', Account::STATUS_ACTIVE)
                ->where('balance', '>', TransactionService::MINIMUM_BALANCE)
                ->get();
        } else {
            $accounts = Account::where('status', Account::STATUS_ACTIVE)
                ->where('balance', '>', TransactionService::MINIMUM_BALANCE)
                ->with('member')
                ->get();
        }

        // Get daily withdrawal limits info
        $limits = [
            'daily_limit' => TransactionService::DAILY_WITHDRAWAL_LIMIT,
            'minimum_balance' => TransactionService::MINIMUM_BALANCE,
            'large_transaction_threshold' => TransactionService::LARGE_TRANSACTION_THRESHOLD,
        ];

        return view('transactions.withdrawal', compact('accounts', 'limits'));
    }

    /**
     * Process withdrawal
     */
    public function storeWithdrawal(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:1|max:500000',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            $account = Account::findOrFail($request->account_id);
            
            // Check permissions
            if (Auth::user()->role === 'member' && $account->member_id !== Auth::id()) {
                return back()->withErrors(['account_id' => 'You can only withdraw from your own accounts.']);
            }

            $transaction = $this->transactionService->processWithdrawal(
                $account,
                $request->amount,
                $request->description,
                ['channel' => 'web', 'ip_address' => $request->ip()]
            );

            $message = $transaction->status === Transaction::STATUS_PENDING 
                ? 'Withdrawal submitted successfully. Large amounts require approval.' 
                : 'Withdrawal processed successfully!';

            return redirect()->route('transactions.receipt', $transaction)
                ->with('success', $message);

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Withdrawal failed', ['error' => $e->getMessage(), 'request' => $request->all()]);
            return back()->with('error', 'Transaction failed. Please try again.')->withInput();
        }
    }

    /**
     * Show transfer form
     */
    public function createTransfer()
    {
        $user = Auth::user();
        
        if ($user->role === 'member') {
            $fromAccounts = $user->accounts()
                ->where('status', Account::STATUS_ACTIVE)
                ->where('balance', '>', TransactionService::MINIMUM_BALANCE)
                ->get();
                
            // Members can only transfer to their own accounts
            $toAccounts = $user->accounts()
                ->where('status', Account::STATUS_ACTIVE)
                ->get();
        } else {
            // Staff can transfer between any accounts
            $fromAccounts = Account::where('status', Account::STATUS_ACTIVE)
                ->where('balance', '>', TransactionService::MINIMUM_BALANCE)
                ->with('member')
                ->get();
                
            $toAccounts = Account::where('status', Account::STATUS_ACTIVE)
                ->with('member')
                ->get();
        }

        return view('transactions.transfer', compact('fromAccounts', 'toAccounts'));
    }

    /**
     * Process transfer
     */
    public function storeTransfer(Request $request)
    {
        $request->validate([
            'from_account_id' => 'required|exists:accounts,id',
            'to_account_id' => 'required|exists:accounts,id|different:from_account_id',
            'amount' => 'required|numeric|min:1|max:500000',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            $fromAccount = Account::findOrFail($request->from_account_id);
            $toAccount = Account::findOrFail($request->to_account_id);
            
            // Check permissions for members
            if (Auth::user()->role === 'member') {
                if ($fromAccount->member_id !== Auth::id()) {
                    return back()->withErrors(['from_account_id' => 'You can only transfer from your own accounts.']);
                }
                if ($toAccount->member_id !== Auth::id()) {
                    return back()->withErrors(['to_account_id' => 'You can only transfer to your own accounts.']);
                }
            }

            $result = $this->transactionService->processTransfer(
                $fromAccount,
                $toAccount,
                $request->amount,
                $request->description,
                ['channel' => 'web', 'ip_address' => $request->ip()]
            );

            $message = $result['debit_transaction']->status === Transaction::STATUS_PENDING 
                ? 'Transfer submitted successfully. Large amounts require approval.' 
                : 'Transfer completed successfully!';

            return redirect()->route('transactions.receipt', $result['debit_transaction'])
                ->with('success', $message)
                ->with('transfer_reference', $result['reference_number']);

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Transfer failed', ['error' => $e->getMessage(), 'request' => $request->all()]);
            return back()->with('error', 'Transfer failed. Please try again.')->withInput();
        }
    }

    /**
     * Show transaction receipt
     */
    public function receipt(Transaction $transaction)
    {
        // Check permissions
        $user = Auth::user();
        if ($user->role === 'member' && $transaction->member_id !== $user->id) {
            abort(403, 'Unauthorized access to transaction receipt.');
        }

        $transaction->load(['account', 'member']);
        
        return view('transactions.receipt', compact('transaction'));
    }

    /**
     * Approve pending transaction (Admin/Manager only)
     */
    public function approve(Request $request, Transaction $transaction)
    {
        $this->authorize('approve', $transaction);

        $request->validate([
            'comments' => 'nullable|string|max:500',
        ]);

        try {
            $this->transactionService->approveTransaction($transaction, Auth::user());
            
            // Add custom comments if provided
            if ($request->comments) {
                $metadata = $transaction->metadata ?? [];
                $metadata['approval_comments'] = $request->comments;
                $transaction->update(['metadata' => $metadata]);
            }

            Log::info('Transaction approved', [
                'transaction_id' => $transaction->id,
                'approved_by' => Auth::id(),
                'amount' => $transaction->amount,
                'comments' => $request->comments,
            ]);
            
            return back()->with('success', 'Transaction approved successfully!');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            Log::error('Transaction approval failed', ['error' => $e->getMessage(), 'transaction_id' => $transaction->id]);
            return back()->with('error', 'Failed to approve transaction.');
        }
    }

    /**
     * Reject pending transaction (Admin/Manager only)
     */
    public function reject(Request $request, Transaction $transaction)
    {
        $this->authorize('approve', $transaction);

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $this->transactionService->rejectTransaction($transaction, Auth::user(), $request->reason);
            
            Log::info('Transaction rejected', [
                'transaction_id' => $transaction->id,
                'rejected_by' => Auth::id(),
                'amount' => $transaction->amount,
                'reason' => $request->reason,
            ]);
            
            return back()->with('success', 'Transaction rejected successfully!');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            Log::error('Transaction rejection failed', ['error' => $e->getMessage(), 'transaction_id' => $transaction->id]);
            return back()->with('error', 'Failed to reject transaction.');
        }
    }

    /**
     * Bulk approve multiple transactions (Admin/Manager only)
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'transaction_ids' => 'required|array|min:1',
            'transaction_ids.*' => 'exists:transactions,id',
            'comments' => 'nullable|string|max:500',
        ]);

        $approvedCount = 0;
        $failedCount = 0;
        $errors = [];

        foreach ($request->transaction_ids as $transactionId) {
            try {
                $transaction = Transaction::findOrFail($transactionId);
                $this->authorize('approve', $transaction);
                
                $this->transactionService->approveTransaction($transaction, Auth::user());
                
                // Add bulk comments if provided
                if ($request->comments) {
                    $metadata = $transaction->metadata ?? [];
                    $metadata['approval_comments'] = $request->comments;
                    $metadata['bulk_approved'] = true;
                    $transaction->update(['metadata' => $metadata]);
                }
                
                $approvedCount++;
                
            } catch (\Exception $e) {
                $failedCount++;
                $errors[] = "Transaction #{$transactionId}: " . $e->getMessage();
                Log::error('Bulk approval failed for transaction', [
                    'transaction_id' => $transactionId,
                    'error' => $e->getMessage(),
                    'user_id' => Auth::id()
                ]);
            }
        }

        Log::info('Bulk approval completed', [
            'approved_count' => $approvedCount,
            'failed_count' => $failedCount,
            'user_id' => Auth::id(),
            'comments' => $request->comments,
        ]);

        $message = "{$approvedCount} transactions approved successfully";
        if ($failedCount > 0) {
            $message .= ", {$failedCount} failed";
        }

        if ($failedCount > 0 && count($errors) > 0) {
            return back()->with('warning', $message)->withErrors($errors);
        }

        return back()->with('success', $message);
    }

    /**
     * Bulk reject multiple transactions (Admin/Manager only)
     */
    public function bulkReject(Request $request)
    {
        $request->validate([
            'transaction_ids' => 'required|array|min:1',
            'transaction_ids.*' => 'exists:transactions,id',
            'reason' => 'required|string|max:500',
        ]);

        $rejectedCount = 0;
        $failedCount = 0;
        $errors = [];

        foreach ($request->transaction_ids as $transactionId) {
            try {
                $transaction = Transaction::findOrFail($transactionId);
                $this->authorize('approve', $transaction);
                
                $this->transactionService->rejectTransaction($transaction, Auth::user(), $request->reason);
                $rejectedCount++;
                
            } catch (\Exception $e) {
                $failedCount++;
                $errors[] = "Transaction #{$transactionId}: " . $e->getMessage();
                Log::error('Bulk rejection failed for transaction', [
                    'transaction_id' => $transactionId,
                    'error' => $e->getMessage(),
                    'user_id' => Auth::id()
                ]);
            }
        }

        Log::info('Bulk rejection completed', [
            'rejected_count' => $rejectedCount,
            'failed_count' => $failedCount,
            'user_id' => Auth::id(),
            'reason' => $request->reason,
        ]);

        $message = "{$rejectedCount} transactions rejected successfully";
        if ($failedCount > 0) {
            $message .= ", {$failedCount} failed";
        }

        if ($failedCount > 0 && count($errors) > 0) {
            return back()->with('warning', $message)->withErrors($errors);
        }

        return back()->with('success', $message);
    }

    /**
     * Show detailed transaction view for approval
     */
    public function show(Transaction $transaction)
    {
        $this->authorize('view', $transaction);

        $transaction->load(['account.member', 'member']);
        
        // Get related transactions if it's a transfer
        $relatedTransactions = collect([]);
        if ($transaction->type === Transaction::TYPE_TRANSFER) {
            $transferReference = $transaction->metadata['transfer_reference'] ?? null;
            if ($transferReference) {
                $relatedTransactions = Transaction::where('metadata->transfer_reference', $transferReference)
                    ->where('id', '!=', $transaction->id)
                    ->with(['account.member'])
                    ->get();
            }
        }

        // Get member's recent transaction history
        $memberHistory = Transaction::where('member_id', $transaction->member_id)
            ->where('id', '!=', $transaction->id)
            ->with('account')
            ->latest()
            ->limit(10)
            ->get();

        return view('transactions.show', compact('transaction', 'relatedTransactions', 'memberHistory'));
    }

    /**
     * Get account details via AJAX
     */
    public function getAccountDetails(Account $account)
    {
        $account->load('member');
        
        $dailyWithdrawalTotal = $this->transactionService->getDailyWithdrawalTotal($account);
        $summary = $this->transactionService->getAccountTransactionSummary($account, 7);
        
        return response()->json([
            'account' => $account,
            'daily_withdrawal_total' => $dailyWithdrawalTotal,
            'summary' => $summary,
            'available_for_withdrawal' => max(0, $account->balance - TransactionService::MINIMUM_BALANCE),
            'daily_limit_remaining' => max(0, TransactionService::DAILY_WITHDRAWAL_LIMIT - $dailyWithdrawalTotal),
        ]);
    }

    /**
     * Download transaction receipt as PDF
     */
    public function downloadReceipt(Transaction $transaction)
    {
        // Check permissions
        $user = Auth::user();
        if ($user->role === 'member' && $transaction->member_id !== $user->id) {
            abort(403, 'Unauthorized access to transaction receipt.');
        }

        $transaction->load(['account', 'member']);
        
        // We'll implement PDF generation in the next step
        return view('transactions.receipt-pdf', compact('transaction'));
    }
} 