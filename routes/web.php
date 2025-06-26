<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    // Transaction Processing System
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', [App\Http\Controllers\TransactionController::class, 'index'])->name('index');
        Route::get('/{transaction}', [App\Http\Controllers\TransactionController::class, 'show'])->name('show');
        
        // Deposit routes
        Volt::route('/create/deposit', 'transactions.create-deposit')->name('deposit.create');
        
        // Withdrawal routes
        Volt::route('/create/withdrawal', 'transactions.create-withdrawal')->name('withdrawal.create');
        
        // Transfer routes
        Route::get('/create/transfer', [App\Http\Controllers\TransactionController::class, 'createTransfer'])->name('transfer.create');
        Route::post('/create/transfer', [App\Http\Controllers\TransactionController::class, 'storeTransfer'])->name('transfer.store');
        
        // Receipt routes
        Route::get('/receipt/{transaction}', [App\Http\Controllers\TransactionController::class, 'receipt'])->name('receipt');
        Route::get('/receipt/{transaction}/download', [App\Http\Controllers\TransactionController::class, 'downloadReceipt'])->name('receipt.download');
        
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
        Route::get('/', [App\Http\Controllers\LoansController::class, 'index'])->name('index');
        Volt::route('/my', 'member.my-loans')->name('my');
        Route::get('/create', [App\Http\Controllers\LoansController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\LoansController::class, 'store'])->name('store');
        Route::get('/{loan}', [App\Http\Controllers\LoansController::class, 'show'])->name('show');
        Route::post('/{loan}/approve', [App\Http\Controllers\LoansController::class, 'approve'])->name('approve')->middleware('can:approve,loan');
        Route::post('/{loan}/reject', [App\Http\Controllers\LoansController::class, 'reject'])->name('reject')->middleware('can:approve,loan');
        Route::post('/{loan}/repayment', [App\Http\Controllers\LoansController::class, 'repayment'])->name('repayment');
        Route::get('/reports/generate', [App\Http\Controllers\LoansController::class, 'report'])->name('report')->middleware('can:view-reports');
    });

    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [App\Http\Controllers\PaymentsController::class, 'index'])->name('index');
        Route::get('/my', [App\Http\Controllers\PaymentsController::class, 'my'])->name('my');
        Route::get('/create', [App\Http\Controllers\PaymentsController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\PaymentsController::class, 'store'])->name('store');
        Route::get('/{transaction}', [App\Http\Controllers\PaymentsController::class, 'show'])->name('show');
        Route::post('/{transaction}/approve', [App\Http\Controllers\PaymentsController::class, 'approve'])->name('approve')->middleware('can:approve,transaction');
        Route::post('/{transaction}/reject', [App\Http\Controllers\PaymentsController::class, 'reject'])->name('reject')->middleware('can:approve,transaction');
        Route::delete('/{transaction}/reverse', [App\Http\Controllers\PaymentsController::class, 'reverse'])->name('reverse')->middleware('auth');
        Route::get('/{transaction}/receipt', [App\Http\Controllers\PaymentsController::class, 'receipt'])->name('receipt');
        Route::post('/mobile-money', [App\Http\Controllers\PaymentsController::class, 'mobileMoney'])->name('mobile-money');
        Route::get('/reports/generate', [App\Http\Controllers\PaymentsController::class, 'report'])->name('report')->middleware('can:view-reports');
    });

    // Member Services
    Route::prefix('members')->name('members.')->group(function () {
        Volt::route('/', 'members.manage-members')->name('index');
        Route::get('/profile', [App\Http\Controllers\MemberController::class, 'profile'])->name('profile');
        Route::get('/{member}', [App\Http\Controllers\MemberController::class, 'show'])->name('show');
    });

    Route::prefix('goals')->name('goals.')->group(function () {
        Route::get('/', [App\Http\Controllers\GoalsController::class, 'index'])->name('index');
        Volt::route('/create', 'goals.create-goal')->name('create');
        Route::get('/{goal}', [App\Http\Controllers\GoalsController::class, 'show'])->name('show');
        Route::get('/{goal}/edit', [App\Http\Controllers\GoalsController::class, 'edit'])->name('edit');
        Route::put('/{goal}', [App\Http\Controllers\GoalsController::class, 'update'])->name('update');
        Route::delete('/{goal}', [App\Http\Controllers\GoalsController::class, 'destroy'])->name('destroy');
        Route::post('/{goal}/progress', [App\Http\Controllers\GoalsController::class, 'updateProgress'])->name('progress.update');
    });

    Route::prefix('budget')->name('budget.')->group(function () {
        Route::get('/', [App\Http\Controllers\BudgetController::class, 'index'])->name('index');
        Volt::route('/create', 'budget.create-budget')->name('create');
        Route::get('/smart-suggestions', [App\Http\Controllers\BudgetController::class, 'getSmartSuggestions'])->name('smart-suggestions');
        Route::get('/{budget}', [App\Http\Controllers\BudgetController::class, 'show'])->name('show');
        Route::post('/{budget}/expenses', [App\Http\Controllers\BudgetController::class, 'recordExpense'])->name('expenses.store');
        Route::delete('/{budget}/expenses/{expense}', [App\Http\Controllers\BudgetController::class, 'deleteExpense'])->name('expenses.destroy');
        Route::get('/{budget}/report', [App\Http\Controllers\BudgetController::class, 'report'])->name('report');
    });

    Route::prefix('insurance')->name('insurance.')->group(function () {
        Route::get('/', function () { return view('insurance.index'); })->name('index');
        Route::get('/my', function () { return view('insurance.my'); })->name('my');
    });

    // Management & Analytics
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/', [App\Http\Controllers\AnalyticsController::class, 'index'])->name('index');
        Route::get('/export', [App\Http\Controllers\AnalyticsController::class, 'export'])->name('export');
    });

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [App\Http\Controllers\ReportsController::class, 'index'])->name('index');
        Route::get('/financial', [App\Http\Controllers\ReportsController::class, 'financial'])->name('financial');
        Route::get('/members', [App\Http\Controllers\ReportsController::class, 'members'])->name('members');
        Route::get('/loans', [App\Http\Controllers\ReportsController::class, 'loans'])->name('loans');
        Route::get('/operational', [App\Http\Controllers\ReportsController::class, 'operational'])->name('operational');
    });

    Route::prefix('branches')->name('branches.')->group(function () {
        Route::get('/', [App\Http\Controllers\BranchController::class, 'index'])->name('index');
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
        Route::get('/', function () { return view('roles.index'); })->name('index');
    });

    Route::prefix('system')->name('system.')->group(function () {
        Route::get('/settings', function () { return view('system.settings'); })->name('settings');
    });

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Volt::route('/', 'notifications.notification-center')->name('index');
        Route::post('/{id}/mark-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{id}', [App\Http\Controllers\NotificationController::class, 'destroy'])->name('destroy');
        Route::get('/clear-read', [App\Http\Controllers\NotificationController::class, 'clearRead'])->name('clearRead');
        Route::get('/settings', [App\Http\Controllers\NotificationController::class, 'settings'])->name('settings');
        Route::post('/settings', [App\Http\Controllers\NotificationController::class, 'updateSettings'])->name('settings.update');
    });

    Route::prefix('schedule')->name('schedule.')->group(function () {
        Route::get('/', function () { return view('schedule.index'); })->name('index');
    });

    // Account Management Routes
    Route::prefix('accounts')->name('accounts.')->group(function () {
        Route::get('/', [App\Http\Controllers\AccountController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\AccountController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\AccountController::class, 'store'])->name('store');
        Route::get('/{account}', [App\Http\Controllers\AccountController::class, 'show'])->name('show');
        Route::patch('/{account}/status', [App\Http\Controllers\AccountController::class, 'updateStatus'])
            ->name('update-status')
            ->middleware('can:manage,account');
    });
});

Route::middleware(['auth', 'verified'])->prefix('savings')->name('savings.')->group(function () {
    // ... existing code ...
});

require __DIR__.'/auth.php';
