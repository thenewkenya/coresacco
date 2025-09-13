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