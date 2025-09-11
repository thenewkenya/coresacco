<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\AccountRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->hasRole('member')) {
            // Members see only their accounts
            $accounts = $user->accounts()->with('member')->latest()->get();
            return view('accounts.my', compact('accounts'));
        } else {
            // Staff see all accounts with search and filters
            $query = Account::with('member');
            
            // Store search parameters for view
            $search = request('search');
            $accountType = request('account_type');
            $status = request('status');
            
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('account_number', 'LIKE', "%{$search}%")
                      ->orWhereHas('member', function($memberQuery) use ($search) {
                          $memberQuery->where('name', 'LIKE', "%{$search}%")
                                     ->orWhere('email', 'LIKE', "%{$search}%");
                      });
                });
            }
            
            if ($accountType) {
                $query->where('account_type', $accountType);
            }
            
            if ($status) {
                $query->where('status', $status);
            }
            
            $accounts = $query->latest()->paginate(15);
            
            // Calculate statistics
            $totalAccounts = Account::count();
            $totalBalance = Account::sum('balance');
            $activeAccounts = Account::where('status', 'active')->count();
            $thisMonthAccounts = Account::whereMonth('created_at', now()->month)
                                      ->whereYear('created_at', now()->year)
                                      ->count();
            
            return view('accounts.index', compact(
                'accounts', 
                'search', 
                'accountType', 
                'status',
                'totalAccounts',
                'totalBalance', 
                'activeAccounts',
                'thisMonthAccounts'
            ));
        }
    }

    public function create()
    {
        $user = Auth::user();
        
        if ($user->hasRole('member')) {
            // Members can open accounts for themselves
            $members = collect([$user]);
        } else {
            // Staff can open accounts for any member
            $members = User::where('role', 'member')->with('accounts')->orderBy('name')->get();
        }
        
        $accountTypes = collect(Account::ACCOUNT_TYPES)->map(function($type) {
            return [
                'value' => $type,
                'label' => Account::getDisplayNameForType($type),
                'description' => $this->getAccountDescription($type),
                'interest_rate' => $this->getInterestRate($type),
                'minimum_balance' => $this->getMinimumBalance($type),
                'icon' => $this->getAccountIcon($type),
                'color' => $this->getAccountColor($type)
            ];
        });
        
        // Existing types for current member (if applicable)
        $existingTypes = [];
        if ($user->hasRole('member')) {
            $existingTypes = $user->accounts()->pluck('account_type')->toArray();
        }

        // Types that can be opened multiple times
        $multiAllowed = ['deposits', 'junior', 'goal_based', 'business'];

        return view('accounts.create', compact('members', 'accountTypes', 'existingTypes', 'multiAllowed'));
    }

    public function store(AccountRequest $request)
    {
        $user = Auth::user();
        
        // Authorization check for non-members (staff/admin)
        if (!$user->hasRole('member')) {
            $this->authorize('create', Account::class);
        }
        
        try {
            DB::beginTransaction();
            
            $memberId = $user->hasRole('member') ? $user->id : $request->member_id;
            
            // Generate account number
            $accountNumber = $this->generateAccountNumber($request->account_type);
            
            $account = Account::create([
                'member_id' => $memberId,
                'account_number' => $accountNumber,
                'account_type' => $request->account_type,
                'balance' => $request->initial_deposit ?? 0,
                'status' => Account::STATUS_ACTIVE,
            ]);
            
            DB::commit();
            
            return redirect()->route('accounts.show', $account)
                           ->with('success', 'Account opened successfully! Account Number: ' . $accountNumber);
                           
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to open account: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    public function show(Account $account)
    {
        $account->load(['member', 'transactions' => function($query) {
            $query->latest()->take(20);
        }]);
        
        // Authorization check
        $user = Auth::user();
        if ($user->hasRole('member') && $account->member_id !== $user->id) {
            abort(403, 'You can only view your own accounts.');
        }
        
        $accountInfo = [
            'display_name' => $account->getDisplayName(),
            'description' => $this->getAccountDescription($account->account_type),
            'interest_rate' => $this->getInterestRate($account->account_type),
            'minimum_balance' => $this->getMinimumBalance($account->account_type),
            'icon' => $this->getAccountIcon($account->account_type),
            'color' => $this->getAccountColor($account->account_type)
        ];
        
        return view('accounts.show', compact('account', 'accountInfo'));
    }

    public function updateStatus(Request $request, Account $account)
    {
        $this->authorize('manage', $account);
        
        $request->validate([
            'status' => 'required|in:' . implode(',', [
                Account::STATUS_ACTIVE,
                Account::STATUS_INACTIVE,
                Account::STATUS_FROZEN,
                Account::STATUS_CLOSED
            ]),
            'status_reason' => 'required_if:status,' . Account::STATUS_FROZEN . ',' . Account::STATUS_CLOSED . '|string|max:500'
        ]);
        
        $account->update([
            'status' => $request->status,
            'status_reason' => $request->status_reason
        ]);
        
        $statusLabels = [
            Account::STATUS_ACTIVE => 'activated',
            Account::STATUS_INACTIVE => 'marked as inactive', 
            Account::STATUS_FROZEN => 'frozen',
            Account::STATUS_CLOSED => 'closed'
        ];
        
        return back()->with('success', 'Account has been ' . $statusLabels[$request->status] . ' successfully.');
    }

    public function my()
    {
        $user = Auth::user();
        $accounts = $user->accounts()->with('member')->latest()->get();
        return view('accounts.my', compact('accounts'));
    }

    public function destroy(Account $account)
    {
        $this->authorize('manage', $account);
        
        // Verify account has zero balance
        if ($account->balance > 0) {
            return back()->withErrors(['balance' => 'Cannot close account with positive balance. Please withdraw all funds first.']);
        }
        
        $account->update([
            'status' => Account::STATUS_CLOSED,
            'status_reason' => 'Account closure requested'
        ]);
        
        return redirect()->route('accounts.index')->with('success', 'Account closed successfully.');
    }

    public function statement(Account $account)
    {
        $user = Auth::user();
        if ($user->hasRole('member') && $account->member_id !== $user->id) {
            abort(403, 'You can only view your own account statements.');
        }
        
        // Generate PDF statement (placeholder implementation)
        return response('PDF Statement Content', 200)
               ->header('Content-Type', 'application/pdf');
    }

    public function closeRequest(Request $request, Account $account)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);
        
        // Create notification for admin approval
        $account->member->notify(new \App\Notifications\SystemNotification(
            'Account Closure Request',
            "Account closure request for {$account->account_number}. Reason: {$request->reason}",
            ['account_id' => $account->id, 'type' => 'account_closure_request']
        ));
        
        return back()->with('success', 'Account closure request submitted successfully.');
    }

    private function generateAccountNumber($accountType)
    {
        $prefix = strtoupper(substr($accountType, 0, 2));
        $nextNumber = Account::where('account_type', $accountType)->count() + 1;
        return $prefix . str_pad($nextNumber, 8, '0', STR_PAD_LEFT);
    }

    private function getAccountDescription($type)
    {
        $descriptions = [
            'shares' => 'Every member contributes a minimum share capital. Establishes SACCO ownership; not withdrawable while a member',
            'savings' => 'Regular savings where members make monthly or voluntary deposits; acts as loan security; earns dividends/interest',
            'deposits' => 'Term deposit for a fixed period; higher interest; withdrawals restricted until maturity',
            'junior' => 'Special account for minors; parents/guardians save on their behalf; education-focused benefits',
            'goal_based' => 'Smart savings for specific goals (holidays, projects, weddings); encourages disciplined saving',
            'business' => 'For members running businesses; manage business finances separately from personal savings',
        ];
        
        return $descriptions[$type] ?? 'Specialized SACCO account';
    }

    private function getInterestRate($type)
    {
        $rates = [
            'shares' => 0.0, // non-withdrawable capital; returns via dividends
            'savings' => 8.5,
            'deposits' => 15.0,
            'junior' => 7.0,
            'goal_based' => 7.5,
            'business' => 8.0,
        ];
        
        return $rates[$type] ?? 7.0;
    }

    private function getMinimumBalance($type)
    {
        $minimums = [
            'shares' => 5000,
            'savings' => 1000,
            'deposits' => 10000,
            'junior' => 500,
            'goal_based' => 500,
            'business' => 2000,
        ];
        
        return $minimums[$type] ?? 1000;
    }

    private function getAccountIcon($type)
    {
        $icons = [
            'shares' => 'building-library',
            'savings' => 'banknotes',
            'deposits' => 'safe',
            'junior' => 'academic-cap',
            'goal_based' => 'flag',
            'business' => 'briefcase',
        ];
        
        return $icons[$type] ?? 'banknotes';
    }

    private function getAccountColor($type)
    {
        $colors = [
            'shares' => 'blue',
            'savings' => 'emerald',
            'deposits' => 'purple',
            'junior' => 'cyan',
            'goal_based' => 'yellow',
            'business' => 'slate',
        ];
        
        return $colors[$type] ?? 'gray';
    }
} 