<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\MobileMoneyWebhookController;
use Inertia\Inertia;

// Frontend routes
Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    
            // Members
            Route::get('members', [App\Http\Controllers\MemberController::class, 'index'])->name('members.index');
            Route::get('members/create', [App\Http\Controllers\MemberController::class, 'create'])->name('members.create');
            Route::post('members', [App\Http\Controllers\MemberController::class, 'store'])->name('members.store');
            Route::get('members/{member}', [App\Http\Controllers\MemberController::class, 'show'])->name('members.show');
            Route::get('members/{member}/edit', [App\Http\Controllers\MemberController::class, 'edit'])->name('members.edit');
            Route::put('members/{member}', [App\Http\Controllers\MemberController::class, 'update'])->name('members.update');
            Route::delete('members/{member}', [App\Http\Controllers\MemberController::class, 'destroy'])->name('members.destroy');
            Route::get('members/{member}/transactions', [App\Http\Controllers\MemberController::class, 'transactions'])->name('members.transactions');
    
    // Accounts
    Route::get('accounts', [App\Http\Controllers\AccountController::class, 'index'])->name('accounts.index');
    Route::get('accounts/create', [App\Http\Controllers\AccountController::class, 'create'])->name('accounts.create');
    Route::post('accounts', [App\Http\Controllers\AccountController::class, 'store'])->name('accounts.store');
    Route::get('accounts/{account}', [App\Http\Controllers\AccountController::class, 'show'])->name('accounts.show');
    Route::put('accounts/{account}/status', [App\Http\Controllers\AccountController::class, 'updateStatus'])->name('accounts.status');
    Route::delete('accounts/{account}', [App\Http\Controllers\AccountController::class, 'destroy'])->name('accounts.destroy');
    Route::get('accounts/{account}/statement', [App\Http\Controllers\AccountController::class, 'statement'])->name('accounts.statement');
    Route::post('accounts/{account}/close-request', [App\Http\Controllers\AccountController::class, 'closeRequest'])->name('accounts.close-request');
    
    // Transactions
    Route::get('transactions', [App\Http\Controllers\TransactionController::class, 'index'])->name('transactions.index');
    Route::get('transactions/create', [App\Http\Controllers\TransactionController::class, 'create'])->name('transactions.create');
    Route::post('transactions', [App\Http\Controllers\TransactionController::class, 'store'])->name('transactions.store');
    Route::get('transactions/{transaction}', [App\Http\Controllers\TransactionController::class, 'show'])->name('transactions.show');
    Route::get('transactions/{transaction}/receipt', [App\Http\Controllers\TransactionController::class, 'receipt'])->name('transactions.receipt');
    Route::post('transactions/{transaction}/approve', [App\Http\Controllers\TransactionController::class, 'approve'])->name('transactions.approve');
    Route::post('transactions/{transaction}/reject', [App\Http\Controllers\TransactionController::class, 'reject'])->name('transactions.reject');
    
    // Loans
    Route::get('loans', [App\Http\Controllers\LoanController::class, 'index'])->name('loans.index');
    Route::get('loans/create', [App\Http\Controllers\LoanController::class, 'create'])->name('loans.create');
    Route::post('loans', [App\Http\Controllers\LoanController::class, 'store'])->name('loans.store');
    Route::get('loans/{loan}', [App\Http\Controllers\LoanController::class, 'show'])->name('loans.show');
    Route::get('loans/{loan}/edit', [App\Http\Controllers\LoanController::class, 'edit'])->name('loans.edit');
    Route::put('loans/{loan}', [App\Http\Controllers\LoanController::class, 'update'])->name('loans.update');
    Route::post('loans/{loan}/approve', [App\Http\Controllers\LoanController::class, 'approve'])->name('loans.approve');
    Route::post('loans/{loan}/reject', [App\Http\Controllers\LoanController::class, 'reject'])->name('loans.reject');
    Route::post('loans/{loan}/disburse', [App\Http\Controllers\LoanController::class, 'disburse'])->name('loans.disburse');
    
    // Loan Accounts
    Route::get('loan-accounts', [App\Http\Controllers\LoanAccountController::class, 'index'])->name('loan-accounts.index');
    Route::get('loan-accounts/{loanAccount}', [App\Http\Controllers\LoanAccountController::class, 'show'])->name('loan-accounts.show');
    
    // Savings
    Route::get('savings', [App\Http\Controllers\SavingsController::class, 'index'])->name('savings.index');
    Route::get('savings/my', [App\Http\Controllers\SavingsController::class, 'my'])->name('savings.my');
    Route::get('savings/goals', [App\Http\Controllers\SavingsController::class, 'goals'])->name('savings.goals');
    Route::get('savings/budget', [App\Http\Controllers\SavingsController::class, 'budget'])->name('savings.budget');
    Route::get('savings/goals/create', [App\Http\Controllers\SavingsController::class, 'createGoal'])->name('savings.goals.create');
    Route::post('savings/goals', [App\Http\Controllers\SavingsController::class, 'storeGoal'])->name('savings.goals.store');
    Route::get('savings/goals/{goal}', [App\Http\Controllers\SavingsController::class, 'showGoal'])->name('savings.goals.show');
    Route::put('savings/goals/{goal}', [App\Http\Controllers\SavingsController::class, 'updateGoal'])->name('savings.goals.update');
    Route::delete('savings/goals/{goal}', [App\Http\Controllers\SavingsController::class, 'destroyGoal'])->name('savings.goals.destroy');
    Route::post('savings/goals/{goal}/contribute', [App\Http\Controllers\SavingsController::class, 'contributeToGoal'])->name('savings.goals.contribute');
    Route::get('savings/budget/create', [App\Http\Controllers\SavingsController::class, 'createBudget'])->name('savings.budget.create');
    Route::post('savings/budget', [App\Http\Controllers\SavingsController::class, 'storeBudget'])->name('savings.budget.store');
    Route::get('savings/budget/{budget}', [App\Http\Controllers\SavingsController::class, 'showBudget'])->name('savings.budget.show');
    Route::put('savings/budget/{budget}', [App\Http\Controllers\SavingsController::class, 'updateBudget'])->name('savings.budget.update');
    
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

    Route::get('reports/loans', function () {
        return Inertia::render('reports/loans');
    })->name('reports.loans');
    
    // Branches
    Route::get('branches', function () {
        return Inertia::render('branches/index');
    })->name('branches.index');
    
    Route::get('branches/create', function () {
        return Inertia::render('branches/create');
    })->name('branches.create');
    
    Route::get('branches/{branch}', function () {
        return Inertia::render('branches/show');
    })->name('branches.show');
    
    Route::get('branches/{branch}/edit', function () {
        return Inertia::render('branches/edit');
    })->name('branches.edit');
    
    // Notifications
    Route::get('notifications', [App\Http\Controllers\NotificationsController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{notification}/read', [App\Http\Controllers\NotificationsController::class, 'markAsRead'])->name('notifications.read');
    Route::post('notifications/{notification}/unread', [App\Http\Controllers\NotificationsController::class, 'markAsUnread'])->name('notifications.unread');
    Route::post('notifications/mark-all-read', [App\Http\Controllers\NotificationsController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('notifications/{notification}', [App\Http\Controllers\NotificationsController::class, 'destroy'])->name('notifications.destroy');
    
    // API routes for notifications
    Route::get('api/notifications/unread-count', [App\Http\Controllers\NotificationsController::class, 'unreadCount'])->name('api.notifications.unread-count');
    Route::get('api/notifications/recent', [App\Http\Controllers\NotificationsController::class, 'recent'])->name('api.notifications.recent');
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
        Route::get('/transactions/{transaction}/status', [TransactionController::class, 'getStatus'])->name('api.transactions.status');
    });
});

// Webhook routes (no authentication required)
Route::prefix('webhooks')->name('webhooks.')->group(function () {
    Route::post('/mpesa/callback', [MobileMoneyWebhookController::class, 'mpesaCallback'])->name('mpesa.callback');
});

// Test webhook endpoints for development
Route::prefix('test-webhooks')->name('test-webhooks.')->group(function () {
    Route::post('/mpesa/{transaction}', function (Request $request, $transactionId) {
        $transaction = \App\Models\Transaction::find($transactionId);
        if (!$transaction || $transaction->payment_method !== 'mpesa') {
            return response()->json(['error' => 'Transaction not found or not M-Pesa'], 404);
        }

        // Simulate successful M-Pesa webhook
        $webhookData = [
            'Body' => [
                'stkCallback' => [
                    'ResultCode' => 0,
                    'ResultDesc' => 'The service request is processed successfully.',
                    'CheckoutRequestID' => $transaction->metadata['checkout_request_id'] ?? 'test_checkout_id',
                    'CallbackMetadata' => [
                        'Item' => [
                            ['Name' => 'Amount', 'Value' => $transaction->amount],
                            ['Name' => 'MpesaReceiptNumber', 'Value' => 'TEST' . time()],
                            ['Name' => 'PhoneNumber', 'Value' => $transaction->phone_number],
                        ]
                    ]
                ]
            ]
        ];

        $mobileMoneyService = app(\App\Services\MobileMoneyService::class);
        $success = $mobileMoneyService->processPaymentConfirmation('mpesa', $webhookData);

        return response()->json([
            'ResultCode' => $success ? 0 : 1,
            'ResultDesc' => $success ? 'Success' : 'Failed',
            'message' => $success ? 'Payment confirmed successfully' : 'Payment confirmation failed'
        ]);
    })->name('mpesa.test');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';