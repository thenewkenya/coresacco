<div class="relative flex items-center justify-center">
    <flux:tooltip :content="__('Notifications')" position="bottom">
        <flux:dropdown position="bottom" align="center">
            <flux:navbar.item class="relative flex items-center justify-center" icon="bell" wire:click="refreshNotifications">
                @if($unreadCount > 0)
                    <span class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center min-w-[1.25rem] h-5 text-xs font-bold leading-none text-white bg-red-500 rounded-full">
                        {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                    </span>
                @endif
            </flux:navbar.item>
            
            <flux:menu class="w-80">
                <!-- Header -->
                <div class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ __('Notifications') }}
                            @if($unreadCount > 0)
                                <span class="ml-2 text-xs text-zinc-500 dark:text-zinc-400">({{ $unreadCount }} new)</span>
                            @endif
                        </h3>
                        @if($unreadCount > 0)
                            <button 
                                wire:click="markAllAsRead"
                                class="text-xs text-blue-600 dark:text-blue-400 hover:underline"
                            >
                                {{ __('Mark all read') }}
                            </button>
                        @endif
                    </div>
                </div>
                
                <!-- Notifications List -->
                <div class="max-h-64 overflow-y-auto">
                    @forelse($notifications as $notification)
                        <div class="relative">
                            <flux:menu.item 
                                class="px-4 py-3 border-b border-zinc-100 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-900 {{ $notification['is_read'] ? 'opacity-75' : '' }}"
                                wire:click="markAsRead('{{ $notification['id'] }}')"
                                href="{{ $notification['url'] ?? '#' }}"
                            >
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        @php
                                            $color = $notification['color'] ?? 'zinc';
                                            $iconColorClass = match($color) {
                                                'blue' => 'text-blue-500',
                                                'green' => 'text-green-500',
                                                'red' => 'text-red-500',
                                                'amber' => 'text-amber-500',
                                                'yellow' => 'text-yellow-500',
                                                'emerald' => 'text-emerald-500',
                                                'indigo' => 'text-indigo-500',
                                                'purple' => 'text-purple-500',
                                                'pink' => 'text-pink-500',
                                                'gray' => 'text-gray-500',
                                                'slate' => 'text-slate-500',
                                                'neutral' => 'text-neutral-500',
                                                'orange' => 'text-orange-500',
                                                'lime' => 'text-lime-500',
                                                'teal' => 'text-teal-500',
                                                'cyan' => 'text-cyan-500',
                                                'sky' => 'text-sky-500',
                                                'violet' => 'text-violet-500',
                                                'fuchsia' => 'text-fuchsia-500',
                                                'rose' => 'text-rose-500',
                                                'zinc' => 'text-zinc-500',
                                                default => 'text-zinc-500'
                                            };
                                        @endphp
                                        <flux:icon.{{ $notification['icon'] ?? 'bell' }} class="h-5 w-5 {{ $iconColorClass }}" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-zinc-900 dark:text-zinc-100 font-medium">
                                            {{ $notification['title'] ?? 'Notification' }}
                                        </p>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                            {{ $notification['message'] ?? '' }}
                                        </p>
                                        <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">
                                            {{ $notification['time_ago'] ?? '' }}
                                        </p>
                                    </div>
                                    @if(!$notification['is_read'])
                                        <div class="w-2 h-2 bg-{{ $notification['color'] ?? 'blue' }}-500 rounded-full"></div>
                                    @endif
                                </div>
                            </flux:menu.item>
                            
                            <!-- Action buttons for high-priority notifications -->
                            @if(isset($notification['requires_action']) && $notification['requires_action'] && !$notification['is_read'])
                                <div class="px-4 py-2 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800">
                                    <div class="flex space-x-2">
                                        @if(isset($notification['actions']))
                                            @foreach($notification['actions'] as $action)
                                                <flux:button 
                                                    size="xs" 
                                                    variant="outline"
                                                    class="text-{{ $action['color'] ?? 'blue' }}-600 border-{{ $action['color'] ?? 'blue' }}-600 hover:bg-{{ $action['color'] ?? 'blue' }}-50"
                                                >
                                                    {{ $action['label'] }}
                                                </flux:button>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="px-4 py-8 text-center">
                            <flux:icon.bell-slash class="h-8 w-8 text-zinc-400 dark:text-zinc-500 mx-auto mb-2" />
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('No notifications') }}</p>
                            <p class="text-xs text-zinc-400 dark:text-zinc-500">{{ __('You\'re all caught up!') }}</p>
                        </div>
                    @endforelse
                </div>
                
                <!-- Footer -->
                <div class="px-4 py-2 border-t border-zinc-200 dark:border-zinc-700">
                    <a 
                        href="{{ route('notifications.index') }}" 
                        class="block text-center text-sm text-blue-600 dark:text-blue-400 hover:underline"
                    >
                        {{ __('View all notifications') }}
                    </a>
                </div>
            </flux:menu>
        </flux:dropdown>
    </flux:tooltip>
</div>
