<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\LoanController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // Member routes
    Route::apiResource('members', MemberController::class);
    Route::get('members/{member}/accounts', [MemberController::class, 'accounts']);
    Route::get('members/{member}/loans', [MemberController::class, 'loans']);
    Route::get('members/{member}/transactions', [MemberController::class, 'transactions']);

    // Account routes
    Route::apiResource('accounts', AccountController::class);
    Route::post('accounts/{account}/deposit', [AccountController::class, 'deposit']);
    Route::post('accounts/{account}/withdraw', [AccountController::class, 'withdraw']);
    Route::get('accounts/{account}/statement', [AccountController::class, 'statement']);

    // Loan routes
    Route::apiResource('loans', LoanController::class);
    Route::post('loans/{loan}/approve', [LoanController::class, 'approve']);
    Route::post('loans/{loan}/reject', [LoanController::class, 'reject']);
    Route::post('loans/{loan}/disburse', [LoanController::class, 'disburse']);
    Route::post('loans/{loan}/repay', [LoanController::class, 'repay']);
    Route::get('loans/{loan}/schedule', [LoanController::class, 'schedule']);
});