<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\MobileMoneyWebhookController;
use Inertia\Inertia;

// Frontend routes
Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
    
    // Members
    Route::get('members', function () {
        return Inertia::render('members/index');
    })->name('members.index');
    
    Route::get('members/create', function () {
        return Inertia::render('members/create');
    })->name('members.create');
    
    // Accounts
    Route::get('accounts', function () {
        return Inertia::render('accounts/index');
    })->name('accounts.index');
    
    Route::get('accounts/create', function () {
        return Inertia::render('accounts/create');
    })->name('accounts.create');
    
    // Transactions
    Route::get('transactions', function () {
        return Inertia::render('transactions/index');
    })->name('transactions.index');
    
    Route::get('transactions/create', function () {
        return Inertia::render('transactions/create');
    })->name('transactions.create');
    
    // Loans
    Route::get('loans', function () {
        return Inertia::render('loans/index');
    })->name('loans.index');
    
    Route::get('loans/create', function () {
        return Inertia::render('loans/create');
    })->name('loans.create');
    
    Route::get('loans/applications', function () {
        return Inertia::render('loans/applications');
    })->name('loans.applications');
    
    // Savings
    Route::get('savings', function () {
        return Inertia::render('savings/index');
    })->name('savings.index');
    
    Route::get('savings/goals', function () {
        return Inertia::render('savings/goals');
    })->name('savings.goals');
    
    // Reports
    Route::get('reports', function () {
        return Inertia::render('reports/index');
    })->name('reports.index');
    
    Route::get('reports/financial', function () {
        return Inertia::render('reports/financial');
    })->name('reports.financial');
    
    Route::get('reports/members', function () {
        return Inertia::render('reports/members');
    })->name('reports.members');
    
    // Branches
    Route::get('branches', function () {
        return Inertia::render('branches/index');
    })->name('branches.index');
    
    Route::get('branches/create', function () {
        return Inertia::render('branches/create');
    })->name('branches.create');
    
    // Notifications
    Route::get('notifications', function () {
        return Inertia::render('notifications/index');
    })->name('notifications.index');
});

// API routes
Route::prefix('api')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware(['auth:sanctum'])
        ->name('api.dashboard');

    // Transaction routes
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/transactions', [TransactionController::class, 'index'])->name('api.transactions.index');
        Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->name('api.transactions.show');
        Route::post('/transactions', [TransactionController::class, 'store'])->name('api.transactions.store');
        Route::get('/transactions/{transaction}/receipt', [TransactionController::class, 'receipt'])->name('api.transactions.receipt');
        Route::post('/transactions/{transaction}/approve', [TransactionController::class, 'approve'])->name('api.transactions.approve');
        Route::post('/transactions/{transaction}/reject', [TransactionController::class, 'reject'])->name('api.transactions.reject');
    });
});

// Webhook routes (no authentication required)
Route::prefix('webhooks')->name('webhooks.')->group(function () {
    Route::post('/mpesa/callback', [MobileMoneyWebhookController::class, 'mpesaCallback'])->name('mpesa.callback');
    Route::post('/airtel/callback', [MobileMoneyWebhookController::class, 'airtelCallback'])->name('airtel.callback');
    Route::post('/tkash/callback', [MobileMoneyWebhookController::class, 'tkashCallback'])->name('tkash.callback');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';