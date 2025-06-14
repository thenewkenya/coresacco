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
        Route::get('/', function () { return view('savings.index'); })->name('index');
        Route::get('/my', function () { return view('savings.my'); })->name('my');
    });

    Route::prefix('loans')->name('loans.')->group(function () {
        Route::get('/', function () { return view('loans.index'); })->name('index');
        Route::get('/my', function () { return view('loans.my'); })->name('my');
    });

    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', function () { return view('payments.index'); })->name('index');
        Route::get('/my', function () { return view('payments.my'); })->name('my');
    });

    // Member Services
    Route::prefix('members')->name('members.')->group(function () {
        Route::get('/', function () { return view('members.index'); })->name('index');
        Route::get('/profile', function () { return view('members.profile'); })->name('profile');
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



    Route::prefix('schedule')->name('schedule.')->group(function () {
        Route::get('/', function () { return view('schedule.index'); })->name('index');
    });
});

require __DIR__.'/auth.php';
