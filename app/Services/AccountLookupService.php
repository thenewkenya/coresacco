<?php

namespace App\Services;

use App\Models\Account;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Collection;

class AccountLookupService
{
    /**
     * Cache duration for account data (10 minutes)
     */
    private const CACHE_DURATION = 600;

    /**
     * Cache duration for account balances (2 minutes for faster updates)
     */
    private const BALANCE_CACHE_DURATION = 120;

    /**
     * Get account details with caching
     *
     * @param int $accountId
     * @return Account|null
     */
    public function getAccountDetails(int $accountId): ?Account
    {
        $cacheKey = "account_details:{$accountId}";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($accountId) {
            return Account::with(['accountType', 'member'])->find($accountId);
        });
    }

    /**
     * Get account balance with caching
     *
     * @param int $accountId
     * @return float
     */
    public function getAccountBalance(int $accountId): float
    {
        $cacheKey = "account_balance:{$accountId}";

        return Cache::remember($cacheKey, self::BALANCE_CACHE_DURATION, function () use ($accountId) {
            $account = Account::find($accountId);
            return $account ? $account->balance : 0.0;
        });
    }

    /**
     * Get all accounts for a member with caching
     *
     * @param int $memberId
     * @param string $status
     * @return Collection
     */
    public function getMemberAccounts(int $memberId, string $status = 'active'): Collection
    {
        $cacheKey = "member_accounts:{$memberId}:{$status}";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($memberId, $status) {
            return Account::with('accountType')
                ->where('member_id', $memberId)
                ->where('status', $status)
                ->get();
        });
    }

    /**
     * Get accounts with minimum balance for withdrawals
     *
     * @param int $memberId
     * @param float $minBalance
     * @return Collection
     */
    public function getMemberAccountsWithBalance(int $memberId, float $minBalance = 1000): Collection
    {
        $cacheKey = "member_accounts_with_balance:{$memberId}:{$minBalance}";

        return Cache::remember($cacheKey, self::BALANCE_CACHE_DURATION, function () use ($memberId, $minBalance) {
            return Account::with('accountType')
                ->where('member_id', $memberId)
                ->where('status', 'active')
                ->where('balance', '>=', $minBalance)
                ->get();
        });
    }

    /**
     * Get account summary for dashboard
     *
     * @param int $memberId
     * @return array
     */
    public function getAccountSummary(int $memberId): array
    {
        $cacheKey = "account_summary:{$memberId}";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($memberId) {
            $accounts = Account::where('member_id', $memberId)->get();

            return [
                'total_accounts' => $accounts->count(),
                'active_accounts' => $accounts->where('status', 'active')->count(),
                'total_balance' => $accounts->sum('balance'),
                'savings_balance' => $accounts->where('account_type.type', 'savings')->sum('balance'),
                'checking_balance' => $accounts->where('account_type.type', 'checking')->sum('balance'),
            ];
        });
    }

    /**
     * Search accounts by account number or member details
     *
     * @param string $search
     * @param int $limit
     * @return Collection
     */
    public function searchAccounts(string $search, int $limit = 10): Collection
    {
        if (strlen($search) < 2) {
            return collect();
        }

        $cacheKey = "account_search:" . md5(strtolower($search) . $limit);

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($search, $limit) {
            return Account::with(['member', 'accountType'])
                ->where('account_number', 'like', '%' . $search . '%')
                ->orWhereHas('member', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                          ->orWhere('member_number', 'like', '%' . $search . '%');
                })
                ->where('status', 'active')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Clear account cache for a specific account or member
     *
     * @param int|null $accountId
     * @param int|null $memberId
     * @return void
     */
    public function clearAccountCache(?int $accountId = null, ?int $memberId = null): void
    {
        if ($accountId) {
            Cache::forget("account_details:{$accountId}");
            Cache::forget("account_balance:{$accountId}");
        }

        if ($memberId) {
            Cache::forget("member_accounts:{$memberId}:active");
            Cache::forget("member_accounts:{$memberId}:inactive");
            Cache::forget("account_summary:{$memberId}");
            
            // Clear balance cache for all member accounts
            $accounts = Account::where('member_id', $memberId)->pluck('id');
            foreach ($accounts as $id) {
                Cache::forget("account_balance:{$id}");
                Cache::forget("member_accounts_with_balance:{$memberId}:1000");
            }
        }
    }

    /**
     * Update account balance and clear relevant cache
     *
     * @param int $accountId
     * @param float $newBalance
     * @return bool
     */
    public function updateAccountBalance(int $accountId, float $newBalance): bool
    {
        $account = Account::find($accountId);
        
        if (!$account) {
            return false;
        }

        $account->update(['balance' => $newBalance]);
        
        // Clear related cache
        $this->clearAccountCache($accountId, $account->member_id);
        
        return true;
    }
} 