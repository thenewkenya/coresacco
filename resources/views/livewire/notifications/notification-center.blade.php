<?php

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('components.layouts.app')] class extends Component {
    use WithPagination;

    public $filter = 'all';
    public $showMarkAllRead = false;

    public $filters = [
        'all' => 'All Notifications',
        'unread' => 'Unread',
        'read' => 'Read',
        'system' => 'System',
        'transaction' => 'Transactions',
        'loan' => 'Loans',
    ];

    public function with()
    {
        $query = auth()->user()->notifications();

        switch ($this->filter) {
            case 'unread':
                $query->whereNull('read_at');
                break;
            case 'read':
                $query->whereNotNull('read_at');
                break;
            case 'system':
                $query->where('type', 'App\Notifications\SystemNotification');
                break;
            case 'transaction':
                $query->where('type', 'like', '%Transaction%');
                break;
            case 'loan':
                $query->where('type', 'like', '%Loan%');
                break;
        }

        $notifications = $query->latest()->paginate(10);
        $unreadCount = auth()->user()->unreadNotifications()->count();

        return [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ];
    }

    public function updatedFilter()
    {
        $this->resetPage();
    }

    public function markAsRead($notificationId)
    {
        $notification = auth()->user()->notifications()->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
        }
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        $this->showMarkAllRead = false;
        session()->flash('success', 'All notifications marked as read.');
    }

    public function deleteNotification($notificationId)
    {
        $notification = auth()->user()->notifications()->find($notificationId);
        if ($notification) {
            $notification->delete();
            session()->flash('success', 'Notification deleted.');
        }
    }

    public function clearRead()
    {
        auth()->user()->readNotifications()->delete();
        session()->flash('success', 'Read notifications cleared.');
    }

    public function getNotificationIcon($type)
    {
        $icons = [
            'App\Notifications\SystemNotification' => 'information-circle',
            'App\Notifications\TransactionNotification' => 'currency-dollar',
            'App\Notifications\LoanApplicationNotification' => 'document-text',
            'App\Notifications\LoanApprovalNotification' => 'check-circle',
            'App\Notifications\LargeDepositNotification' => 'exclamation-triangle',
        ];

        return $icons[$type] ?? 'bell';
    }

    public function getNotificationColor($type)
    {
        $colors = [
            'App\Notifications\SystemNotification' => 'blue',
            'App\Notifications\TransactionNotification' => 'green',
            'App\Notifications\LoanApplicationNotification' => 'yellow',
            'App\Notifications\LoanApprovalNotification' => 'green',
            'App\Notifications\LargeDepositNotification' => 'red',
        ];

        return $colors[$type] ?? 'gray';
    }
}; ?>

<div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
    <!-- Header -->
    <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                        {{ __('Notification Center') }}
                    </h1>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                        {{ __('Stay updated with your SACCO activities') }}
                        @if($unreadCount > 0)
                            • <span class="font-medium text-blue-600 dark:text-blue-400">{{ $unreadCount }} unread</span>
                        @endif
                    </p>
                </div>
                
                @if($unreadCount > 0)
                    <flux:button wire:click="markAllAsRead" variant="outline">
                        <flux:icon.check class="w-4 h-4 mr-1" />
                        {{ __('Mark All Read') }}
                    </flux:button>
                @endif
            </div>
        </div>
    </div>

    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Filters -->
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4 mb-6">
                <div class="flex flex-wrap gap-2">
                    @foreach($filters as $key => $label)
                        <flux:button 
                            wire:click="$set('filter', '{{ $key }}')"
                            variant="{{ $filter === $key ? 'primary' : 'outline' }}"
                            size="sm">
                            {{ $label }}
                            @if($key === 'unread' && $unreadCount > 0)
                                <flux:badge class="ml-2">{{ $unreadCount }}</flux:badge>
                            @endif
                        </flux:button>
                    @endforeach
                </div>
            </div>

            <!-- Notifications List -->
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                @forelse($notifications as $notification)
                    <div class="border-b border-zinc-200 dark:border-zinc-700 last:border-b-0 {{ $notification->read_at ? 'bg-zinc-50 dark:bg-zinc-700/50' : 'bg-white dark:bg-zinc-800' }}">
                        <div class="px-6 py-4">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start space-x-3 flex-1">
                                    <!-- Icon -->
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center 
                                            bg-{{ $this->getNotificationColor($notification->type) }}-100 
                                            dark:bg-{{ $this->getNotificationColor($notification->type) }}-900/20">
                                            <flux:icon name="{{ $this->getNotificationIcon($notification->type) }}" 
                                                class="w-5 h-5 text-{{ $this->getNotificationColor($notification->type) }}-600 
                                                dark:text-{{ $this->getNotificationColor($notification->type) }}-400" />
                                        </div>
                                    </div>

                                    <!-- Content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                                {{ $notification->data['title'] ?? 'Notification' }}
                                            </p>
                                            @if(!$notification->read_at)
                                                <div class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0"></div>
                                            @endif
                                        </div>
                                        
                                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">
                                            {{ $notification->data['message'] ?? $notification->data['body'] ?? 'No message content' }}
                                        </p>
                                        
                                        <div class="flex items-center justify-between mt-3">
                                            <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </p>
                                            
                                            <div class="flex items-center space-x-2">
                                                @if(!$notification->read_at)
                                                    <flux:button 
                                                        wire:click="markAsRead('{{ $notification->id }}')"
                                                        variant="ghost" 
                                                        size="xs">
                                                        {{ __('Mark Read') }}
                                                    </flux:button>
                                                @endif
                                                
                                                <flux:button 
                                                    wire:click="deleteNotification('{{ $notification->id }}')"
                                                    wire:confirm="Are you sure you want to delete this notification?"
                                                    variant="danger" 
                                                    size="xs">
                                                    <flux:icon.trash class="w-3 h-3" />
                                                </flux:button>
                                            </div>
                                        </div>

                                        <!-- Action Button if available -->
                                        @if(isset($notification->data['action_url']) && isset($notification->data['action_text']))
                                            <div class="mt-3">
                                                <flux:button 
                                                    :href="$notification->data['action_url']"
                                                    variant="primary" 
                                                    size="sm"
                                                    wire:navigate>
                                                    {{ $notification->data['action_text'] }}
                                                </flux:button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center">
                        <flux:icon.bell class="w-12 h-12 text-zinc-400 dark:text-zinc-600 mx-auto mb-4" />
                        <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">
                            {{ __('No notifications') }}
                        </h3>
                        <p class="text-zinc-600 dark:text-zinc-400">
                            @if($filter === 'all')
                                {{ __('You have no notifications at this time.') }}
                            @else
                                {{ __('No notifications match the selected filter.') }}
                            @endif
                        </p>
                    </div>
                @endforelse

                <!-- Pagination -->
                @if($notifications->hasPages())
                    <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>

            <!-- Quick Actions -->
            @if($notifications->count() > 0)
                <div class="mt-6 flex justify-between items-center">
                    <div class="text-sm text-zinc-600 dark:text-zinc-400">
                        {{ __('Showing :from to :to of :total notifications', [
                            'from' => $notifications->firstItem(),
                            'to' => $notifications->lastItem(),
                            'total' => $notifications->total()
                        ]) }}
                    </div>
                    
                    <div class="flex space-x-2">
                        @if(auth()->user()->readNotifications()->count() > 0)
                            <flux:button 
                                wire:click="clearRead"
                                wire:confirm="Are you sure you want to delete all read notifications?"
                                variant="outline" 
                                size="sm">
                                {{ __('Clear Read') }}
                            </flux:button>
                        @endif
                        
                        <flux:button 
                            href="{{ route('notifications.settings') }}"
                            variant="outline" 
                            size="sm"
                            wire:navigate>
                            <flux:icon.cog-6-tooth class="w-4 h-4 mr-1" />
                            {{ __('Settings') }}
                        </flux:button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div> 