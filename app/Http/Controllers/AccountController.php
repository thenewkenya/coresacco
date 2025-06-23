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
            
            if (request('search')) {
                $search = request('search');
                $query->where(function($q) use ($search) {
                    $q->where('account_number', 'LIKE', "%{$search}%")
                      ->orWhereHas('member', function($memberQuery) use ($search) {
                          $memberQuery->where('name', 'LIKE', "%{$search}%")
                                     ->orWhere('email', 'LIKE', "%{$search}%");
                      });
                });
            }
            
            if (request('account_type')) {
                $query->where('account_type', request('account_type'));
            }
            
            if (request('status')) {
                $query->where('status', request('status'));
            }
            
            $accounts = $query->latest()->paginate(15);
            $accountTypes = collect(Account::ACCOUNT_TYPES)->map(function($type) {
                return [
                    'value' => $type, 
                    'label' => Account::getDisplayNameForType($type)
                ];
            });
            
            return view('accounts.index', compact('accounts', 'accountTypes'));
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
            $members = User::where('role', 'member')->orderBy('name')->get();
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
        
        return view('accounts.create', compact('members', 'accountTypes'));
    }

    public function store(AccountRequest $request)
    {
        try {
            DB::beginTransaction();
            
            $user = Auth::user();
            $memberId = $user->hasRole('member') ? $user->id : $request->member_id;
            
            // Generate account number
            $accountNumber = $this->generateAccountNumber($request->account_type);
            
            $account = Account::create([
                'member_id' => $memberId,
                'account_number' => $accountNumber,
                'account_type' => $request->account_type,
                'balance' => 0,
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
                Account::STATUS_DORMANT,
                Account::STATUS_FROZEN,
                Account::STATUS_CLOSED
            ]),
            'reason' => 'required_if:status,' . Account::STATUS_FROZEN . ',' . Account::STATUS_CLOSED . '|string|max:500'
        ]);
        
        $account->update([
            'status' => $request->status,
            'status_reason' => $request->reason
        ]);
        
        $statusLabels = [
            Account::STATUS_ACTIVE => 'activated',
            Account::STATUS_DORMANT => 'marked as dormant', 
            Account::STATUS_FROZEN => 'frozen',
            Account::STATUS_CLOSED => 'closed'
        ];
        
        return back()->with('success', 'Account has been ' . $statusLabels[$request->status] . ' successfully.');
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
            'savings' => 'Regular savings account for everyday transactions',
            'shares' => 'Share capital account representing ownership in the SACCO',
            'deposits' => 'Fixed term deposit account with higher interest rates',
            'emergency_fund' => 'Emergency fund for unexpected expenses',
            'holiday_savings' => 'Dedicated savings for holiday and vacation expenses',
            'retirement' => 'Long-term retirement savings with compound growth',
            'education' => 'Education fund for school fees and learning expenses',
            'development' => 'Community development fund for local projects',
            'welfare' => 'Member welfare fund for support during difficult times',
            'loan_guarantee' => 'Fund used as collateral for loan applications',
            'insurance' => 'Insurance premium fund for life and credit protection',
            'investment' => 'High-yield investment account for substantial returns'
        ];
        
        return $descriptions[$type] ?? 'Specialized SACCO account';
    }

    private function getInterestRate($type)
    {
        $rates = [
            'savings' => 8.5, 'shares' => 12.0, 'deposits' => 15.0,
            'emergency_fund' => 6.0, 'holiday_savings' => 7.0, 'retirement' => 10.0,
            'education' => 9.0, 'development' => 8.0, 'welfare' => 6.5,
            'loan_guarantee' => 5.0, 'insurance' => 4.0, 'investment' => 18.0
        ];
        
        return $rates[$type] ?? 7.0;
    }

    private function getMinimumBalance($type)
    {
        $minimums = [
            'savings' => 1000, 'shares' => 5000, 'deposits' => 10000,
            'emergency_fund' => 500, 'holiday_savings' => 500, 'retirement' => 2000,
            'education' => 1000, 'development' => 1000, 'welfare' => 500,
            'loan_guarantee' => 5000, 'insurance' => 1000, 'investment' => 25000
        ];
        
        return $minimums[$type] ?? 1000;
    }

    private function getAccountIcon($type)
    {
        $icons = [
            'savings' => 'banknotes', 'shares' => 'building-library', 'deposits' => 'safe',
            'emergency_fund' => 'shield-check', 'holiday_savings' => 'sun', 'retirement' => 'home',
            'education' => 'academic-cap', 'development' => 'building-office-2', 'welfare' => 'heart',
            'loan_guarantee' => 'shield-exclamation', 'insurance' => 'shield-check', 'investment' => 'chart-bar'
        ];
        
        return $icons[$type] ?? 'banknotes';
    }

    private function getAccountColor($type)
    {
        $colors = [
            'savings' => 'emerald', 'shares' => 'blue', 'deposits' => 'purple',
            'emergency_fund' => 'red', 'holiday_savings' => 'yellow', 'retirement' => 'indigo',
            'education' => 'cyan', 'development' => 'orange', 'welfare' => 'pink',
            'loan_guarantee' => 'gray', 'insurance' => 'teal', 'investment' => 'amber'
        ];
        
        return $colors[$type] ?? 'gray';
    }
} 