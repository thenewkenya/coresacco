<?php

namespace App\Traits;

use App\Services\MemberSearchService;
use App\Services\AccountLookupService;
use App\Services\DashboardStatsService;

trait OptimizedMemberSearch
{
    /**
     * Search members using the optimized service
     *
     * @param string $search
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function searchMembers(string $search, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        $memberSearchService = app(MemberSearchService::class);
        return $memberSearchService->searchMembersForTransactions($search, $limit);
    }

    /**
     * Search members with filters using the optimized service
     *
     * @param array $filters
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function searchMembersWithFilters(array $filters, int $limit = 15): \Illuminate\Database\Eloquent\Collection
    {
        $memberSearchService = app(MemberSearchService::class);
        return $memberSearchService->searchMembersWithFilters($filters, $limit);
    }

    /**
     * Get member by ID using the optimized service
     *
     * @param int $memberId
     * @return \App\Models\User|null
     */
    protected function getMemberById(int $memberId): ?\App\Models\User
    {
        $memberSearchService = app(MemberSearchService::class);
        return $memberSearchService->getMemberById($memberId);
    }

    /**
     * Get member accounts using the optimized service
     *
     * @param int $memberId
     * @param string $status
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getMemberAccounts(int $memberId, string $status = 'active'): \Illuminate\Database\Eloquent\Collection
    {
        $accountLookupService = app(AccountLookupService::class);
        return $accountLookupService->getMemberAccounts($memberId, $status);
    }

    /**
     * Get accounts with minimum balance
     *
     * @param int $memberId
     * @param float $minBalance
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getMemberAccountsWithBalance(int $memberId, float $minBalance = 1000): \Illuminate\Database\Eloquent\Collection
    {
        $accountLookupService = app(AccountLookupService::class);
        return $accountLookupService->getMemberAccountsWithBalance($memberId, $minBalance);
    }

    /**
     * Get account details using the optimized service
     *
     * @param int $accountId
     * @return \App\Models\Account|null
     */
    protected function getAccountDetails(int $accountId): ?\App\Models\Account
    {
        $accountLookupService = app(AccountLookupService::class);
        return $accountLookupService->getAccountDetails($accountId);
    }

    /**
     * Get member statistics using the optimized service
     *
     * @return array
     */
    protected function getMemberStatistics(): array
    {
        $memberSearchService = app(MemberSearchService::class);
        return $memberSearchService->getMemberStatistics();
    }

    /**
     * Get dashboard statistics using the optimized service
     *
     * @return array
     */
    protected function getDashboardStats(): array
    {
        $dashboardStatsService = app(DashboardStatsService::class);
        return $dashboardStatsService->getDashboardStats();
    }

    /**
     * Get member dashboard statistics
     *
     * @param int $memberId
     * @return array
     */
    protected function getMemberDashboardStats(int $memberId): array
    {
        $dashboardStatsService = app(DashboardStatsService::class);
        return $dashboardStatsService->getMemberDashboardStats($memberId);
    }

    /**
     * Clear member-related cache when members are updated
     *
     * @param int|null $memberId
     * @return void
     */
    protected function clearMemberCache(?int $memberId = null): void
    {
        $memberSearchService = app(MemberSearchService::class);
        $memberSearchService->clearMemberCache($memberId);

        if ($memberId) {
            $accountLookupService = app(AccountLookupService::class);
            $accountLookupService->clearAccountCache(null, $memberId);

            $dashboardStatsService = app(DashboardStatsService::class);
            $dashboardStatsService->clearMemberDashboardCache($memberId);
        }
    }
} 