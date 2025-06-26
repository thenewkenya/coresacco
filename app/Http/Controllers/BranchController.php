<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use App\Models\Account;
use App\Models\Loan;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class BranchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of branches
     */
    public function index(Request $request)
    {
        // Check if user has permission to view branches
        if (!Auth::user()->hasAnyRole(['admin', 'manager'])) {
            abort(403, 'Unauthorized to access branch management.');
        }

        $search = $request->get('search');
        $status = $request->get('status');
        $city = $request->get('city');

        // Build query with eager loading
        $query = Branch::with(['manager', 'staff'])
            ->when($search, function ($q) use ($search) {
                $q->where(function($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('code', 'like', "%{$search}%")
                          ->orWhere('city', 'like', "%{$search}%")
                          ->orWhere('address', 'like', "%{$search}%");
                });
            })
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->when($city, function ($q) use ($city) {
                $q->where('city', $city);
            });

        $branches = $query->latest()->get();

        // Calculate analytics for each branch
        $branchesWithAnalytics = $branches->map(function($branch) {
            return $this->calculateBranchAnalytics($branch);
        });

        // Overall statistics
        $stats = [
            'total_branches' => Branch::count(),
            'active_branches' => Branch::where('status', 'active')->count(),
            'total_staff' => User::whereNotNull('branch_id')->where('role', '!=', 'member')->count(),
            'total_members' => User::where('role', 'member')->count(),
            'top_performer' => $this->getTopPerformingBranch(),
        ];

        // Get filter options
        $cities = Branch::select('city')->distinct()->pluck('city');
        $availableManagers = User::where('role', 'manager')
            ->whereDoesntHave('managedBranch')
            ->get();

        return view('branches.index', compact(
            'branchesWithAnalytics', 
            'stats', 
            'cities', 
            'availableManagers',
            'search',
            'status',
            'city'
        ));
    }

    /**
     * Show the form for creating a new branch
     */
    public function create()
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Only administrators can create branches.');
        }

        $availableManagers = User::where('role', 'manager')
            ->whereDoesntHave('managedBranch')
            ->get();

        return view('branches.create', compact('availableManagers'));
    }

    /**
     * Store a newly created branch
     */
    public function store(Request $request)
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Only administrators can create branches.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:branches,name',
            'code' => 'required|string|max:10|unique:branches,code',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:branches,email',
            'manager_id' => 'nullable|exists:users,id',
            'opening_date' => 'required|date',
            'working_hours' => 'required|array',
            'working_hours.monday' => 'required|array',
            'working_hours.monday.open' => 'required|string',
            'working_hours.monday.close' => 'required|string',
            'coordinates' => 'nullable|array',
            'coordinates.latitude' => 'nullable|numeric|between:-90,90',
            'coordinates.longitude' => 'nullable|numeric|between:-180,180',
        ]);

        // Ensure manager is not already assigned to another branch
        if ($validated['manager_id']) {
            $existingBranch = Branch::where('manager_id', $validated['manager_id'])->first();
            if ($existingBranch) {
                return back()->withErrors(['manager_id' => 'This manager is already assigned to another branch.']);
            }
        }

        $branch = Branch::create($validated);

        // Generate branch code if not provided
        if (!$branch->code) {
            $branch->update(['code' => $branch->generateBranchCode()]);
        }

        return redirect()->route('branches.show', $branch)
            ->with('success', 'Branch created successfully.');
    }

    /**
     * Display the specified branch
     */
    public function show(Branch $branch)
    {
        if (!Auth::user()->hasAnyRole(['admin', 'manager']) && Auth::user()->branch_id !== $branch->id) {
            abort(403, 'Unauthorized to view this branch.');
        }

        $branch->load(['manager', 'staff', 'members']);
        
        // Get branch analytics
        $analytics = $this->calculateDetailedBranchAnalytics($branch);
        
        // Recent activities
        $recentTransactions = $this->getRecentBranchTransactions($branch, 10);
        $recentMembers = User::where('branch_id', $branch->id)
            ->where('role', 'member')
            ->latest()
            ->limit(5)
            ->get();

        return view('branches.show', compact('branch', 'analytics', 'recentTransactions', 'recentMembers'));
    }

    /**
     * Show the form for editing the specified branch
     */
    public function edit(Branch $branch)
    {
        if (!Auth::user()->hasRole('admin') && Auth::user()->branch_id !== $branch->id) {
            abort(403, 'Unauthorized to edit this branch.');
        }

        $availableManagers = User::where('role', 'manager')
            ->where(function($query) use ($branch) {
                $query->whereDoesntHave('managedBranch')
                      ->orWhere('id', $branch->manager_id);
            })
            ->get();

        return view('branches.edit', compact('branch', 'availableManagers'));
    }

    /**
     * Update the specified branch
     */
    public function update(Request $request, Branch $branch)
    {
        if (!Auth::user()->hasRole('admin') && Auth::user()->branch_id !== $branch->id) {
            abort(403, 'Unauthorized to edit this branch.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('branches')->ignore($branch->id)],
            'code' => ['required', 'string', 'max:10', Rule::unique('branches')->ignore($branch->id)],
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => ['required', 'email', Rule::unique('branches')->ignore($branch->id)],
            'manager_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,inactive,under_maintenance',
            'opening_date' => 'required|date',
            'working_hours' => 'required|array',
            'coordinates' => 'nullable|array',
            'coordinates.latitude' => 'nullable|numeric|between:-90,90',
            'coordinates.longitude' => 'nullable|numeric|between:-180,180',
        ]);

        // Ensure manager is not already assigned to another branch
        if ($validated['manager_id'] && $validated['manager_id'] !== $branch->manager_id) {
            $existingBranch = Branch::where('manager_id', $validated['manager_id'])->first();
            if ($existingBranch) {
                return back()->withErrors(['manager_id' => 'This manager is already assigned to another branch.']);
            }
        }

        $branch->update($validated);

        return redirect()->route('branches.show', $branch)
            ->with('success', 'Branch updated successfully.');
    }

    /**
     * Remove the specified branch (soft delete)
     */
    public function destroy(Branch $branch)
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Only administrators can delete branches.');
        }

        // Check if branch has active members or staff
        $memberCount = User::where('branch_id', $branch->id)->where('role', 'member')->count();
        $staffCount = User::where('branch_id', $branch->id)->where('role', '!=', 'member')->count();

        if ($memberCount > 0 || $staffCount > 0) {
            return back()->withErrors(['delete' => 'Cannot delete branch with active members or staff. Please transfer them first.']);
        }

        $branch->delete();

        return redirect()->route('branches.index')
            ->with('success', 'Branch deleted successfully.');
    }

    /**
     * Branch staff management
     */
    public function staff(Branch $branch)
    {
        if (!Auth::user()->hasAnyRole(['admin', 'manager']) && Auth::user()->branch_id !== $branch->id) {
            abort(403, 'Unauthorized to view branch staff.');
        }

        $staff = User::where('branch_id', $branch->id)
            ->where('role', '!=', 'member')
            ->with('role')
            ->get();

        $availableStaff = User::whereNull('branch_id')
            ->where('role', '!=', 'member')
            ->get();

        return view('branches.staff', compact('branch', 'staff', 'availableStaff'));
    }

    /**
     * Assign staff to branch
     */
    public function assignStaff(Request $request, Branch $branch)
    {
        if (!Auth::user()->hasAnyRole(['admin', 'manager'])) {
            abort(403, 'Unauthorized to assign staff.');
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($validated['user_id']);
        
        if ($user->branch_id) {
            return back()->withErrors(['user_id' => 'This staff member is already assigned to a branch.']);
        }

        $user->update(['branch_id' => $branch->id]);

        return back()->with('success', 'Staff member assigned successfully.');
    }

    /**
     * Remove staff from branch
     */
    public function removeStaff(Request $request, Branch $branch)
    {
        if (!Auth::user()->hasAnyRole(['admin', 'manager'])) {
            abort(403, 'Unauthorized to remove staff.');
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($validated['user_id']);
        
        if ($user->branch_id !== $branch->id) {
            return back()->withErrors(['user_id' => 'This staff member is not assigned to this branch.']);
        }

        $user->update(['branch_id' => null]);

        return back()->with('success', 'Staff member removed successfully.');
    }

    /**
     * Branch performance analytics
     */
    public function analytics(Branch $branch, Request $request)
    {
        if (!Auth::user()->hasAnyRole(['admin', 'manager']) && Auth::user()->branch_id !== $branch->id) {
            abort(403, 'Unauthorized to view branch analytics.');
        }

        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $analytics = $this->calculatePeriodAnalytics($branch, $startDate, $endDate);

        return view('branches.analytics', compact('branch', 'analytics', 'startDate', 'endDate'));
    }

    // Private helper methods

    private function calculateBranchAnalytics(Branch $branch)
    {
        $memberIds = User::where('branch_id', $branch->id)->where('role', 'member')->pluck('id');
        
        $data = [
            'branch' => $branch,
            'total_members' => $memberIds->count(),
            'total_staff' => $branch->staff->count(),
            'total_deposits' => Account::whereIn('member_id', $memberIds)->sum('balance'),
            'active_loans' => Loan::whereIn('member_id', $memberIds)->whereIn('status', ['active', 'disbursed'])->count(),
            'this_month_transactions' => Transaction::whereHas('account', function($q) use ($memberIds) {
                $q->whereIn('member_id', $memberIds);
            })->whereMonth('created_at', now()->month)->count(),
        ];

        // Calculate performance score
        $data['performance_score'] = $this->calculatePerformanceScore($data);
        
        return $data;
    }

    private function calculateDetailedBranchAnalytics(Branch $branch)
    {
        $memberIds = User::where('branch_id', $branch->id)->where('role', 'member')->pluck('id');
        
        return [
            'members' => [
                'total' => $memberIds->count(),
                'new_this_month' => User::where('branch_id', $branch->id)
                    ->where('role', 'member')
                    ->whereMonth('created_at', now()->month)
                    ->count(),
                'active' => User::where('branch_id', $branch->id)
                    ->where('role', 'member')
                    ->where('membership_status', 'active')
                    ->count(),
            ],
            'accounts' => [
                'total' => Account::whereIn('member_id', $memberIds)->count(),
                'total_balance' => Account::whereIn('member_id', $memberIds)->sum('balance'),
                'average_balance' => Account::whereIn('member_id', $memberIds)->avg('balance'),
            ],
            'loans' => [
                'total_portfolio' => Loan::whereIn('member_id', $memberIds)->sum('amount'),
                'active_loans' => Loan::whereIn('member_id', $memberIds)->whereIn('status', ['active', 'disbursed'])->count(),
                'overdue_loans' => Loan::whereIn('member_id', $memberIds)
                    ->where('status', 'active')
                    ->where('due_date', '<', now())
                    ->count(),
                'completed_loans' => Loan::whereIn('member_id', $memberIds)->where('status', 'completed')->count(),
            ],
            'transactions' => [
                'total_this_month' => Transaction::whereHas('account', function($q) use ($memberIds) {
                    $q->whereIn('member_id', $memberIds);
                })->whereMonth('created_at', now()->month)->count(),
                'total_amount_this_month' => Transaction::whereHas('account', function($q) use ($memberIds) {
                    $q->whereIn('member_id', $memberIds);
                })->whereMonth('created_at', now()->month)->where('status', 'completed')->sum('amount'),
            ],
        ];
    }

    private function calculatePeriodAnalytics(Branch $branch, $startDate, $endDate)
    {
        $memberIds = User::where('branch_id', $branch->id)->where('role', 'member')->pluck('id');
        
        $transactions = Transaction::whereHas('account', function($q) use ($memberIds) {
            $q->whereIn('member_id', $memberIds);
        })->whereBetween('created_at', [$startDate, $endDate])->get();

        return [
            'period_transactions' => $transactions->count(),
            'period_amount' => $transactions->where('status', 'completed')->sum('amount'),
            'deposits' => $transactions->where('type', 'deposit')->sum('amount'),
            'withdrawals' => $transactions->where('type', 'withdrawal')->sum('amount'),
            'loan_repayments' => $transactions->where('type', 'loan_repayment')->sum('amount'),
            'new_members' => User::where('branch_id', $branch->id)
                ->where('role', 'member')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'new_loans' => Loan::whereIn('member_id', $memberIds)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
        ];
    }

    private function calculatePerformanceScore($data)
    {
        $score = 0;
        
        // Member count (25%)
        if ($data['total_members'] > 500) $score += 25;
        elseif ($data['total_members'] > 200) $score += 20;
        elseif ($data['total_members'] > 100) $score += 15;
        elseif ($data['total_members'] > 50) $score += 10;
        else $score += 5;
        
        // Deposits (30%)
        if ($data['total_deposits'] > 10000000) $score += 30;
        elseif ($data['total_deposits'] > 5000000) $score += 25;
        elseif ($data['total_deposits'] > 1000000) $score += 20;
        elseif ($data['total_deposits'] > 500000) $score += 15;
        else $score += 10;
        
        // Activity (25%)
        if ($data['this_month_transactions'] > 500) $score += 25;
        elseif ($data['this_month_transactions'] > 200) $score += 20;
        elseif ($data['this_month_transactions'] > 100) $score += 15;
        elseif ($data['this_month_transactions'] > 50) $score += 10;
        else $score += 5;
        
        // Loan portfolio (20%)
        if ($data['active_loans'] > 100) $score += 20;
        elseif ($data['active_loans'] > 50) $score += 16;
        elseif ($data['active_loans'] > 25) $score += 12;
        elseif ($data['active_loans'] > 10) $score += 8;
        else $score += 4;
        
        return min(100, $score);
    }

    private function getTopPerformingBranch()
    {
        $branches = Branch::with(['manager'])->get();
        $topBranch = null;
        $topScore = 0;

        foreach ($branches as $branch) {
            $analytics = $this->calculateBranchAnalytics($branch);
            if ($analytics['performance_score'] > $topScore) {
                $topScore = $analytics['performance_score'];
                $topBranch = $branch;
            }
        }

        return $topBranch;
    }

    private function getRecentBranchTransactions(Branch $branch, $limit = 10)
    {
        $memberIds = User::where('branch_id', $branch->id)->where('role', 'member')->pluck('id');
        
        return Transaction::whereHas('account', function($q) use ($memberIds) {
            $q->whereIn('member_id', $memberIds);
        })->with(['account.member'])
        ->latest()
        ->limit($limit)
        ->get();
    }
} 