<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class NotificationController extends Controller
{
    /**
     * Display all notifications for the current user
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get filters from request
        $filter = $request->get('filter', 'all'); // all, unread, read
        $type = $request->get('type', 'all'); // all, transaction, loan, large_deposit
        
        // Build query
        $query = $user->notifications();
        
        // Apply filters
        if ($filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($filter === 'read') {
            $query->whereNotNull('read_at');
        }
        
        if ($type !== 'all') {
            $notificationTypes = [
                'transaction' => 'App\Notifications\TransactionNotification',
                'loan' => 'App\Notifications\LoanApplicationNotification',
                'large_deposit' => 'App\Notifications\LargeDepositNotification',
            ];
            
            if (isset($notificationTypes[$type])) {
                $query->where('type', $notificationTypes[$type]);
            }
        }
        
        // Role-based filtering
        if ($user->hasRole('member')) {
            $query->whereIn('type', [
                'App\Notifications\TransactionNotification',
                'App\Notifications\LoanApplicationNotification'
            ]);
        }
        
        $notifications = $query->latest()->paginate(20);
        
        // Get counts for badges
        $counts = [
            'all' => $user->notifications()->count(),
            'unread' => $user->unreadNotifications()->count(),
            'read' => $user->notifications()->whereNotNull('read_at')->count(),
        ];
        
        return view('notifications.index', compact('notifications', 'filter', 'type', 'counts'));
    }
    
    /**
     * Mark a specific notification as read
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->find($id);
        
        if ($notification && is_null($notification->read_at)) {
            $notification->markAsRead();
        }
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Delete a specific notification
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->find($id);
        
        if ($notification) {
            $notification->delete();
        }
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Clear all read notifications
     */
    public function clearRead()
    {
        Auth::user()->notifications()->whereNotNull('read_at')->delete();
        
        return Redirect::route('notifications.index')->with('success', 'Read notifications cleared.');
    }
    
    /**
     * Get notification settings
     */
    public function settings()
    {
        $user = Auth::user();
        
        // Default notification preferences
        $preferences = [
            'email_transactions' => true,
            'email_loans' => true,
            'email_large_deposits' => true,
            'push_transactions' => true,
            'push_loans' => true,
            'push_large_deposits' => true,
            'sms_transactions' => false,
            'sms_loans' => true,
            'sms_large_deposits' => true,
        ];
        
        // TODO: Load from user preferences table when implemented
        
        return view('notifications.settings', compact('preferences'));
    }
    
    /**
     * Update notification settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'email_transactions' => 'boolean',
            'email_loans' => 'boolean',
            'email_large_deposits' => 'boolean',
            'push_transactions' => 'boolean',
            'push_loans' => 'boolean',
            'push_large_deposits' => 'boolean',
            'sms_transactions' => 'boolean',
            'sms_loans' => 'boolean',
            'sms_large_deposits' => 'boolean',
        ]);
        
        // TODO: Save to user preferences table when implemented
        
        return Redirect::route('notifications.settings')->with('success', 'Notification preferences updated.');
    }
}
