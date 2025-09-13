<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TransactionController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        
        $query = Transaction::with(['account', 'member'])
            ->when($user->hasRole('member'), function ($query) use ($user) {
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

        return Inertia::render('Transactions/Index', [
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
        if ($user->hasRole('member') && $transaction->member_id !== $user->id) {
            abort(403, 'Unauthorized access to transaction details.');
        }

        $transaction->load(['account', 'member']);

        return Inertia::render('Transactions/Show', [
            'transaction' => $transaction,
        ]);
    }

    public function receipt(Transaction $transaction): Response
    {
        $user = auth()->user();
        
        // Check permissions
        if ($user->hasRole('member') && $transaction->member_id !== $user->id) {
            abort(403, 'Unauthorized access to transaction details.');
        }

        // Only allow receipts for completed transactions
        if ($transaction->status !== Transaction::STATUS_COMPLETED) {
            abort(404, 'Receipt not available. Transaction must be completed to generate a receipt.');
        }

        $transaction->load(['account', 'member']);

        return Inertia::render('Transactions/Receipt', [
            'transaction' => $transaction,
        ]);
    }
}