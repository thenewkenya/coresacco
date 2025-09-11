<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Volt::route('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

    // Loan Application Routes (for members) - must be outside auth group
    Volt::route('/loans/apply', 'loans.apply')->name('loans.apply')->middleware('auth');
    Route::post('/loans/apply', [App\Http\Controllers\LoanApplicationController::class, 'store'])->name('loans.apply.store')->middleware('auth');
    
    // Member eligibility check route
    Route::get('/members/{member}/eligibility', [App\Http\Controllers\LoanApplicationController::class, 'checkMemberEligibility'])->name('members.eligibility')->middleware('auth');
    
    // Test route for eligibility
    Route::get('/test-eligibility/{member}', function($memberId) {
        try {
            $member = \App\Models\Member::find($memberId);
            if (!$member) {
                return response()->json(['success' => false, 'message' => 'Member not found'], 404);
            }
            
            $savingsBalance = $member->getTotalSavingsBalance();
            $sharesBalance = $member->getTotalSharesBalance();
            $totalBalance = $member->getTotalBalance();
            $monthsInSacco = $member->getMonthsInSacco();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'member_id' => $member->id,
                    'member_name' => $member->name,
                    'savings_balance' => $savingsBalance,
                    'shares_balance' => $sharesBalance,
                    'total_balance' => $totalBalance,
                    'months_in_sacco' => $monthsInSacco,
                    'max_loan_amount' => $savingsBalance * 3.0,
                    'overall_eligible' => $savingsBalance >= 1000 && $monthsInSacco >= 6
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    })->middleware('auth');
    
    // Test route for form submission
    Route::post('/test-loan-submission', function(\Illuminate\Http\Request $request) {
        return response()->json([
            'success' => true,
            'message' => 'Form submission test successful',
            'data' => $request->all()
        ]);
    })->middleware('auth');

// Test route to verify routing is working
Route::get('/test-apply', function() {
    return 'Test route working!';
})->name('test.apply');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    // Transaction Processing System
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Volt::route('/', 'transactions.index')->name('index');
        Route::post('/', [App\Http\Controllers\TransactionController::class, 'store'])->name('store');
        Route::get('/my', [App\Http\Controllers\TransactionController::class, 'my'])->name('my');
        Route::get('/{transaction}', [App\Http\Controllers\TransactionController::class, 'show'])->name('show');
        Route::post('/{transaction}/reverse', [App\Http\Controllers\TransactionController::class, 'reverse'])->name('reverse');
        Route::post('/bulk-deposit', [App\Http\Controllers\TransactionController::class, 'bulkDeposit'])->name('bulk-deposit');
        
        // Deposit routes
        Volt::route('/create/deposit', 'transactions.create-deposit')->name('deposit.create');
        Route::post('/deposit', [App\Http\Controllers\TransactionController::class, 'storeDeposit'])->name('deposit.store');
        
        // Withdrawal routes
        Volt::route('/create/withdrawal', 'transactions.create-withdrawal')->name('withdrawal.create');
        Route::post('/withdrawal', [App\Http\Controllers\TransactionController::class, 'storeWithdrawal'])->name('withdrawal.store');
        
        // Transfer routes
        Volt::route('/create/transfer', 'transactions.create-transfer')->name('transfer.create');
        Route::post('/create/transfer', [App\Http\Controllers\TransactionController::class, 'storeTransfer'])->name('transfer.store');
        
        // Receipt routes
        Route::get('/receipt/{transaction}', [App\Http\Controllers\TransactionController::class, 'receipt'])->name('receipt');
        Route::get('/receipt/{transaction}/download', [App\Http\Controllers\TransactionController::class, 'downloadReceipt'])->name('receipt.download');
        Route::get('/receipt/{transaction}/qr', [App\Http\Controllers\ReceiptQrController::class, 'show'])->name('receipt.qr');
        
        // Approval routes
        Route::post('/approve/{transaction}', [App\Http\Controllers\TransactionController::class, 'approve'])->name('approve')->middleware('can:approve,transaction');
        Route::post('/reject/{transaction}', [App\Http\Controllers\TransactionController::class, 'reject'])->name('reject')->middleware('can:approve,transaction');
        Route::post('/bulk/approve', [App\Http\Controllers\TransactionController::class, 'bulkApprove'])->name('bulk.approve');
        Route::post('/bulk/reject', [App\Http\Controllers\TransactionController::class, 'bulkReject'])->name('bulk.reject');
        
        // AJAX routes
        Route::get('/account/{account}/details', [App\Http\Controllers\TransactionController::class, 'getAccountDetails'])->name('account.details');
    });

    // Financial Services
    Route::prefix('savings')->name('savings.')->group(function () {
        Route::get('/', [App\Http\Controllers\SavingsController::class, 'index'])->name('index');
        Volt::route('/my', 'member.my-savings')->name('my');
        Route::get('/create', [App\Http\Controllers\SavingsController::class, 'create'])->name('create')->middleware('can:create,App\Models\Account');
        Route::post('/', [App\Http\Controllers\SavingsController::class, 'store'])->name('store')->middleware('can:create,App\Models\Account');
        Route::get('/{account}', [App\Http\Controllers\SavingsController::class, 'show'])->name('show');
        Route::post('/{account}/deposit', [App\Http\Controllers\SavingsController::class, 'deposit'])->name('deposit');
        Route::post('/{account}/withdraw', [App\Http\Controllers\SavingsController::class, 'withdraw'])->name('withdraw');
        Route::post('/{account}/interest', [App\Http\Controllers\SavingsController::class, 'calculateInterest'])->name('interest')->middleware('can:manage,App\Models\Account');
        Route::patch('/{account}/status', [App\Http\Controllers\SavingsController::class, 'updateStatus'])->name('status')->middleware('can:manage,App\Models\Account');
        Route::get('/reports/generate', [App\Http\Controllers\SavingsController::class, 'report'])->name('report')->middleware('can:view-reports');
    });

        Route::prefix('loans')->name('loans.')->group(function () {
        // Member loan access (must come before {loan} route)
        Volt::route('/my', 'loans.my')->name('my')->middleware('auth');
        
        // Staff/Admin loan management
        Route::middleware(['auth'])->group(function () {
            Volt::route('/', 'loans.index')->name('index')
                ->middleware('role:admin,manager,staff');
            Route::get('/create', [App\Http\Controllers\LoansController::class, 'create'])->name('create')
                ->middleware('role:admin,manager,staff');
            Route::post('/', [App\Http\Controllers\LoansController::class, 'store'])->name('store')
                ->middleware('role:admin,manager,staff');
            Volt::route('/{loan}', 'loans.show')->name('show');
            Route::post('/{loan}/approve', [App\Http\Controllers\LoansController::class, 'approve'])->name('approve')
                ->middleware('role:admin,manager');
            Route::post('/{loan}/reject', [App\Http\Controllers\LoansController::class, 'reject'])->name('reject')
                ->middleware('role:admin,manager');
            Route::post('/{loan}/repayment', [App\Http\Controllers\LoansController::class, 'repayment'])->name('repayment');
            Route::get('/reports/generate', [App\Http\Controllers\LoansController::class, 'report'])->name('report')
                ->middleware('role:admin,manager');
        });
    });

    // Loan Applications with Borrowing Criteria
    Route::prefix('loan-applications')->name('loan-applications.')->group(function () {
        Route::get('/', [App\Http\Controllers\LoanApplicationController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\LoanApplicationController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\LoanApplicationController::class, 'store'])->name('store');
        Route::get('/{loan}', [App\Http\Controllers\LoanApplicationController::class, 'show'])->name('show');
        Route::get('/{loan}/eligibility', [App\Http\Controllers\LoanApplicationController::class, 'getEligibilityReport'])->name('eligibility');
    });

    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [App\Http\Controllers\PaymentsController::class, 'index'])->name('index');
        Volt::route('/my', 'payments.my')->name('my');
        Volt::route('/create', 'payments.create')->name('create');
        Route::post('/', [App\Http\Controllers\PaymentsController::class, 'store'])->name('store');
        Route::get('/{transaction}', [App\Http\Controllers\PaymentsController::class, 'show'])->name('show');
        Route::post('/{transaction}/approve', [App\Http\Controllers\PaymentsController::class, 'approve'])->name('approve')->middleware('can:approve,transaction');
        Route::post('/{transaction}/reject', [App\Http\Controllers\PaymentsController::class, 'reject'])->name('reject')->middleware('can:approve,transaction');
        Route::delete('/{transaction}/reverse', [App\Http\Controllers\PaymentsController::class, 'reverse'])->name('reverse')->middleware('auth');
        Route::get('/{transaction}/receipt', [App\Http\Controllers\PaymentsController::class, 'receipt'])->name('receipt');
        Route::post('/mobile-money', [App\Http\Controllers\PaymentsController::class, 'mobileMoney'])->name('mobile-money');
        Route::get('/reports/generate', [App\Http\Controllers\PaymentsController::class, 'report'])->name('report')->middleware('can:view-reports');
    });
    
    // Mobile Money quick route removed (use transactions/payment flows instead)

    // Member Services
    Route::prefix('members')->name('members.')->group(function () {
        Volt::route('/', 'members.manage-members')->name('index');
        Route::get('/profile', [App\Http\Controllers\MemberController::class, 'profile'])->name('profile');
        Route::get('/{member}', [App\Http\Controllers\MemberController::class, 'show'])->name('show');
        Route::get('/{member}/transactions', [App\Http\Controllers\MemberController::class, 'transactions'])->name('transactions');
    });

    Route::prefix('goals')->name('goals.')->group(function () {
        Volt::route('/', 'goals.index')->name('index');
        Volt::route('/create', 'goals.create-goal')->name('create');
        Route::get('/{goal}', [App\Http\Controllers\GoalsController::class, 'show'])->name('show');
        Route::get('/{goal}/edit', [App\Http\Controllers\GoalsController::class, 'edit'])->name('edit');
        Route::put('/{goal}', [App\Http\Controllers\GoalsController::class, 'update'])->name('update');
        Route::delete('/{goal}', [App\Http\Controllers\GoalsController::class, 'destroy'])->name('destroy');
        Route::post('/{goal}/progress', [App\Http\Controllers\GoalsController::class, 'updateProgress'])->name('progress.update');
    });

    Route::prefix('budget')->name('budget.')->group(function () {
        Volt::route('/', 'budget.index')->name('index');
        Volt::route('/create', 'budget.create-budget')->name('create');
        Route::get('/smart-suggestions', [App\Http\Controllers\BudgetController::class, 'getSmartSuggestions'])->name('smart-suggestions');
        Route::get('/{budget}', [App\Http\Controllers\BudgetController::class, 'show'])->name('show');
        Route::post('/{budget}/expenses', [App\Http\Controllers\BudgetController::class, 'recordExpense'])->name('expenses.store');
        Route::delete('/{budget}/expenses/{expense}', [App\Http\Controllers\BudgetController::class, 'deleteExpense'])->name('expenses.destroy');
        Route::get('/{budget}/report', [App\Http\Controllers\BudgetController::class, 'report'])->name('report');
    });



    // Management & Analytics
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Volt::route('/', 'analytics.index')->name('index');
        Route::get('/export', [App\Http\Controllers\AnalyticsController::class, 'export'])->name('export');
    });

    Route::prefix('reports')->name('reports.')->group(function () {
        Volt::route('/', 'reports.index')->name('index');
        Route::get('/financial', [App\Http\Controllers\ReportsController::class, 'financial'])->name('financial');
        Route::get('/members', [App\Http\Controllers\ReportsController::class, 'members'])->name('members');
        Route::get('/loans', [App\Http\Controllers\ReportsController::class, 'loans'])->name('loans');
        Route::get('/operational', [App\Http\Controllers\ReportsController::class, 'operational'])->name('operational');
    });

    Route::prefix('branches')->name('branches.')->group(function () {
        Volt::route('/', 'branches.index')->name('index');
        Route::get('/map', [App\Http\Controllers\BranchController::class, 'mapView'])->name('map');
        Route::get('/create', [App\Http\Controllers\BranchController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\BranchController::class, 'store'])->name('store');
        Route::get('/{branch}', [App\Http\Controllers\BranchController::class, 'show'])->name('show');
        Route::get('/{branch}/edit', [App\Http\Controllers\BranchController::class, 'edit'])->name('edit');
        Route::put('/{branch}', [App\Http\Controllers\BranchController::class, 'update'])->name('update');
        Route::delete('/{branch}', [App\Http\Controllers\BranchController::class, 'destroy'])->name('destroy');
        Route::get('/{branch}/staff', [App\Http\Controllers\BranchController::class, 'staff'])->name('staff');
        Route::post('/{branch}/staff/assign', [App\Http\Controllers\BranchController::class, 'assignStaff'])->name('staff.assign');
        Route::delete('/{branch}/staff/remove', [App\Http\Controllers\BranchController::class, 'removeStaff'])->name('staff.remove');
        Route::get('/{branch}/analytics', [App\Http\Controllers\BranchController::class, 'analytics'])->name('analytics');
    });

    Route::prefix('roles')->name('roles.')->group(function () {
        Volt::route('/', 'roles.index')->name('index');
        Route::get('/create', [App\Http\Controllers\RoleController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\RoleController::class, 'store'])->name('store');
        Route::get('/{role}', [App\Http\Controllers\RoleController::class, 'show'])->name('show');
        Route::get('/{role}/edit', [App\Http\Controllers\RoleController::class, 'edit'])->name('edit');
        Route::put('/{role}', [App\Http\Controllers\RoleController::class, 'update'])->name('update');
        Route::delete('/{role}', [App\Http\Controllers\RoleController::class, 'destroy'])->name('destroy');
        
        // Role assignment routes
        Route::post('/assign', [App\Http\Controllers\RoleController::class, 'assignRole'])->name('assign');
        Route::post('/remove', [App\Http\Controllers\RoleController::class, 'removeRole'])->name('remove');
        Route::post('/bulk-assign', [App\Http\Controllers\RoleController::class, 'bulkAssign'])->name('bulk-assign');
        
        // AJAX routes
        Route::get('/{role}/permissions', [App\Http\Controllers\RoleController::class, 'permissions'])->name('permissions');
    });


    Route::prefix('system')->name('system.')->group(function () {
        Volt::route('/settings', 'system.settings')->name('settings');
        Route::post('/settings', [App\Http\Controllers\SystemController::class, 'updateSettings'])->name('settings.update');
        Route::post('/settings/reset', [App\Http\Controllers\SystemController::class, 'resetSettings'])->name('settings.reset');
    });

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Volt::route('/', 'notifications.notification-center')->name('index');
        Route::post('/{id}/mark-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{id}', [App\Http\Controllers\NotificationController::class, 'destroy'])->name('destroy');
        Route::get('/clear-read', [App\Http\Controllers\NotificationController::class, 'clearRead'])->name('clearRead');
        Volt::route('/settings', 'notifications.settings')->name('settings');
    });

    Route::prefix('schedule')->name('schedule.')->group(function () {
        Route::get('/', function () { return view('schedule.index'); })->name('index');
    });

    // Account Management Routes
    Route::prefix('accounts')->name('accounts.')->group(function () {
        Volt::route('/', 'accounts.index')->name('index');
        Route::get('/my', [App\Http\Controllers\AccountController::class, 'my'])->name('my');
        Route::get('/create', [App\Http\Controllers\AccountController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\AccountController::class, 'store'])->name('store');
        Route::get('/{account}', [App\Http\Controllers\AccountController::class, 'show'])->name('show');
        Route::patch('/{account}/status', [App\Http\Controllers\AccountController::class, 'updateStatus'])
            ->name('update-status')
            ->middleware('can:manage,account');
        Route::delete('/{account}', [App\Http\Controllers\AccountController::class, 'destroy'])->name('destroy');
        Route::get('/{account}/statement', [App\Http\Controllers\AccountController::class, 'statement'])->name('statement');
        Route::post('/{account}/close-request', [App\Http\Controllers\AccountController::class, 'closeRequest'])->name('close-request');
    });

    // Documentation Route
    Route::get('/documentation', function () {
        return view('documentation.index');
    })->name('documentation');
});



Route::middleware(['auth', 'verified'])->prefix('savings')->name('savings.')->group(function () {
    // ... existing code ...
});

require __DIR__.'/auth.php';

// Mobile Money Webhook Routes (no authentication required)
Route::prefix('webhooks')->name('webhooks.')->group(function () {
    Route::post('/mpesa/callback', [App\Http\Controllers\MobileMoneyWebhookController::class, 'mpesaCallback'])->name('mpesa.callback');
    Route::post('/airtel/callback', [App\Http\Controllers\MobileMoneyWebhookController::class, 'airtelCallback'])->name('airtel.callback');
    Route::post('/tkash/callback', [App\Http\Controllers\MobileMoneyWebhookController::class, 'tkashCallback'])->name('tkash.callback');
});
