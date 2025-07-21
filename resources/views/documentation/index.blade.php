<x-layouts.app>
    <div class="min-h-screen bg-gray-50 dark:bg-zinc-900">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-3xl font-bold text-zinc-900 dark:text-white mb-4">{{ __('How to Use eSacco') }}</h1>
                <p class="text-lg text-zinc-600 dark:text-zinc-400">{{ __('Step-by-step guides for managing your SACCO operations') }}</p>
            </div>

            <!-- Navigation -->
            <div class="mb-8">
                <nav class="flex flex-wrap justify-center gap-4" x-data="{ activeTab: 'getting-started' }">
                    <button @click="activeTab = 'getting-started'" 
                            :class="activeTab === 'getting-started' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-gray-50 dark:hover:bg-zinc-700'"
                            class="px-4 py-2 rounded-lg font-medium text-sm transition-colors border border-zinc-200 dark:border-zinc-700">
                        {{ __('Getting Started') }}
                    </button>
                    <button @click="activeTab = 'members'" 
                            :class="activeTab === 'members' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-gray-50 dark:hover:bg-zinc-700'"
                            class="px-4 py-2 rounded-lg font-medium text-sm transition-colors border border-zinc-200 dark:border-zinc-700">
                        {{ __('Managing Members') }}
                    </button>
                    <button @click="activeTab = 'transactions'" 
                            :class="activeTab === 'transactions' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-gray-50 dark:hover:bg-zinc-700'"
                            class="px-4 py-2 rounded-lg font-medium text-sm transition-colors border border-zinc-200 dark:border-zinc-700">
                        {{ __('Processing Transactions') }}
                    </button>
                    <button @click="activeTab = 'loans'" 
                            :class="activeTab === 'loans' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-gray-50 dark:hover:bg-zinc-700'"
                            class="px-4 py-2 rounded-lg font-medium text-sm transition-colors border border-zinc-200 dark:border-zinc-700">
                        {{ __('Managing Loans') }}
                    </button>
                    <button @click="activeTab = 'reports'" 
                            :class="activeTab === 'reports' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-gray-50 dark:hover:bg-zinc-700'"
                            class="px-4 py-2 rounded-lg font-medium text-sm transition-colors border border-zinc-200 dark:border-zinc-700">
                        {{ __('Reports & Analytics') }}
                    </button>
                </nav>
            </div>

            <div class="space-y-8">
                
                <!-- Getting Started Tab -->
                <div x-show="activeTab === 'getting-started'" x-transition>
                    <div class="space-y-6">
                        <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ __('Getting Started') }}</h2>
                        
                        <!-- Step 1 -->
                        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 border border-zinc-200 dark:border-zinc-700">
                            <div class="flex items-start space-x-4">
                                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-sm font-bold text-blue-600">1</span>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-2">{{ __('Set up your SACCO information') }}</h3>
                                    <p class="text-zinc-600 dark:text-zinc-400 mb-3">{{ __('Go to Settings and configure your SACCO name, address, and contact details.') }}</p>
                                    <ul class="text-sm text-zinc-600 dark:text-zinc-400 space-y-1">
                                        <li>• {{ __('Navigate to Settings > General') }}</li>
                                        <li>• {{ __('Enter your SACCO name and registration details') }}</li>
                                        <li>• {{ __('Set your default currency (KES, USD, etc.)') }}</li>
                                        <li>• {{ __('Configure email settings for notifications') }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 border border-zinc-200 dark:border-zinc-700">
                            <div class="flex items-start space-x-4">
                                <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-sm font-bold text-green-600">2</span>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-2">{{ __('Create staff accounts') }}</h3>
                                    <p class="text-zinc-600 dark:text-zinc-400 mb-3">{{ __('Add your staff members and assign them appropriate roles.') }}</p>
                                    <ul class="text-sm text-zinc-600 dark:text-zinc-400 space-y-1">
                                        <li>• {{ __('Go to Members > Add New Member') }}</li>
                                        <li>• {{ __('Select "Staff" role when creating accounts') }}</li>
                                        <li>• {{ __('Available roles: Admin, Manager, Staff, Member') }}</li>
                                        <li>• {{ __('Staff can process transactions, Managers can approve loans') }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 border border-zinc-200 dark:border-zinc-700">
                            <div class="flex items-start space-x-4">
                                <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-sm font-bold text-purple-600">3</span>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-2">{{ __('Start registering members') }}</h3>
                                    <p class="text-zinc-600 dark:text-zinc-400 mb-3">{{ __('Begin adding your SACCO members to the system.') }}</p>
                                    <ul class="text-sm text-zinc-600 dark:text-zinc-400 space-y-1">
                                        <li>• {{ __('Click "Add New Member" from the Members page') }}</li>
                                        <li>• {{ __('Fill in member details: name, ID number, phone, address') }}</li>
                                        <li>• {{ __('System automatically assigns member numbers') }}</li>
                                        <li>• {{ __('Members can open accounts after registration') }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Managing Members Tab -->
                <div x-show="activeTab === 'members'" x-transition>
                    <div class="space-y-6">
                        <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ __('Managing Members') }}</h2>
                        
                        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 border border-zinc-200 dark:border-zinc-700">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">{{ __('How to register a new member') }}</h3>
                            <ol class="space-y-3 text-zinc-600 dark:text-zinc-400">
                                <li class="flex items-start space-x-3">
                                    <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-600 text-xs font-bold px-2 py-1 rounded">1</span>
                                    <span>{{ __('Go to Members page and click "Add New Member"') }}</span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-600 text-xs font-bold px-2 py-1 rounded">2</span>
                                    <span>{{ __('Fill in required information: Full name, ID number, phone number') }}</span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-600 text-xs font-bold px-2 py-1 rounded">3</span>
                                    <span>{{ __('Add address and emergency contact details') }}</span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-600 text-xs font-bold px-2 py-1 rounded">4</span>
                                    <span>{{ __('Select member role (usually "Member" for regular SACCO members)') }}</span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-600 text-xs font-bold px-2 py-1 rounded">5</span>
                                    <span>{{ __('Click "Save" - the system assigns a unique member number') }}</span>
                                </li>
                            </ol>
                        </div>

                        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 border border-zinc-200 dark:border-zinc-700">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">{{ __('Opening accounts for members') }}</h3>
                            <p class="text-zinc-600 dark:text-zinc-400 mb-4">{{ __('After registering a member, you can open different types of accounts:') }}</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-gray-50 dark:bg-zinc-700/50 rounded-lg p-4">
                                    <h4 class="font-medium text-zinc-900 dark:text-white mb-2">{{ __('Savings Account') }}</h4>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('For regular deposits and withdrawals. Usually the first account opened for new members.') }}</p>
                                </div>
                                <div class="bg-gray-50 dark:bg-zinc-700/50 rounded-lg p-4">
                                    <h4 class="font-medium text-zinc-900 dark:text-white mb-2">{{ __('Shares Account') }}</h4>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('For member equity contributions. Required for loan eligibility in most SACCOs.') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 border border-zinc-200 dark:border-zinc-700">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">{{ __('Finding and updating member information') }}</h3>
                            <ul class="space-y-2 text-zinc-600 dark:text-zinc-400">
                                <li>• {{ __('Use the search bar to find members by name, member number, or phone') }}</li>
                                <li>• {{ __('Click on a member\'s name to view their full profile') }}</li>
                                <li>• {{ __('Use "Edit" button to update member details') }}</li>
                                <li>• {{ __('View all member accounts and transaction history from their profile') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Processing Transactions Tab -->
                <div x-show="activeTab === 'transactions'" x-transition>
                    <div class="space-y-6">
                        <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ __('Processing Transactions') }}</h2>
                        
                        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 border border-zinc-200 dark:border-zinc-700">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">{{ __('How to process a deposit') }}</h3>
                            <ol class="space-y-3 text-zinc-600 dark:text-zinc-400">
                                <li class="flex items-start space-x-3">
                                    <span class="bg-green-100 dark:bg-green-900/30 text-green-600 text-xs font-bold px-2 py-1 rounded">1</span>
                                    <span>{{ __('Go to Transactions > Create Deposit') }}</span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <span class="bg-green-100 dark:bg-green-900/30 text-green-600 text-xs font-bold px-2 py-1 rounded">2</span>
                                    <span>{{ __('Search for the member by name or member number') }}</span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <span class="bg-green-100 dark:bg-green-900/30 text-green-600 text-xs font-bold px-2 py-1 rounded">3</span>
                                    <span>{{ __('Select which account to deposit into (Savings, Shares, etc.)') }}</span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <span class="bg-green-100 dark:bg-green-900/30 text-green-600 text-xs font-bold px-2 py-1 rounded">4</span>
                                    <span>{{ __('Enter the deposit amount and add a description') }}</span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <span class="bg-green-100 dark:bg-green-900/30 text-green-600 text-xs font-bold px-2 py-1 rounded">5</span>
                                    <span>{{ __('Click "Process Deposit" - the account balance updates immediately') }}</span>
                                </li>
                            </ol>
                        </div>

                        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 border border-zinc-200 dark:border-zinc-700">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">{{ __('How to process a withdrawal') }}</h3>
                            <ol class="space-y-3 text-zinc-600 dark:text-zinc-400">
                                <li class="flex items-start space-x-3">
                                    <span class="bg-red-100 dark:bg-red-900/30 text-red-600 text-xs font-bold px-2 py-1 rounded">1</span>
                                    <span>{{ __('Go to Transactions > Create Withdrawal') }}</span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <span class="bg-red-100 dark:bg-red-900/30 text-red-600 text-xs font-bold px-2 py-1 rounded">2</span>
                                    <span>{{ __('Find the member and select their account') }}</span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <span class="bg-red-100 dark:bg-red-900/30 text-red-600 text-xs font-bold px-2 py-1 rounded">3</span>
                                    <span>{{ __('Enter withdrawal amount (system checks if sufficient balance exists)') }}</span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <span class="bg-red-100 dark:bg-red-900/30 text-red-600 text-xs font-bold px-2 py-1 rounded">4</span>
                                    <span>{{ __('Add reason for withdrawal and any notes') }}</span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <span class="bg-red-100 dark:bg-red-900/30 text-red-600 text-xs font-bold px-2 py-1 rounded">5</span>
                                    <span>{{ __('Click "Process Withdrawal" - balance is updated and receipt is generated') }}</span>
                                </li>
                            </ol>
                        </div>

                        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 border border-zinc-200 dark:border-zinc-700">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">{{ __('Printing receipts') }}</h3>
                            <p class="text-zinc-600 dark:text-zinc-400 mb-3">{{ __('Every transaction automatically generates a receipt that you can print or download:') }}</p>
                            <ul class="space-y-2 text-zinc-600 dark:text-zinc-400">
                                <li>• {{ __('After processing any transaction, click "Print Receipt"') }}</li>
                                <li>• {{ __('Receipts show transaction details, balances, and SACCO information') }}</li>
                                <li>• {{ __('You can also find receipts later in the transaction history') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Managing Loans Tab -->
                <div x-show="activeTab === 'loans'" x-transition>
                    <div class="space-y-6">
                        <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ __('Managing Loans') }}</h2>
                        
                        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 border border-zinc-200 dark:border-zinc-700">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">{{ __('Processing a loan application') }}</h3>
                            <ol class="space-y-3 text-zinc-600 dark:text-zinc-400">
                                <li class="flex items-start space-x-3">
                                    <span class="bg-purple-100 dark:bg-purple-900/30 text-purple-600 text-xs font-bold px-2 py-1 rounded">1</span>
                                    <span>{{ __('Go to Loans > Create New Loan') }}</span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <span class="bg-purple-100 dark:bg-purple-900/30 text-purple-600 text-xs font-bold px-2 py-1 rounded">2</span>
                                    <span>{{ __('Select the member applying for the loan') }}</span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <span class="bg-purple-100 dark:bg-purple-900/30 text-purple-600 text-xs font-bold px-2 py-1 rounded">3</span>
                                    <span>{{ __('Choose loan type and enter the requested amount') }}</span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <span class="bg-purple-100 dark:bg-purple-900/30 text-purple-600 text-xs font-bold px-2 py-1 rounded">4</span>
                                    <span>{{ __('Set repayment period and interest rate') }}</span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <span class="bg-purple-100 dark:bg-purple-900/30 text-purple-600 text-xs font-bold px-2 py-1 rounded">5</span>
                                    <span>{{ __('Add guarantor information and collateral details') }}</span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <span class="bg-purple-100 dark:bg-purple-900/30 text-purple-600 text-xs font-bold px-2 py-1 rounded">6</span>
                                    <span>{{ __('Submit for approval (Manager/Admin must approve before disbursement)') }}</span>
                                </li>
                            </ol>
                        </div>

                        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 border border-zinc-200 dark:border-zinc-700">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">{{ __('Approving and disbursing loans') }}</h3>
                            <p class="text-zinc-600 dark:text-zinc-400 mb-4">{{ __('Only Managers and Admins can approve loans:') }}</p>
                            <ul class="space-y-2 text-zinc-600 dark:text-zinc-400">
                                <li>• {{ __('Go to Loans page to see pending applications') }}</li>
                                <li>• {{ __('Click on a loan to review details and member eligibility') }}</li>
                                <li>• {{ __('Click "Approve" or "Reject" with reasons') }}</li>
                                <li>• {{ __('Approved loans can be disbursed immediately or scheduled') }}</li>
                                <li>• {{ __('Disbursement automatically creates a transaction and updates balances') }}</li>
                            </ul>
                        </div>

                        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 border border-zinc-200 dark:border-zinc-700">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">{{ __('Processing loan repayments') }}</h3>
                            <p class="text-zinc-600 dark:text-zinc-400 mb-4">{{ __('When members make loan payments:') }}</p>
                            <ul class="space-y-2 text-zinc-600 dark:text-zinc-400">
                                <li>• {{ __('Go to the member\'s loan details page') }}</li>
                                <li>• {{ __('Click "Record Payment"') }}</li>
                                <li>• {{ __('Enter payment amount (system calculates principal vs interest)') }}</li>
                                <li>• {{ __('Payment is recorded and loan balance is updated') }}</li>
                                <li>• {{ __('System tracks payment history and remaining balance') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Reports & Analytics Tab -->
                <div x-show="activeTab === 'reports'" x-transition>
                    <div class="space-y-6">
                        <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ __('Reports & Analytics') }}</h2>
                        
                        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 border border-zinc-200 dark:border-zinc-700">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">{{ __('Viewing the dashboard') }}</h3>
                            <p class="text-zinc-600 dark:text-zinc-400 mb-4">{{ __('The main dashboard shows key information at a glance:') }}</p>
                            <ul class="space-y-2 text-zinc-600 dark:text-zinc-400">
                                <li>• {{ __('Total number of members and their growth over time') }}</li>
                                <li>• {{ __('Total deposits and withdrawals for the current month') }}</li>
                                <li>• {{ __('Active loans and total loan portfolio value') }}</li>
                                <li>• {{ __('Recent transactions and pending approvals') }}</li>
                            </ul>
                        </div>

                        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 border border-zinc-200 dark:border-zinc-700">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">{{ __('Generating member statements') }}</h3>
                            <ol class="space-y-3 text-zinc-600 dark:text-zinc-400">
                                <li class="flex items-start space-x-3">
                                    <span class="bg-orange-100 dark:bg-orange-900/30 text-orange-600 text-xs font-bold px-2 py-1 rounded">1</span>
                                    <span>{{ __('Go to the member\'s profile page') }}</span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <span class="bg-orange-100 dark:bg-orange-900/30 text-orange-600 text-xs font-bold px-2 py-1 rounded">2</span>
                                    <span>{{ __('Click "Generate Statement" button') }}</span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <span class="bg-orange-100 dark:bg-orange-900/30 text-orange-600 text-xs font-bold px-2 py-1 rounded">3</span>
                                    <span>{{ __('Select date range and account types to include') }}</span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <span class="bg-orange-100 dark:bg-orange-900/30 text-orange-600 text-xs font-bold px-2 py-1 rounded">4</span>
                                    <span>{{ __('Statement is generated as PDF showing all transactions and balances') }}</span>
                                </li>
                            </ol>
                        </div>

                        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 border border-zinc-200 dark:border-zinc-700">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">{{ __('Available reports') }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-gray-50 dark:bg-zinc-700/50 rounded-lg p-4">
                                    <h4 class="font-medium text-zinc-900 dark:text-white mb-2">{{ __('Financial Reports') }}</h4>
                                    <ul class="text-sm text-zinc-600 dark:text-zinc-400 space-y-1">
                                        <li>• {{ __('Daily transaction summary') }}</li>
                                        <li>• {{ __('Monthly financial statements') }}</li>
                                        <li>• {{ __('Account balance reports') }}</li>
                                    </ul>
                                </div>
                                <div class="bg-gray-50 dark:bg-zinc-700/50 rounded-lg p-4">
                                    <h4 class="font-medium text-zinc-900 dark:text-white mb-2">{{ __('Member Reports') }}</h4>
                                    <ul class="text-sm text-zinc-600 dark:text-zinc-400 space-y-1">
                                        <li>• {{ __('Member registration reports') }}</li>
                                        <li>• {{ __('Active vs inactive members') }}</li>
                                        <li>• {{ __('Member savings summaries') }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>