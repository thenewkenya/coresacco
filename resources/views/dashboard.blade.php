<x-layouts.app>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-2">Welcome, {{ auth()->user()->name }}!</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Your roles: 
                            @foreach(auth()->user()->roles as $role)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 mr-2">
                                    {{ $role->name }}
                                </span>
                            @endforeach
                        </p>
                    </div>

                    <!-- Quick Actions Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        @can('view-members')
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-lg">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-lg font-medium text-blue-900 dark:text-blue-100">Members</h4>
                                    <p class="text-sm text-blue-600 dark:text-blue-300">Manage SACCO members</p>
                                </div>
                            </div>
                        </div>
                        @endcan

                        @can('view-accounts')
                        <div class="bg-green-50 dark:bg-green-900/20 p-6 rounded-lg">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-lg font-medium text-green-900 dark:text-green-100">Accounts</h4>
                                    <p class="text-sm text-green-600 dark:text-green-300">Manage member accounts</p>
                                </div>
                            </div>
                        </div>
                        @endcan

                        @can('view-loans')
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 p-6 rounded-lg">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-lg font-medium text-yellow-900 dark:text-yellow-100">Loans</h4>
                                    <p class="text-sm text-yellow-600 dark:text-yellow-300">Manage loan applications</p>
                                </div>
                            </div>
                        </div>
                        @endcan

                        @can('view-reports')
                        <div class="bg-purple-50 dark:bg-purple-900/20 p-6 rounded-lg">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 00-2-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 00-2 2"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-lg font-medium text-purple-900 dark:text-purple-100">Reports</h4>
                                    <p class="text-sm text-purple-600 dark:text-purple-300">View system reports</p>
                                </div>
                            </div>
                        </div>
                        @endcan
                    </div>

                    <!-- Admin Only Section -->
                    @role('admin')
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h4 class="text-lg font-medium text-red-600 dark:text-red-400 mb-4">
                            Administrator Panel
                        </h4>
                        <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4">
                            <p class="text-sm text-red-800 dark:text-red-200">
                                You have administrative privileges. Handle with care!
                            </p>
                            <div class="mt-4 space-x-4">
                                @can('manage-roles')
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    Manage Roles
                                </button>
                                @endcan
                                
                                @can('manage-settings')
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    System Settings
                                </button>
                                @endcan
                            </div>
                        </div>
                    </div>
                    @endrole

                    <!-- Staff/Manager Section -->
                    @roleany('staff', 'manager')
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h4 class="text-lg font-medium text-blue-600 dark:text-blue-400 mb-4">
                            Staff Operations
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @can('process-transactions')
                            <button class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg text-center hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                                <div class="text-blue-600 dark:text-blue-400 font-medium">Process Transactions</div>
                                <div class="text-sm text-blue-500 dark:text-blue-300">Handle deposits & withdrawals</div>
                            </button>
                            @endcan

                            @can('approve-loans') 
                            <button class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg text-center hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
                                <div class="text-green-600 dark:text-green-400 font-medium">Approve Loans</div>
                                <div class="text-sm text-green-500 dark:text-green-300">Review loan applications</div>
                            </button>
                            @endcan

                            @can('disburse-loans')
                            <button class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg text-center hover:bg-yellow-100 dark:hover:bg-yellow-900/30 transition-colors">
                                <div class="text-yellow-600 dark:text-yellow-400 font-medium">Disburse Loans</div>
                                <div class="text-sm text-yellow-500 dark:text-yellow-300">Release approved funds</div>
                            </button>
                            @endcan
                        </div>
                    </div>
                    @endroleany

                    <!-- Member Only Section -->
                    @role('member')
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h4 class="text-lg font-medium text-green-600 dark:text-green-400 mb-4">
                            My SACCO Services
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                <h5 class="font-medium text-green-800 dark:text-green-200">My Accounts</h5>
                                <p class="text-sm text-green-600 dark:text-green-300">View your savings and current accounts</p>
                            </div>
                            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                <h5 class="font-medium text-blue-800 dark:text-blue-200">My Loans</h5>
                                <p class="text-sm text-blue-600 dark:text-blue-300">View your loan status and history</p>
                            </div>
                        </div>
                    </div>
                    @endrole
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
