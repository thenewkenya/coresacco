<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Collection;

class MemberSearchService
{
    /**
     * Cache duration for search results (5 minutes)
     */
    private const CACHE_DURATION = 300;

    /**
     * Search members using Scout with caching
     *
     * @param string $search
     * @param int $limit
     * @param array $columns
     * @return Collection
     */
    public function searchMembers(string $search, int $limit = 10, array $columns = ['*']): Collection
    {
        if (strlen($search) < 2) {
            return collect();
        }

        $cacheKey = "member_search:" . md5(strtolower($search) . $limit . implode('', $columns));

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($search, $limit, $columns) {
            return User::search($search)
                ->where('role', 'member')
                ->take($limit)
                ->get($columns);
        });
    }

    /**
     * Search members with filters using Scout
     *
     * @param array $filters
     * @param int $limit
     * @return Collection
     */
    public function searchMembersWithFilters(array $filters, int $limit = 15): Collection
    {
        $cacheKey = "member_filtered_search:" . md5(serialize($filters) . $limit);

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($filters, $limit) {
            $query = User::search($filters['search'] ?? '');

            // Apply status filter
            if (isset($filters['status']) && !empty($filters['status'])) {
                $query->where('membership_status', $filters['status']);
            }

            // Apply branch filter
            if (isset($filters['branch']) && !empty($filters['branch'])) {
                $query->where('branch_id', $filters['branch']);
            }

            return $query->where('role', 'member')
                ->take($limit)
                ->get();
        });
    }

    /**
     * Get member by ID with caching
     *
     * @param int $memberId
     * @return User|null
     */
    public function getMemberById(int $memberId): ?User
    {
        $cacheKey = "member_profile:{$memberId}";

        return Cache::remember($cacheKey, self::CACHE_DURATION * 4, function () use ($memberId) {
            return User::with(['branch', 'accounts', 'loans'])->find($memberId);
        });
    }

    /**
     * Get member statistics with caching
     *
     * @return array
     */
    public function getMemberStatistics(): array
    {
        $cacheKey = "member_statistics";

        return Cache::remember($cacheKey, self::CACHE_DURATION * 2, function () {
            return [
                'total_members' => User::where('role', 'member')->count(),
                'active_members' => User::where('role', 'member')->where('membership_status', 'active')->count(),
                'inactive_members' => User::where('role', 'member')->where('membership_status', 'inactive')->count(),
                'suspended_members' => User::where('role', 'member')->where('membership_status', 'suspended')->count(),
                'new_this_month' => User::where('role', 'member')->whereMonth('created_at', now()->month)->count(),
                'new_this_week' => User::where('role', 'member')->whereDate('created_at', '>=', now()->startOfWeek())->count(),
            ];
        });
    }

    /**
     * Clear member-related cache
     *
     * @param int|null $memberId
     * @return void
     */
    public function clearMemberCache(?int $memberId = null): void
    {
        if ($memberId) {
            Cache::forget("member_profile:{$memberId}");
        }

        // Clear search caches (use tags when available)
        Cache::flush(); // For now, flush all cache (consider using tags in production)
    }

    /**
     * Get members for account lookup (optimized for transaction forms)
     *
     * @param string $search
     * @param int $limit
     * @return Collection
     */
    public function searchMembersForTransactions(string $search, int $limit = 10): Collection
    {
        if (strlen($search) < 2) {
            return collect();
        }

        $cacheKey = "member_transaction_search:" . md5(strtolower($search) . $limit);

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($search, $limit) {
            return User::search($search)
                ->where('role', 'member')
                ->where('membership_status', 'active')
                ->take($limit)
                ->get(['id', 'name', 'email', 'member_number']);
        });
    }
} 