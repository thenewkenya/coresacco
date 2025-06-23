<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>SACCO Core - Modern Banking Management System</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
        </style>
    </head>
    <body class="antialiased bg-gray-50 dark:bg-gray-900">
        <!-- Navigation -->
        <nav class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 flex items-center">
                            <svg class="h-8 w-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"/>
                            </svg>
                            <span class="ml-2 text-xl font-bold text-gray-900 dark:text-white">SACCO Core</span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                    Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 text-sm font-medium">
                                    Log in
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                        Get Started
                                    </a>
                                @endif
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="relative bg-white dark:bg-gray-900 overflow-hidden">
            <div class="max-w-7xl mx-auto">
                <div class="relative z-10 pb-8 bg-white dark:bg-gray-900 sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
                    <svg class="hidden lg:block absolute right-0 inset-y-0 h-full w-48 text-white dark:text-gray-900 transform translate-x-1/2" fill="currentColor" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
                        <polygon points="50,0 100,0 50,100 0,100" />
                    </svg>

                    <div class="pt-10 mx-auto max-w-7xl px-4 sm:pt-12 sm:px-6 md:pt-16 lg:pt-20 lg:px-8 xl:pt-28">
                        <div class="sm:text-center lg:text-left">
                            <h1 class="text-4xl tracking-tight font-bold text-gray-900 dark:text-white sm:text-5xl md:text-6xl">
                                <span class="block xl:inline">Modern Banking for</span>
                                <span class="block text-blue-600 xl:inline">SACCO Organizations</span>
                            </h1>
                            <p class="mt-3 text-base text-gray-500 dark:text-gray-400 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                                Comprehensive management system for Savings and Credit Cooperative Organizations. Handle members, accounts, loans, and transactions with enterprise-grade security and real-time processing.
                            </p>
                            <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                                <div class="rounded-md shadow">
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 md:py-4 md:text-lg md:px-10 transition-colors">
                                            Start Free Trial
                                        </a>
                                    @endif
                                </div>
                                <div class="mt-3 sm:mt-0 sm:ml-3">
                                    <a href="#features" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 md:py-4 md:text-lg md:px-10 transition-colors">
                                        Learn More
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2">
                <div class="h-56 w-full bg-gradient-to-br from-blue-600 via-purple-600 to-green-600 sm:h-72 md:h-96 lg:w-full lg:h-full flex items-center justify-center">
                    <div class="text-center text-white p-8">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div class="bg-white/20 rounded-lg p-4">
                                <div class="text-2xl font-bold">KES 5.5M+</div>
                                <div class="text-xs opacity-90">Assets Managed</div>
                            </div>
                            <div class="bg-white/20 rounded-lg p-4">
                                <div class="text-2xl font-bold">800+</div>
                                <div class="text-xs opacity-90">Transactions</div>
                            </div>
                            <div class="bg-white/20 rounded-lg p-4">
                                <div class="text-2xl font-bold">23</div>
                                <div class="text-xs opacity-90">Active Users</div>
                            </div>
                            <div class="bg-white/20 rounded-lg p-4">
                                <div class="text-2xl font-bold">3</div>
                                <div class="text-xs opacity-90">Branch Locations</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Overview -->
        <div id="features" class="py-12 bg-gray-50 dark:bg-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="lg:text-center">
                    <h2 class="text-base text-blue-600 font-semibold tracking-wide uppercase">Complete Solution</h2>
                    <p class="mt-2 text-3xl leading-8 font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                        Everything Your SACCO Needs
                    </p>
                    <p class="mt-4 max-w-2xl text-xl text-gray-500 dark:text-gray-400 lg:mx-auto">
                        From member onboarding to financial reporting, we've got every aspect of your SACCO covered with enterprise-grade tools.
                    </p>
                </div>

                <div class="mt-16">
                    <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-4">
                        <!-- Core Features Grid -->
                        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Member Management</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">23 active members across 3 branches with complete profile management.</p>
                        </div>

                        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                            <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Real-time Transactions</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">800+ transactions processed with instant balance updates and notifications.</p>
                        </div>

                        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                            <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Loan Management</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Complete loan lifecycle from application to disbursement and repayment.</p>
                        </div>

                        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                            <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Financial Analytics</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">KES 5.5M+ assets under management with comprehensive reporting.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Features Section -->
        <div class="py-16 bg-white dark:bg-gray-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Member Management Deep Dive -->
                <div class="mb-20">
                    <div class="lg:grid lg:grid-cols-2 lg:gap-8 lg:items-center">
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900 dark:text-white sm:text-4xl">
                                Complete Member Lifecycle Management
                            </h2>
                            <p class="mt-3 max-w-3xl text-lg text-gray-500 dark:text-gray-400">
                                From onboarding to account management, handle every aspect of member relationships with powerful tools designed for SACCO operations.
                            </p>
                            <div class="mt-8 space-y-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="flex items-center justify-center h-6 w-6 rounded-full bg-green-500 text-white">
                                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Digital Member Registration</h3>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Streamlined onboarding with document upload, verification, and automatic account creation.</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="flex items-center justify-center h-6 w-6 rounded-full bg-green-500 text-white">
                                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Multi-Branch Support</h3>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage members across multiple branches with centralized data and local access controls.</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="flex items-center justify-center h-6 w-6 rounded-full bg-green-500 text-white">
                                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Member Self-Service Portal</h3>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Empower members with 24/7 access to their accounts, statements, and service requests.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-10 lg:mt-0">
                            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg p-8 text-white">
                                <div class="text-center">
                                    <h3 class="text-2xl font-bold mb-4">Member Dashboard Preview</h3>
                                    <div class="bg-white/10 rounded-lg p-4 mb-4">
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="text-sm opacity-90">Account Balance</span>
                                            <span class="font-bold">KES 125,450</span>
                                        </div>
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="text-sm opacity-90">Shares</span>
                                            <span class="font-bold">KES 45,000</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm opacity-90">Active Loans</span>
                                            <span class="font-bold">2</span>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <button class="bg-white/20 hover:bg-white/30 px-3 py-2 rounded text-sm transition-colors">Deposit</button>
                                        <button class="bg-white/20 hover:bg-white/30 px-3 py-2 rounded text-sm transition-colors">Withdraw</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transaction Processing Deep Dive -->
                <div class="mb-20">
                    <div class="lg:grid lg:grid-cols-2 lg:gap-8 lg:items-center">
                        <div class="order-2 lg:order-1">
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-8">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Transaction Processing Engine</h3>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-700 rounded-lg">
                                        <div class="flex items-center">
                                            <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">Deposit</span>
                                        </div>
                                        <span class="text-sm text-green-600">+KES 15,000</span>
                                    </div>
                                    <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-700 rounded-lg">
                                        <div class="flex items-center">
                                            <div class="w-2 h-2 bg-blue-500 rounded-full mr-3"></div>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">Transfer</span>
                                        </div>
                                        <span class="text-sm text-blue-600">KES 5,000</span>
                                    </div>
                                    <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-700 rounded-lg">
                                        <div class="flex items-center">
                                            <div class="w-2 h-2 bg-orange-500 rounded-full mr-3"></div>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">Withdrawal</span>
                                        </div>
                                        <span class="text-sm text-orange-600">-KES 8,500</span>
                                    </div>
                                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-500 dark:text-gray-400">Today's Volume</span>
                                            <span class="font-medium text-gray-900 dark:text-white">47 transactions</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="order-1 lg:order-2">
                            <h2 class="text-3xl font-bold text-gray-900 dark:text-white sm:text-4xl">
                                Real-time Transaction Processing
                            </h2>
                            <p class="mt-3 max-w-3xl text-lg text-gray-500 dark:text-gray-400">
                                Process transactions instantly with multi-level approval workflows, automated notifications, and comprehensive audit trails.
                            </p>
                            <div class="mt-8 space-y-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="flex items-center justify-center h-6 w-6 rounded-full bg-green-500 text-white">
                                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Instant Balance Updates</h3>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Real-time balance calculations with automatic reconciliation and error detection.</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="flex items-center justify-center h-6 w-6 rounded-full bg-green-500 text-white">
                                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Approval Workflows</h3>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Configurable approval chains for high-value transactions with automatic routing.</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="flex items-center justify-center h-6 w-6 rounded-full bg-green-500 text-white">
                                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Smart Notifications</h3>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Automated SMS and email notifications for large deposits, withdrawals, and suspicious activities.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Loan Management Deep Dive -->
                <div class="mb-20">
                    <div class="lg:grid lg:grid-cols-2 lg:gap-8 lg:items-center">
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900 dark:text-white sm:text-4xl">
                                Advanced Loan Management
                            </h2>
                            <p class="mt-3 max-w-3xl text-lg text-gray-500 dark:text-gray-400">
                                Complete loan lifecycle management with automated calculations, flexible repayment schedules, and intelligent risk assessment.
                            </p>
                            <div class="mt-8 space-y-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="flex items-center justify-center h-6 w-6 rounded-full bg-green-500 text-white">
                                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Multiple Loan Products</h3>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Support for development loans, emergency loans, asset financing, and custom loan products.</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="flex items-center justify-center h-6 w-6 rounded-full bg-green-500 text-white">
                                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Automated Calculations</h3>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Interest calculations, penalty charges, and payment schedules updated automatically.</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="flex items-center justify-center h-6 w-6 rounded-full bg-green-500 text-white">
                                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Digital Loan Applications</h3>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Online applications with document upload, creditworthiness scoring, and approval workflows.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-10 lg:mt-0">
                            <div class="bg-gradient-to-r from-purple-500 to-pink-600 rounded-lg p-8 text-white">
                                <div class="text-center">
                                    <h3 class="text-2xl font-bold mb-4">Loan Portfolio Overview</h3>
                                    <div class="grid grid-cols-2 gap-4 mb-6">
                                        <div class="bg-white/10 rounded-lg p-3">
                                            <div class="text-2xl font-bold">12</div>
                                            <div class="text-sm opacity-90">Active Loans</div>
                                        </div>
                                        <div class="bg-white/10 rounded-lg p-3">
                                            <div class="text-2xl font-bold">98.5%</div>
                                            <div class="text-sm opacity-90">Recovery Rate</div>
                                        </div>
                                        <div class="bg-white/10 rounded-lg p-3">
                                            <div class="text-2xl font-bold">KES 2.1M</div>
                                            <div class="text-sm opacity-90">Outstanding</div>
                                        </div>
                                        <div class="bg-white/10 rounded-lg p-3">
                                            <div class="text-2xl font-bold">5</div>
                                            <div class="text-sm opacity-90">Applications</div>
                                        </div>
                                    </div>
                                    <button class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded transition-colors">
                                        View Loan Dashboard
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security & Analytics Section -->
        <div class="py-16 bg-gray-50 dark:bg-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="lg:text-center mb-16">
                    <h2 class="text-base text-blue-600 font-semibold tracking-wide uppercase">Security & Intelligence</h2>
                    <p class="mt-2 text-3xl leading-8 font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                        Enterprise-Grade Security & Analytics
                    </p>
                    <p class="mt-4 max-w-2xl text-xl text-gray-500 dark:text-gray-400 lg:mx-auto">
                        Advanced security controls and intelligent analytics to protect your SACCO and drive data-driven decisions.
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
                    <!-- Security Features -->
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Advanced Security Controls</h3>
                        <div class="space-y-6">
                            <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-sm">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="font-semibold text-gray-900 dark:text-white">Role-Based Access Control</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Four-tier permission system (Admin, Manager, Staff, Member) with granular controls over 50+ operations.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-sm">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="font-semibold text-gray-900 dark:text-white">Audit Trail System</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Complete transaction logging with user tracking, IP addresses, and timestamp records for compliance.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-sm">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5-5-5h5v-6h4v6zM1 1h12v12H1V1z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="font-semibold text-gray-900 dark:text-white">Fraud Detection</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Automated alerts for large deposits (>KES 100K), suspicious patterns, and policy violations.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Analytics Features -->
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Intelligent Analytics</h3>
                        <div class="bg-white dark:bg-gray-900 rounded-lg p-6 shadow-sm">
                            <div class="mb-6">
                                <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Real-time Dashboard</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-gradient-to-r from-green-400 to-green-500 p-4 rounded-lg text-white">
                                        <div class="text-2xl font-bold">KES 5.5M</div>
                                        <div class="text-sm opacity-90">Total Assets</div>
                                    </div>
                                    <div class="bg-gradient-to-r from-blue-400 to-blue-500 p-4 rounded-lg text-white">
                                        <div class="text-2xl font-bold">847</div>
                                        <div class="text-sm opacity-90">Transactions</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">Loan Portfolio Health</span>
                                    <span class="text-sm text-green-600">98.5% Recovery</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: 98.5%"></div>
                                </div>
                                
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">Member Growth</span>
                                    <span class="text-sm text-blue-600">+15% YoY</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: 75%"></div>
                                </div>
                                
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">Savings Target</span>
                                    <span class="text-sm text-purple-600">87% Complete</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-purple-500 h-2 rounded-full" style="width: 87%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Features Showcase -->
        <div class="py-16 bg-white dark:bg-gray-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white sm:text-4xl">
                        Comprehensive Feature Set
                    </h2>
                    <p class="mt-4 text-lg text-gray-500 dark:text-gray-400">
                        Every tool you need to run a successful SACCO operation
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Financial Planning -->
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-8 hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Financial Planning Tools</h3>
                        <ul class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                            <li class="flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                Personal & Business Goals Setting
                            </li>
                            <li class="flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                Budget Planning & Expense Tracking
                            </li>
                            <li class="flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                Savings Progress Monitoring
                            </li>
                            <li class="flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                Investment Planning Tools
                            </li>
                        </ul>
                    </div>

                    <!-- Notification System -->
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-8 hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5-5-5h5v-6h4v6zM1 1h12v12H1V1z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Smart Notifications</h3>
                        <ul class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                            <li class="flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                Real-time Transaction Alerts
                            </li>
                            <li class="flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                Loan Payment Reminders
                            </li>
                            <li class="flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                Large Deposit Notifications
                            </li>
                            <li class="flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                System & Security Alerts
                            </li>
                        </ul>
                    </div>

                    <!-- Reporting & Compliance -->
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-8 hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Comprehensive Reporting</h3>
                        <ul class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                            <li class="flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                Financial Statements & Balance Sheets
                            </li>
                            <li class="flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                Loan Portfolio Analysis
                            </li>
                            <li class="flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                Member Activity & Trends
                            </li>
                            <li class="flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                Regulatory Compliance Reports
                            </li>
                        </ul>
                    </div>

                    <!-- Insurance Management -->
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-8 hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Insurance Management</h3>
                        <ul class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                            <li class="flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                Member Insurance Registration
                            </li>
                            <li class="flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                Premium Collection Tracking
                            </li>
                            <li class="flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                Claims Processing Workflows
                            </li>
                            <li class="flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                Coverage Status Monitoring
                            </li>
                        </ul>
                    </div>

                    <!-- Multi-Branch Operations -->
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-8 hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Multi-Branch Operations</h3>
                        <ul class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                            <li class="flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                Centralized Member Database
                            </li>
                            <li class="flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                Branch-Specific Reporting
                            </li>
                            <li class="flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                Inter-Branch Transfers
                            </li>
                            <li class="flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                Distributed User Management
                            </li>
                        </ul>
                    </div>

                    <!-- API & Integration -->
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-8 hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">API & Integration</h3>
                        <ul class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                            <li class="flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                RESTful API with Sanctum
                            </li>
                            <li class="flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                Third-Party Payment Gateways
                            </li>
                            <li class="flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                Mobile App Integration Ready
                            </li>
                            <li class="flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                Webhook Support for Events
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Roles Section -->
        <div class="bg-white dark:bg-gray-900">
            <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:py-20 lg:px-8">
                <div class="lg:text-center">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white sm:text-4xl">
                        Built for Every User Type
                    </h2>
                    <p class="mt-4 text-lg text-gray-500 dark:text-gray-400">
                        Tailored experiences for different roles in your SACCO organization
                    </p>
                </div>
                <div class="mt-12 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- Admin -->
                    <div class="pt-6">
                        <div class="flow-root bg-gray-50 dark:bg-gray-800 rounded-lg px-6 pb-8">
                            <div class="-mt-6">
                                <div>
                                    <span class="inline-flex items-center justify-center p-3 bg-gradient-to-r from-blue-500 to-blue-600 rounded-md shadow-lg">
                                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </span>
                                </div>
                                <h3 class="mt-8 text-lg font-medium text-gray-900 dark:text-white tracking-tight">Admin</h3>
                                <p class="mt-5 text-base text-gray-500 dark:text-gray-400">
                                    Full system access, user management, system configuration, and complete oversight.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Manager -->
                    <div class="pt-6">
                        <div class="flow-root bg-gray-50 dark:bg-gray-800 rounded-lg px-6 pb-8">
                            <div class="-mt-6">
                                <div>
                                    <span class="inline-flex items-center justify-center p-3 bg-gradient-to-r from-green-500 to-green-600 rounded-md shadow-lg">
                                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </span>
                                </div>
                                <h3 class="mt-8 text-lg font-medium text-gray-900 dark:text-white tracking-tight">Manager</h3>
                                <p class="mt-5 text-base text-gray-500 dark:text-gray-400">
                                    Branch management, transaction approvals, and advanced reporting capabilities.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Staff -->
                    <div class="pt-6">
                        <div class="flow-root bg-gray-50 dark:bg-gray-800 rounded-lg px-6 pb-8">
                            <div class="-mt-6">
                                <div>
                                    <span class="inline-flex items-center justify-center p-3 bg-gradient-to-r from-purple-500 to-purple-600 rounded-md shadow-lg">
                                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6m8 0V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m0 0h8m-8 0H8m0 0v2a2 2 0 002 2h4a2 2 0 002-2V8" />
                                        </svg>
                                    </span>
                                </div>
                                <h3 class="mt-8 text-lg font-medium text-gray-900 dark:text-white tracking-tight">Staff</h3>
                                <p class="mt-5 text-base text-gray-500 dark:text-gray-400">
                                    Daily operations, member services, account management, and loan processing.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Member -->
                    <div class="pt-6">
                        <div class="flow-root bg-gray-50 dark:bg-gray-800 rounded-lg px-6 pb-8">
                            <div class="-mt-6">
                                <div>
                                    <span class="inline-flex items-center justify-center p-3 bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-md shadow-lg">
                                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </span>
                                </div>
                                <h3 class="mt-8 text-lg font-medium text-gray-900 dark:text-white tracking-tight">Member</h3>
                                <p class="mt-5 text-base text-gray-500 dark:text-gray-400">
                                    Self-service portal to view accounts, loan status, transaction history, and financial goals.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Technical Features -->
        <div class="bg-gray-50 dark:bg-gray-800">
            <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:py-20 lg:px-8">
                <div class="lg:text-center">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white sm:text-4xl">
                        Enterprise-Grade Technology
                    </h2>
                    <p class="mt-4 text-lg text-gray-500 dark:text-gray-400">
                        Built with modern technologies for reliability, security, and performance
                    </p>
                </div>
                <div class="mt-12 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="bg-white dark:bg-gray-900 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 bg-blue-500 rounded-md flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">L</span>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Framework</dt>
                                        <dd class="text-lg font-medium text-gray-900 dark:text-white">Laravel 12.0</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-900 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 bg-green-500 rounded-md flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">API</span>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">RESTful API</dt>
                                        <dd class="text-lg font-medium text-gray-900 dark:text-white">Laravel Sanctum</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-900 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 bg-purple-500 rounded-md flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">LW</span>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Frontend</dt>
                                        <dd class="text-lg font-medium text-gray-900 dark:text-white">Livewire + Flux</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="bg-blue-600">
            <div class="max-w-2xl mx-auto text-center py-16 px-4 sm:py-20 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold text-white sm:text-4xl">
                    <span class="block">Ready to modernize your SACCO?</span>
                </h2>
                <p class="mt-4 text-lg leading-6 text-blue-200">
                    Join the future of cooperative banking with our comprehensive management system.
                </p>
                <div class="mt-8 flex justify-center">
                    @if (Route::has('register'))
                        <div class="inline-flex rounded-md shadow">
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-blue-600 bg-white hover:bg-gray-50 transition-colors">
                                Get Started Today
                            </a>
                        </div>
                    @endif
                    <div class="ml-3 inline-flex">
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-500 hover:bg-blue-600 transition-colors">
                                Demo Login
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-white dark:bg-gray-900">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 md:flex md:items-center md:justify-between lg:px-8">
                <div class="flex justify-center space-x-6 md:order-2">
                    <span class="text-gray-400 hover:text-gray-500">
                        Built with Laravel {{ app()->version() }}
                    </span>
                </div>
                <div class="mt-8 md:mt-0 md:order-1">
                    <p class="text-center text-base text-gray-400">
                        &copy; {{ date('Y') }} SACCO Core. Professional banking management system.
                    </p>
                </div>
            </div>
        </footer>
    </body>
</html>
