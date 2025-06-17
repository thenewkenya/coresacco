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
        Route::get('/create/deposit', [App\Http\Controllers\TransactionController::class, 'createDeposit'])->name('deposit.create');
        Route::post('/create/deposit', [App\Http\Controllers\TransactionController::class, 'storeDeposit'])->name('deposit.store');
        
        // Withdrawal routes
        Route::get('/create/withdrawal', [App\Http\Controllers\TransactionController::class, 'createWithdrawal'])->name('withdrawal.create');
        Route::post('/create/withdrawal', [App\Http\Controllers\TransactionController::class, 'storeWithdrawal'])->name('withdrawal.store');
        
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
        Route::get('/my', [App\Http\Controllers\SavingsController::class, 'my'])->name('my');
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
        Route::get('/my', [App\Http\Controllers\LoansController::class, 'my'])->name('my');
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
        Route::get('/{transaction}/receipt', [App\Http\Controllers\PaymentsController::class, 'receipt'])->name('receipt');
        Route::post('/mobile-money', [App\Http\Controllers\PaymentsController::class, 'mobileMoney'])->name('mobile-money');
        Route::get('/reports/generate', [App\Http\Controllers\PaymentsController::class, 'report'])->name('report')->middleware('can:view-reports');
    });

    // Member Services
    Route::prefix('members')->name('members.')->group(function () {
        Route::get('/', [App\Http\Controllers\MemberController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\MemberController::class, 'create'])->name('create');
        Route::get('/profile', [App\Http\Controllers\MemberController::class, 'profile'])->name('profile');
        Route::post('/', [App\Http\Controllers\MemberController::class, 'store'])->name('store');
        Route::get('/{member}', [App\Http\Controllers\MemberController::class, 'show'])->name('show');
        Route::get('/{member}/edit', [App\Http\Controllers\MemberController::class, 'edit'])->name('edit');
        Route::put('/{member}', [App\Http\Controllers\MemberController::class, 'update'])->name('update');
        Route::delete('/{member}', [App\Http\Controllers\MemberController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('goals')->name('goals.')->group(function () {
        Route::get('/', function () { return view('goals.index'); })->name('index');
    });

    Route::prefix('budget')->name('budget.')->group(function () {
        Route::get('/', function () { return view('budget.index'); })->name('index');
    });

    Route::prefix('insurance')->name('insurance.')->group(function () {
        Route::get('/', function () { return view('insurance.index'); })->name('index');
        Route::get('/my', function () { return view('insurance.my'); })->name('my');
    });

    // Management & Analytics
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/', function () { return view('analytics.index'); })->name('index');
    });

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', function () { return view('reports.index'); })->name('index');
    });

    Route::prefix('branches')->name('branches.')->group(function () {
        Route::get('/', function () { return view('branches.index'); })->name('index');
    });

    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', function () { return view('roles.index'); })->name('index');
    });

    Route::prefix('system')->name('system.')->group(function () {
        Route::get('/settings', function () { return view('system.settings'); })->name('settings');
    });

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [App\Http\Controllers\NotificationController::class, 'index'])->name('index');
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
});

require __DIR__.'/auth.php';
