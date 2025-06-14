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

    // Core Operations
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', function () { return view('transactions.index'); })->name('index');
        Route::get('/my', function () { return view('transactions.my'); })->name('my');
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

    // Staff Tools
    Route::prefix('approvals')->name('approvals.')->group(function () {
        Route::get('/', function () { return view('approvals.index'); })->name('index');
    });

    Route::prefix('schedule')->name('schedule.')->group(function () {
        Route::get('/', function () { return view('schedule.index'); })->name('index');
    });
});

require __DIR__.'/auth.php';
