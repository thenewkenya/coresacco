<?php

namespace App\Http\Controllers;

use App\Models\LoanAccount;
use App\Models\LedgerEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class LoanAccountController extends Controller
{
    public function index(Request $request): Response
    {
        $user = Auth::user();
        
        $query = LoanAccount::with(['member', 'loan.loanType'])
            ->when($user->role === 'member', function ($query) use ($user) {
                return $query->where('member_id', $user->id);
            });

        // Apply filters
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('account_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('member', function($memberQuery) use ($request) {
                      $memberQuery->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('loan_type')) {
            $query->where('loan_type', $request->loan_type);
        }

        $loanAccounts = $query->orderBy('created_at', 'desc')->paginate(15);

        // Calculate statistics
        $totalAccounts = LoanAccount::when($user->role === 'member', function ($query) use ($user) {
            return $query->where('member_id', $user->id);
        })->count();

        $totalDisbursed = LoanAccount::when($user->role === 'member', function ($query) use ($user) {
            return $query->where('member_id', $user->id);
        })->sum('amount_disbursed');

        $totalOutstanding = LoanAccount::when($user->role === 'member', function ($query) use ($user) {
            return $query->where('member_id', $user->id);
        })->sum('outstanding_principal');

        $activeAccounts = LoanAccount::when($user->role === 'member', function ($query) use ($user) {
            return $query->where('member_id', $user->id);
        })->where('status', LoanAccount::STATUS_ACTIVE)->count();

        return Inertia::render('loan-accounts/index', [
            'loanAccounts' => $loanAccounts,
            'stats' => [
                'totalAccounts' => $totalAccounts,
                'totalDisbursed' => $totalDisbursed,
                'totalOutstanding' => $totalOutstanding,
                'activeAccounts' => $activeAccounts,
            ],
            'filters' => $request->only(['search', 'status', 'loan_type']),
            'statusOptions' => [
                'active' => 'Active',
                'completed' => 'Completed',
                'defaulted' => 'Defaulted',
                'written_off' => 'Written Off',
            ],
            'loanTypeOptions' => [
                'salary_backed' => 'Salary Backed',
                'asset_backed' => 'Asset Backed',
                'group_loan' => 'Group Loan',
                'business_loan' => 'Business Loan',
                'emergency' => 'Emergency',
            ],
        ]);
    }

    public function show(LoanAccount $loanAccount): Response
    {
        $user = Auth::user();
        
        // Check permissions
        if ($user->role === 'member' && $loanAccount->member_id !== $user->id) {
            abort(403, 'You can only view your own loan accounts.');
        }

        $loanAccount->load(['member', 'loan.loanType', 'ledgerEntries' => function($query) {
            $query->orderBy('transaction_date', 'desc')->orderBy('created_at', 'desc');
        }]);

        // Calculate arrears
        $arrears = $loanAccount->calculateArrears();

        return Inertia::render('loan-accounts/show', [
            'loanAccount' => $loanAccount,
            'arrears' => $arrears,
        ]);
    }
}
