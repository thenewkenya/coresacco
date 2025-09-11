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

<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" class="!text-zinc-900 dark:!text-zinc-100">Notification Center</flux:heading>
            <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">
                Stay updated with your SACCO activities
                @if($unreadCount > 0)
                    • <span class="font-medium text-blue-600 dark:text-blue-400">{{ $unreadCount }} unread</span>
                @endif
            </flux:subheading>
        </div>
        
        @if($unreadCount > 0)
            <flux:button wire:click="markAllAsRead" variant="outline" icon="check">
                Mark All Read
            </flux:button>
        @endif
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:heading size="base" class="dark:text-zinc-100">Total Notifications</flux:heading>
                    <flux:subheading class="dark:text-zinc-400">All notifications</flux:subheading>
                </div>
                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <flux:icon.bell class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $notifications->total() }}</div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:heading size="base" class="dark:text-zinc-100">Unread</flux:heading>
                    <flux:subheading class="dark:text-zinc-400">New notifications</flux:subheading>
                </div>
                <div class="p-2 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                    <flux:icon.exclamation-circle class="w-5 h-5 text-orange-600 dark:text-orange-400" />
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $unreadCount }}</div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:heading size="base" class="dark:text-zinc-100">Read</flux:heading>
                    <flux:subheading class="dark:text-zinc-400">Viewed notifications</flux:subheading>
                </div>
                <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                    <flux:icon.check-circle class="w-5 h-5 text-green-600 dark:text-green-400" />
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $notifications->total() - $unreadCount }}</div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:heading size="base" class="dark:text-zinc-100">This Week</flux:heading>
                    <flux:subheading class="dark:text-zinc-400">Recent activity</flux:subheading>
                </div>
                <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                    <flux:icon.calendar class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $notifications->where('created_at', '>=', now()->subWeek())->count() }}</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
        <div class="flex items-center space-x-3 mb-4">
            <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                <flux:icon.funnel class="w-5 h-5 text-blue-600 dark:text-blue-400" />
            </div>
            <div>
                <flux:heading size="base" class="dark:text-zinc-100">Filter Notifications</flux:heading>
                <flux:subheading class="dark:text-zinc-400">View notifications by type and status</flux:subheading>
            </div>
        </div>
        
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
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        @forelse($notifications as $notification)
            <div class="border-b border-zinc-200 dark:border-zinc-700 last:border-b-0 {{ $notification->read_at ? 'bg-zinc-50 dark:bg-zinc-700/50' : 'bg-white dark:bg-zinc-800' }}">
                <div class="px-6 py-4">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start space-x-4 flex-1">
                            <!-- Icon -->
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center 
                                    bg-{{ $this->getNotificationColor($notification->type) }}-100 
                                    dark:bg-{{ $this->getNotificationColor($notification->type) }}-900/20">
                                    <flux:icon name="{{ $this->getNotificationIcon($notification->type) }}" 
                                        class="w-6 h-6 text-{{ $this->getNotificationColor($notification->type) }}-600 
                                        dark:text-{{ $this->getNotificationColor($notification->type) }}-400" />
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2">
                                            <p class="text-base font-semibold text-zinc-900 dark:text-zinc-100">
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
                                                        size="sm">
                                                        Mark Read
                                                    </flux:button>
                                                @endif
                                                
                                                <flux:button 
                                                    wire:click="deleteNotification('{{ $notification->id }}')"
                                                    wire:confirm="Are you sure you want to delete this notification?"
                                                    variant="danger" 
                                                    size="sm">
                                                    <flux:icon.trash class="w-4 h-4" />
                                                </flux:button>
                                            </div>
                                        </div>

                                        <!-- Action Button if available -->
                                        @if(isset($notification->data['action_url']) && isset($notification->data['action_text']))
                                            <div class="mt-4">
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
                </div>
            </div>
        @empty
            <div class="px-6 py-12 text-center">
                <flux:icon.bell class="w-16 h-16 text-zinc-400 dark:text-zinc-600 mx-auto mb-4" />
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-2">
                    No notifications
                </h3>
                <p class="text-zinc-600 dark:text-zinc-400">
                    @if($filter === 'all')
                        You have no notifications at this time.
                    @else
                        No notifications match the selected filter.
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
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div class="text-sm text-zinc-600 dark:text-zinc-400">
                    Showing {{ $notifications->firstItem() }} to {{ $notifications->lastItem() }} of {{ $notifications->total() }} notifications
                </div>
                
                <div class="flex space-x-3">
                    @if(auth()->user()->readNotifications()->count() > 0)
                        <flux:button 
                            wire:click="clearRead"
                            wire:confirm="Are you sure you want to delete all read notifications?"
                            variant="outline" 
                            size="sm"
                            icon="trash">
                            Clear Read
                        </flux:button>
                    @endif
                    
                    <flux:button 
                        href="{{ route('notifications.settings') }}"
                        variant="outline" 
                        size="sm"
                        icon="cog-6-tooth"
                        wire:navigate>
                        Settings
                    </flux:button>
                </div>
            </div>
        </div>
    @endif
</div> 