<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\LoanController;
use App\Http\Controllers\LoanApplicationController;
use App\Models\Transaction;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // Member routes
    Route::middleware('permission:view-members')->group(function () {
        Route::get('members', [MemberController::class, 'index']);
        Route::get('members/{member}', [MemberController::class, 'show']);
        Route::get('members/{member}/accounts', [MemberController::class, 'accounts']);
        Route::get('members/{member}/loans', [MemberController::class, 'loans']);
        Route::get('members/{member}/transactions', [MemberController::class, 'transactions']);
    });
    
    Route::middleware('permission:create-members')->group(function () {
        Route::post('members', [MemberController::class, 'store']);
    });
    
    Route::middleware('permission:edit-members')->group(function () {
        Route::put('members/{member}', [MemberController::class, 'update']);
        Route::patch('members/{member}', [MemberController::class, 'update']);
    });
    
    Route::middleware('permission:delete-members')->group(function () {
        Route::delete('members/{member}', [MemberController::class, 'destroy']);
    });

    // Account routes
    Route::middleware('permission:view-accounts')->group(function () {
        Route::get('accounts', [AccountController::class, 'index']);
        Route::get('accounts/{account}', [AccountController::class, 'show']);
        Route::get('accounts/{account}/statement', [AccountController::class, 'statement']);
    });
    
    Route::middleware('permission:create-accounts')->group(function () {
        Route::post('accounts', [AccountController::class, 'store']);
    });
    
    Route::middleware('permission:edit-accounts')->group(function () {
        Route::put('accounts/{account}', [AccountController::class, 'update']);
        Route::patch('accounts/{account}', [AccountController::class, 'update']);
    });
    
    Route::delete('accounts/{account}', [AccountController::class, 'destroy'])
        ->middleware('permission:delete-accounts');
    
    Route::middleware('permission:process-transactions')->group(function () {
        Route::post('accounts/{account}/deposit', [AccountController::class, 'deposit']);
        Route::post('accounts/{account}/withdraw', [AccountController::class, 'withdraw']);
    });

    // Loan routes
    Route::middleware('permission:view-loans')->group(function () {
        Route::get('loans', [LoanController::class, 'index']);
        Route::get('loans/{loan}', [LoanController::class, 'show']);
        Route::get('loans/{loan}/schedule', [LoanController::class, 'schedule']);
    });
    
    Route::middleware('permission:create-loans')->group(function () {
        Route::post('loans', [LoanController::class, 'store']);
    });
    
    Route::middleware('permission:edit-loans')->group(function () {
        Route::put('loans/{loan}', [LoanController::class, 'update']);
        Route::patch('loans/{loan}', [LoanController::class, 'update']);
    });
    
    Route::delete('loans/{loan}', [LoanController::class, 'destroy'])
        ->middleware('permission:delete-loans');
    
    Route::middleware('permission:approve-loans')->group(function () {
        Route::post('loans/{loan}/approve', [LoanController::class, 'approve']);
        Route::post('loans/{loan}/reject', [LoanController::class, 'reject']);
    });
    
    Route::middleware('permission:disburse-loans')->group(function () {
        Route::post('loans/{loan}/disburse', [LoanController::class, 'disburse']);
    });
    
    Route::post('loans/{loan}/repay', [LoanController::class, 'repay'])
        ->middleware('permission:process-transactions');

    // Loan Application routes with borrowing criteria and guarantors
    Route::middleware('permission:view-loans')->group(function () {
        Route::get('loan-applications', [LoanApplicationController::class, 'index']);
        Route::get('loan-applications/{loan}', [LoanApplicationController::class, 'show']);
        Route::get('loan-applications/{loan}/eligibility', [LoanApplicationController::class, 'getEligibilityReport']);
        Route::get('members/{member}/eligibility', [LoanApplicationController::class, 'checkMemberEligibility']);
        Route::get('members/{member}/available-guarantors', [LoanApplicationController::class, 'getAvailableGuarantors']);
    });
    
    Route::middleware('permission:create-loans')->group(function () {
        Route::post('loan-applications', [LoanApplicationController::class, 'store']);
    });
    
    Route::middleware('permission:approve-loans')->group(function () {
        Route::post('loan-applications/{loan}/approve', [LoanApplicationController::class, 'approveLoan']);
        Route::post('loan-applications/{loan}/reject', [LoanApplicationController::class, 'rejectLoan']);
        Route::post('loan-applications/{loan}/guarantors/{guarantor}/approve', [LoanApplicationController::class, 'approveGuarantor']);
        Route::post('loan-applications/{loan}/guarantors/{guarantor}/reject', [LoanApplicationController::class, 'rejectGuarantor']);
    });
        
    // Payment status endpoint for mobile money polling
    Route::get('transactions/{transaction}/status', function (Transaction $transaction) {
        // Check if user owns this transaction or has permission to view it
        $user = auth()->user();
        if ($user->hasRole('member') && $transaction->member_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        return response()->json([
            'id' => $transaction->id,
            'status' => $transaction->status,
            'amount' => $transaction->amount,
            'description' => $transaction->description,
            'metadata' => $transaction->metadata,
            'created_at' => $transaction->created_at,
            'updated_at' => $transaction->updated_at,
        ]);
    });
});