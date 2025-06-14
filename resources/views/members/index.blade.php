<x-layouts.app :title="__('Members')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                    <div>
                        <h1 class="text-xl lg:text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ __('Member Management') }}</h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Manage SACCO member profiles and accounts') }}</p>
                    </div>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                        <a href="{{ route('members.create') }}" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors text-center">
                            <flux:icon.plus class="w-4 h-4 inline mr-2" />
                            {{ __('Add Member') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <!-- Statistics -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">{{ __('Member Statistics') }}</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
                    <div class="bg-white dark:bg-zinc-800 rounded-xl p-4 lg:p-6 border border-zinc-200 dark:border-zinc-700">
                        <div class="flex items-center">
                            <div class="p-2 lg:p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex-shrink-0">
                                <flux:icon.users class="w-5 h-5 lg:w-6 lg:h-6 text-blue-600 dark:text-blue-400" />
                            </div>
                            <div class="ml-3 lg:ml-4 min-w-0 flex-1">
                                <p class="text-xs lg:text-sm text-zinc-600 dark:text-zinc-400 truncate">{{ __('Total Members') }}</p>
                                <p class="text-lg lg:text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['total_members'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-zinc-800 rounded-xl p-4 lg:p-6 border border-zinc-200 dark:border-zinc-700">
                        <div class="flex items-center">
                            <div class="p-2 lg:p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex-shrink-0">
                                <flux:icon.check-circle class="w-5 h-5 lg:w-6 lg:h-6 text-emerald-600 dark:text-emerald-400" />
                            </div>
                            <div class="ml-3 lg:ml-4 min-w-0 flex-1">
                                <p class="text-xs lg:text-sm text-zinc-600 dark:text-zinc-400 truncate">{{ __('Active Members') }}</p>
                                <p class="text-lg lg:text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['active_members'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-zinc-800 rounded-xl p-4 lg:p-6 border border-zinc-200 dark:border-zinc-700">
                        <div class="flex items-center">
                            <div class="p-2 lg:p-3 bg-red-100 dark:bg-red-900/30 rounded-lg flex-shrink-0">
                                <flux:icon.x-circle class="w-5 h-5 lg:w-6 lg:h-6 text-red-600 dark:text-red-400" />
                            </div>
                            <div class="ml-3 lg:ml-4 min-w-0 flex-1">
                                <p class="text-xs lg:text-sm text-zinc-600 dark:text-zinc-400 truncate">{{ __('Inactive Members') }}</p>
                                <p class="text-lg lg:text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['inactive_members'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-zinc-800 rounded-xl p-4 lg:p-6 border border-zinc-200 dark:border-zinc-700">
                        <div class="flex items-center">
                            <div class="p-2 lg:p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex-shrink-0">
                                <flux:icon.calendar class="w-5 h-5 lg:w-6 lg:h-6 text-purple-600 dark:text-purple-400" />
                            </div>
                            <div class="ml-3 lg:ml-4 min-w-0 flex-1">
                                <p class="text-xs lg:text-sm text-zinc-600 dark:text-zinc-400 truncate">{{ __('New This Month') }}</p>
                                <p class="text-lg lg:text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['new_this_month'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-4 mb-8">
                <form method="GET" action="{{ route('members.index') }}" class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-4 sm:space-y-0 sm:space-x-4">
                        <!-- Search -->
                        <div class="flex items-center space-x-2">
                            <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Search:') }}</label>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Name, email, member number...') }}" class="px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 w-64">
                        </div>
                        
                        <!-- Status Filter -->
                        <div class="flex items-center space-x-2">
                            <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Status:') }}</label>
                            <select name="status" class="px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100">
                                <option value="">{{ __('All') }}</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>{{ __('Suspended') }}</option>
                            </select>
                        </div>

                        <!-- Branch Filter -->
                        <div class="flex items-center space-x-2">
                            <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Branch:') }}</label>
                            <select name="branch" class="px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100">
                                <option value="">{{ __('All Branches') }}</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ request('branch') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            {{ __('Filter') }}
                        </button>
                        <a href="{{ route('members.index') }}" class="bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-zinc-700 dark:text-zinc-300 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            {{ __('Clear') }}
                        </a>
                    </div>
                </form>
            </div>

            <!-- Members Table -->
            @if($members->count() > 0)
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 mb-8">
                    <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                        <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ __('Members') }} ({{ $members->total() }})
                        </h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-zinc-50 dark:bg-zinc-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Member') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Contact') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Branch') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Status') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Accounts') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Joined') }}</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                                @foreach($members as $member)
                                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center flex-shrink-0">
                                                    <span class="text-sm font-bold text-white">
                                                        {{ substr($member->name, 0, 1) }}{{ substr(explode(' ', $member->name)[1] ?? '', 0, 1) }}
                                                    </span>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $member->name }}</div>
                                                    <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $member->member_number }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-zinc-900 dark:text-zinc-100">{{ $member->email }}</div>
                                            <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $member->phone_number }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-zinc-900 dark:text-zinc-100">{{ $member->branch->name ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $member->membership_status === 'active' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400' : '' }}
                                                {{ $member->membership_status === 'inactive' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : '' }}
                                                {{ $member->membership_status === 'suspended' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}">
                                                {{ ucfirst($member->membership_status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-100">
                                            {{ $member->accounts->count() }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                            {{ $member->joining_date ? \Carbon\Carbon::parse($member->joining_date)->format('M d, Y') : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <div class="flex items-center justify-center space-x-2">
                                                <a href="{{ route('members.show', $member) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                                    <flux:icon.eye class="w-4 h-4" />
                                                </a>
                                                @can('update', $member)
                                                    <a href="{{ route('members.edit', $member) }}" class="text-emerald-600 hover:text-emerald-900 dark:text-emerald-400 dark:hover:text-emerald-300">
                                                        <flux:icon.pencil class="w-4 h-4" />
                                                    </a>
                                                @endcan
                                                @can('delete', $member)
                                                    <form method="POST" action="{{ route('members.destroy', $member) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this member?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                            <flux:icon.trash class="w-4 h-4" />
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
                        {{ $members->links() }}
                    </div>
                </div>
            @else
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-8 text-center">
                    <flux:icon.users class="mx-auto h-12 w-12 text-zinc-400 mb-4" />
                    <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">{{ __('No Members Found') }}</h3>
                    <p class="text-zinc-600 dark:text-zinc-400 mb-4">{{ __('No members match your current filters.') }}</p>
                    <a href="{{ route('members.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        {{ __('Add First Member') }}
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app> 