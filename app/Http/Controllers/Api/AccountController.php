<?php

namespace App\Http\Controllers\Api;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\AccountRequest;
use App\Http\Requests\TransactionRequest;
use Illuminate\Support\Facades\DB;

class AccountController extends ApiController
{
    public function index(): JsonResponse
    {
        $accounts = Account::with(['member'])->paginate(10);
        return $this->successResponse($accounts);
    }

    public function store(AccountRequest $request): JsonResponse
    {
        try {
            $account = Account::create([
                'member_id' => $request->member_id,
                'account_number' => $request->account_type[0] . 'A' . str_pad(Account::count() + 1, 8, '0', STR_PAD_LEFT),
                'account_type' => $request->account_type,
                'status' => 'active',
            ]);

            return $this->createdResponse($account, 'Account created successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create account: ' . $e->getMessage());
        }
    }

    public function show(Account $account): JsonResponse
    {
        $account->load(['member', 'transactions' => function ($query) {
            $query->latest()->take(10);
        }]);
        return $this->successResponse($account);
    }

    public function update(AccountRequest $request, Account $account): JsonResponse
    {
        try {
            $account->update($request->validated());
            return $this->successResponse($account, 'Account updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update account: ' . $e->getMessage());
        }
    }

    public function deposit(TransactionRequest $request, Account $account): JsonResponse
    {
        try {
            DB::beginTransaction();

            $transaction = $account->transactions()->create([
                'member_id' => $account->member_id,
                'type' => Transaction::TYPE_DEPOSIT,
                'amount' => $request->amount,
                'description' => $request->description,
                'reference_number' => 'DEP' . time() . rand(1000, 9999),
                'balance_before' => $account->balance,
                'balance_after' => $account->balance + $request->amount,
                'status' => 'completed',
            ]);

            $account->deposit($request->amount);

            DB::commit();
            return $this->successResponse($transaction, 'Deposit successful');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to process deposit: ' . $e->getMessage());
        }
    }

    public function withdraw(TransactionRequest $request, Account $account): JsonResponse
    {
        try {
            if ($account->balance < $request->amount) {
                return $this->errorResponse('Insufficient funds');
            }

            DB::beginTransaction();

            $transaction = $account->transactions()->create([
                'member_id' => $account->member_id,
                'type' => Transaction::TYPE_WITHDRAWAL,
                'amount' => $request->amount,
                'description' => $request->description,
                'reference_number' => 'WTH' . time() . rand(1000, 9999),
                'balance_before' => $account->balance,
                'balance_after' => $account->balance - $request->amount,
                'status' => 'completed',
            ]);

            $account->withdraw($request->amount);

            DB::commit();
            return $this->successResponse($transaction, 'Withdrawal successful');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to process withdrawal: ' . $e->getMessage());
        }
    }

    public function statement(Account $account): JsonResponse
    {
        $transactions = $account->transactions()
            ->with(['member'])
            ->latest()
            ->paginate(20);
        
        return $this->successResponse([
            'account' => $account,
            'transactions' => $transactions,
        ]);
    }
} 