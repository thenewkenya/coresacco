<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class NotificationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of notifications
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get filter parameters
        $type = $request->get('type', 'all');
        $category = $request->get('category', 'all');
        $priority = $request->get('priority', 'all');
        $status = $request->get('status', 'all'); // all, read, unread
        
        // Build query
        $query = $user->notifications()->active()->latest();
        
        if ($type !== 'all') {
            $query->byType($type);
        }
        
        if ($category !== 'all') {
            $query->byCategory($category);
        }
        
        if ($priority !== 'all') {
            $query->byPriority($priority);
        }
        
        if ($status === 'read') {
            $query->read();
        } elseif ($status === 'unread') {
            $query->unread();
        }
        
        $notifications = $query->paginate(20);
        
        // Get statistics
        $stats = [
            'total' => $user->notifications()->active()->count(),
            'unread' => $user->notifications()->active()->unread()->count(),
            'alerts' => $user->notifications()->active()->byType(Notification::TYPE_ALERT)->unread()->count(),
            'reminders' => $user->notifications()->active()->byType(Notification::TYPE_REMINDER)->unread()->count(),
            'urgent' => $user->notifications()->active()->byPriority(Notification::PRIORITY_URGENT)->unread()->count(),
        ];
        
        // Get recent notifications for sidebar
        $recentNotifications = $user->notifications()
            ->active()
            ->unread()
            ->latest()
            ->limit(5)
            ->get();
        
        // Get filter options
        $types = [
            'all' => 'All Types',
            Notification::TYPE_ALERT => 'Alerts',
            Notification::TYPE_INFO => 'Information',
            Notification::TYPE_REMINDER => 'Reminders',
            Notification::TYPE_SYSTEM => 'System',
        ];
        
        $categories = [
            'all' => 'All Categories',
            Notification::CATEGORY_LOAN => 'Loans',
            Notification::CATEGORY_ACCOUNT => 'Accounts',
            Notification::CATEGORY_TRANSACTION => 'Transactions',
            Notification::CATEGORY_SAVINGS => 'Savings',
            Notification::CATEGORY_SYSTEM => 'System',
            Notification::CATEGORY_MEMBER => 'Members',
            Notification::CATEGORY_BRANCH => 'Branches',
        ];
        
        $priorities = [
            'all' => 'All Priorities',
            Notification::PRIORITY_LOW => 'Low',
            Notification::PRIORITY_NORMAL => 'Normal',
            Notification::PRIORITY_HIGH => 'High',
            Notification::PRIORITY_URGENT => 'Urgent',
        ];
        
        return Inertia::render('notifications/index', [
            'notifications' => $notifications,
            'stats' => $stats,
            'recentNotifications' => $recentNotifications,
            'filters' => [
                'type' => $type,
                'category' => $category,
                'priority' => $priority,
                'status' => $status,
            ],
            'filterOptions' => [
                'types' => $types,
                'categories' => $categories,
                'priorities' => $priorities,
            ],
        ]);
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(Notification $notification)
    {
        $this->authorize('update', $notification);
        
        $notification->markAsRead();
        
        return redirect()->back();
    }

    /**
     * Mark a notification as unread
     */
    public function markAsUnread(Notification $notification)
    {
        $this->authorize('update', $notification);
        
        $notification->markAsUnread();
        
        return redirect()->back();
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        
        $user->notifications()
            ->active()
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        
        return redirect()->back();
    }

    /**
     * Delete a notification
     */
    public function destroy(Notification $notification)
    {
        $this->authorize('delete', $notification);
        
        $notification->delete();
        
        return redirect()->back();
    }

    /**
     * Get unread notifications count for header
     */
    public function unreadCount()
    {
        $user = Auth::user();
        
        $count = $user->notifications()
            ->active()
            ->unread()
            ->count();
        
        return response()->json(['count' => $count]);
    }

    /**
     * Get recent notifications for header dropdown
     */
    public function recent()
    {
        $user = Auth::user();
        
        $notifications = $user->notifications()
            ->active()
            ->latest()
            ->limit(10)
            ->get();
        
        return response()->json(['notifications' => $notifications]);
    }
}
