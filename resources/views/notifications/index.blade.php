<x-layouts.app :title="__('Notifications')">
    <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
        <!-- Header -->
        <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            {{ __('Notifications') }}
                        </h1>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Manage your notifications and stay updated') }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-4">
                        @if($counts['unread'] > 0)
                            <flux:button 
                                variant="outline" 
                                icon="check-circle"
                                onclick="markAllAsRead()"
                            >
                                {{ __('Mark all read') }}
                            </flux:button>
                        @endif
                        
                        <flux:button 
                            variant="ghost" 
                            icon="trash"
                            href="{{ route('notifications.clearRead') }}"
                            onclick="return confirm('{{ __('Clear all read notifications?') }}')"
                        >
                            {{ __('Clear read') }}
                        </flux:button>
                        
                        <flux:button 
                            variant="primary" 
                            icon="cog"
                            href="{{ route('notifications.settings') }}"
                        >
                            {{ __('Settings') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4 sm:px-6 lg:px-8 py-8">
            <!-- Filters -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 mb-8">
                <div class="p-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                        <!-- Status Filter -->
                        <div class="flex items-center space-x-2">
                            <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Status:') }}</label>
                            <div class="flex space-x-1">
                                <a href="{{ route('notifications.index', array_merge(request()->all(), ['filter' => 'all'])) }}" 
                                   class="px-3 py-2 text-sm rounded-lg transition-colors {{ $filter === 'all' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-zinc-100 text-zinc-700 dark:bg-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-600' }}">
                                    {{ __('All') }} ({{ $counts['all'] }})
                                </a>
                                <a href="{{ route('notifications.index', array_merge(request()->all(), ['filter' => 'unread'])) }}" 
                                   class="px-3 py-2 text-sm rounded-lg transition-colors {{ $filter === 'unread' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-zinc-100 text-zinc-700 dark:bg-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-600' }}">
                                    {{ __('Unread') }} ({{ $counts['unread'] }})
                                </a>
                                <a href="{{ route('notifications.index', array_merge(request()->all(), ['filter' => 'read'])) }}" 
                                   class="px-3 py-2 text-sm rounded-lg transition-colors {{ $filter === 'read' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-zinc-100 text-zinc-700 dark:bg-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-600' }}">
                                    {{ __('Read') }} ({{ $counts['read'] }})
                                </a>
                            </div>
                        </div>

                        <!-- Type Filter -->
                        <div class="flex items-center space-x-2">
                            <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Type:') }}</label>
                            <select onchange="window.location.href='{{ route('notifications.index') }}?' + new URLSearchParams(Object.assign(Object.fromEntries(new URLSearchParams(window.location.search)), {type: this.value})).toString()" 
                                    class="px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100">
                                <option value="all" {{ $type === 'all' ? 'selected' : '' }}>{{ __('All Types') }}</option>
                                <option value="transaction" {{ $type === 'transaction' ? 'selected' : '' }}>{{ __('Transactions') }}</option>
                                <option value="loan" {{ $type === 'loan' ? 'selected' : '' }}>{{ __('Loans') }}</option>
                                @if(!auth()->user()->hasRole('member'))
                                    <option value="large_deposit" {{ $type === 'large_deposit' ? 'selected' : '' }}>{{ __('Large Deposits') }}</option>
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications List -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                @forelse($notifications as $notification)
                    @php
                        $data = $notification->data;
                        $iconColorClass = match($data['color'] ?? 'zinc') {
                            'blue' => 'text-blue-500',
                            'green' => 'text-green-500',
                            'red' => 'text-red-500',
                            'amber' => 'text-amber-500',
                            'emerald' => 'text-emerald-500',
                            default => 'text-zinc-500'
                        };
                    @endphp
                    
                    <div class="border-b border-zinc-200 dark:border-zinc-700 last:border-b-0">
                        <div class="p-6 {{ is_null($notification->read_at) ? 'bg-blue-50/30 dark:bg-blue-900/10' : '' }}">
                            <div class="flex items-start space-x-4">
                                <!-- Icon -->
                                <div class="flex-shrink-0">
                                    <flux:icon.{{ $data['icon'] ?? 'bell' }} class="h-6 w-6 {{ $iconColorClass }}" />
                                </div>
                                
                                <!-- Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                                                {{ $data['title'] ?? 'Notification' }}
                                                @if(is_null($notification->read_at))
                                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                                        {{ __('New') }}
                                                    </span>
                                                @endif
                                            </h3>
                                            
                                            <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">
                                                {{ $data['message'] ?? '' }}
                                            </p>
                                            
                                            <div class="flex items-center space-x-4 mt-2 text-xs text-zinc-500 dark:text-zinc-400">
                                                <span>{{ $notification->created_at->diffForHumans() }}</span>
                                                @if(isset($data['priority']))
                                                    <span class="px-2 py-0.5 rounded-full {{ $data['priority'] === 'high' ? 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400' : ($data['priority'] === 'medium' ? 'bg-yellow-100 text-yellow-600 dark:bg-yellow-900/30 dark:text-yellow-400' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-700 dark:text-zinc-400') }}">
                                                        {{ ucfirst($data['priority']) }} Priority
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <!-- Actions -->
                                        <div class="flex items-center space-x-2 ml-4">
                                            @if(is_null($notification->read_at))
                                                <flux:button 
                                                    size="xs" 
                                                    variant="ghost"
                                                    icon="check"
                                                    onclick="markAsRead('{{ $notification->id }}')"
                                                    title="{{ __('Mark as read') }}"
                                                >
                                                </flux:button>
                                            @endif
                                            
                                            @if(isset($data['url']) && $data['url'] !== '#')
                                                <flux:button 
                                                    size="xs" 
                                                    variant="outline"
                                                    icon="eye"
                                                    href="{{ $data['url'] }}"
                                                >
                                                    {{ __('View') }}
                                                </flux:button>
                                            @endif
                                            
                                            <flux:button 
                                                size="xs" 
                                                variant="ghost"
                                                icon="trash"
                                                onclick="deleteNotification('{{ $notification->id }}')"
                                                title="{{ __('Delete') }}"
                                                class="text-red-600 hover:text-red-700"
                                            >
                                            </flux:button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <flux:icon.bell-slash class="h-12 w-12 text-zinc-400 dark:text-zinc-500 mx-auto mb-4" />
                        <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">{{ __('No notifications') }}</h3>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">
                            @if($filter === 'unread')
                                {{ __('You have no unread notifications.') }}
                            @elseif($filter === 'read')
                                {{ __('You have no read notifications.') }}
                            @else
                                {{ __('You have no notifications yet.') }}
                            @endif
                        </p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($notifications->hasPages())
                <div class="mt-8">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        function markAsRead(notificationId) {
            fetch(`/notifications/${notificationId}/mark-read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                }
            });
        }

        function markAllAsRead() {
            fetch('/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                }
            });
        }

        function deleteNotification(notificationId) {
            if (confirm('{{ __('Delete this notification?') }}')) {
                fetch(`/notifications/${notificationId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    }
                });
            }
        }
    </script>
</x-layouts.app> 