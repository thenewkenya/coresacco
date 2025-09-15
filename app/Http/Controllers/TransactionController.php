<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Account;
use App\Models\User;
use App\Services\MobileMoneyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class TransactionController extends Controller
{
    public function index(Request $request): Response
    {
        $user = Auth::user();
        
        $query = Transaction::with(['account', 'member'])
            ->when($user->role === 'member', function ($query) use ($user) {
                return $query->where('member_id', $user->id);
            });

        // Apply filters
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('reference_number', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(15);

        return Inertia::render('transactions/index', [
            'transactions' => $transactions,
            'filters' => $request->only(['search', 'status', 'type']),
            'statusOptions' => [
                'pending' => 'Pending',
                'completed' => 'Completed',
                'failed' => 'Failed',
                'cancelled' => 'Cancelled',
            ],
            'typeOptions' => [
                'deposit' => 'Deposits',
                'withdrawal' => 'Withdrawals',
                'transfer' => 'Transfers',
            ],
        ]);
    }

    public function show(Transaction $transaction): Response
    {
        $user = auth()->user();
        
        // Check permissions
        if ($user->role === 'member' && $transaction->member_id !== $user->id) {
            abort(403, 'Unauthorized access to transaction details.');
        }

        $transaction->load(['account', 'member']);

        return Inertia::render('transactions/show', [
            'transaction' => $transaction,
        ]);
    }

    public function receipt(Transaction $transaction): Response
    {
        $user = auth()->user();
        
        // Check permissions
        if ($user->role === 'member' && $transaction->member_id !== $user->id) {
            abort(403, 'Unauthorized access to transaction details.');
        }

        // Only allow receipts for completed transactions
        if ($transaction->status !== Transaction::STATUS_COMPLETED) {
            abort(404, 'Receipt not available. Transaction must be completed to generate a receipt.');
        }

        $transaction->load(['account', 'member']);

        return Inertia::render('transactions/receipt', [
            'transaction' => $transaction,
        ]);
    }

    public function create(): Response
    {
        $user = Auth::user();
        
        if ($user->role === 'member') {
            // Members can only create transactions for their own accounts
            $accounts = $user->accounts()->where('status', 'active')->get();
        } else {
            // Staff can create transactions for any active account
            $accounts = Account::where('status', 'active')->with('member')->get();
        }

        $transactionTypes = [
            'deposit' => 'Deposit',
            'withdrawal' => 'Withdrawal',
            'transfer' => 'Transfer',
            'fee' => 'Fee',
            'interest' => 'Interest',
        ];

        $paymentMethods = [
            'cash' => 'Cash',
            'mpesa' => 'M-Pesa',
            'bank_transfer' => 'Bank Transfer',
        ];

        return Inertia::render('transactions/create', [
            'accounts' => $accounts,
            'transactionTypes' => $transactionTypes,
            'paymentMethods' => $paymentMethods,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'type' => 'required|in:deposit,withdrawal,transfer,fee,interest',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'reference_number' => 'nullable|string|max:50',
            'payment_method' => 'nullable|in:cash,mpesa,bank_transfer',
            'phone_number' => 'nullable|string|min:10|max:15',
        ]);

        $user = Auth::user();
        $account = Account::findOrFail($request->account_id);

        // Check permissions
        if ($user->role === 'member' && $account->member_id !== $user->id) {
            abort(403, 'You can only create transactions for your own accounts.');
        }

        try {
            DB::beginTransaction();

            // Generate reference number if not provided
            $referenceNumber = $request->reference_number ?: $this->generateReferenceNumber();

            // Get current balance
            $balanceBefore = $account->balance;

            // Calculate new balance
            $amount = $request->amount;
            $balanceAfter = $this->calculateNewBalance($balanceBefore, $amount, $request->type);

            // Check if withdrawal is valid
            if ($request->type === 'withdrawal' && $balanceAfter < 0) {
                throw new \Exception('Insufficient funds for withdrawal.');
            }

            // Handle M-Pesa payments for deposits
            if ($request->type === 'deposit' && $request->payment_method === 'mpesa') {
                $mobileMoneyService = app(MobileMoneyService::class);
                
                try {
                    $mobileMoneyResult = $mobileMoneyService->initiateMpesaPayment($account, $amount, $request->phone_number);
                    
                    if (!$mobileMoneyResult['success']) {
                        throw new \Exception($mobileMoneyResult['message'] ?? 'Mobile money payment failed');
                    }
                    
                    // Mobile money service creates the transaction, so redirect to it
                    $transaction = Transaction::find($mobileMoneyResult['transaction_id']);
                    
                    DB::commit();
                    
                    return redirect()->route('transactions.show', $transaction)
                                   ->with('success', 'M-Pesa payment initiated! Please complete the payment on your phone.');
                    
                } catch (\Exception $e) {
                    throw new \Exception('M-Pesa payment failed: ' . $e->getMessage());
                }
            }

            // Create transaction for non-mobile money payments
            $transaction = Transaction::create([
                'account_id' => $account->id,
                'member_id' => $account->member_id,
                'type' => $request->type,
                'amount' => $amount,
                'description' => $request->description,
                'reference_number' => $referenceNumber,
                'status' => Transaction::STATUS_COMPLETED,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'payment_method' => $request->payment_method,
                'phone_number' => $request->phone_number,
            ]);

            // Update account balance
            $account->update(['balance' => $balanceAfter]);

            DB::commit();

            return redirect()->route('transactions.show', $transaction)
                           ->with('success', 'Transaction created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create transaction: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    public function approve(Request $request, Transaction $transaction)
    {
        $this->authorize('approve', $transaction);

        if ($transaction->status !== Transaction::STATUS_PENDING) {
            return back()->withErrors(['error' => 'Only pending transactions can be approved.']);
        }

        // Prevent approval of M-Pesa transactions
        if ($transaction->payment_method === 'mpesa') {
            return back()->withErrors(['error' => 'M-Pesa transactions are automatically confirmed via webhooks.']);
        }

        try {
            DB::beginTransaction();

            // Update transaction status
            $transaction->update(['status' => Transaction::STATUS_COMPLETED]);

            // Update account balance if not already done
            if ($transaction->balance_after !== $transaction->account->balance) {
                $transaction->account->update(['balance' => $transaction->balance_after]);
            }

            DB::commit();

            return back()->with('success', 'Transaction approved successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to approve transaction: ' . $e->getMessage()]);
        }
    }

    public function reject(Request $request, Transaction $transaction)
    {
        $this->authorize('reject', $transaction);

        if ($transaction->status !== Transaction::STATUS_PENDING) {
            return back()->withErrors(['error' => 'Only pending transactions can be rejected.']);
        }

        // Prevent rejection of M-Pesa transactions
        if ($transaction->payment_method === 'mpesa') {
            return back()->withErrors(['error' => 'M-Pesa transactions are automatically confirmed via webhooks.']);
        }

        $transaction->update(['status' => Transaction::STATUS_FAILED]);

        return back()->with('success', 'Transaction rejected successfully!');
    }

    /**
     * Get transaction status for real-time updates
     */
    public function getStatus(Transaction $transaction)
    {
        $this->authorize('view', $transaction);

        // For M-Pesa transactions, also query the status from M-Pesa API
        if ($transaction->payment_method === 'mpesa' && $transaction->status === Transaction::STATUS_PENDING) {
            $mobileMoneyService = app(MobileMoneyService::class);
            $result = $mobileMoneyService->queryMpesaStatus($transaction);
            
            // Reload the transaction to get updated status
            $transaction->refresh();
            
            // Log the result for debugging
            \Log::info('M-Pesa status query result', [
                'transaction_id' => $transaction->id,
                'result' => $result,
                'new_status' => $transaction->status
            ]);
        }

        return response()->json([
            'id' => $transaction->id,
            'status' => $transaction->status,
            'payment_method' => $transaction->payment_method,
            'amount' => $transaction->amount,
            'description' => $transaction->description,
            'balance_after' => $transaction->balance_after,
            'updated_at' => $transaction->updated_at,
        ]);
    }

    private function generateReferenceNumber(): string
    {
        do {
            $referenceNumber = 'TXN' . date('Ymd') . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
            $exists = Transaction::where('reference_number', $referenceNumber)->exists();
        } while ($exists);

        return $referenceNumber;
    }

    private function calculateNewBalance(float $currentBalance, float $amount, string $type): float
    {
        switch ($type) {
            case 'deposit':
            case 'interest':
                return $currentBalance + $amount;
            case 'withdrawal':
            case 'fee':
                return $currentBalance - $amount;
            case 'transfer':
                // For transfers, we'll handle this in a separate method
                return $currentBalance - $amount;
            default:
                return $currentBalance;
        }
    }
}