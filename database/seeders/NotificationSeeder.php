<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some users to create notifications for
        $users = User::limit(5)->get();
        
        if ($users->isEmpty()) {
            $this->command->info('No users found. Please create users first.');
            return;
        }

        $notifications = [
            // Alert notifications
            [
                'type' => Notification::TYPE_ALERT,
                'title' => 'Overdue Loan Payment',
                'message' => 'Your loan payment of KES 15,000 is overdue by 5 days. Please make payment immediately to avoid penalties.',
                'priority' => Notification::PRIORITY_URGENT,
                'category' => Notification::CATEGORY_LOAN,
                'action_url' => '/loans',
                'action_text' => 'View Loan Details',
                'expires_at' => Carbon::now()->addDays(7),
            ],
            [
                'type' => Notification::TYPE_ALERT,
                'title' => 'Low Account Balance',
                'message' => 'Your account balance is below the minimum required amount of KES 1,000.',
                'priority' => Notification::PRIORITY_HIGH,
                'category' => Notification::CATEGORY_ACCOUNT,
                'action_url' => '/accounts',
                'action_text' => 'View Account',
            ],
            [
                'type' => Notification::TYPE_ALERT,
                'title' => 'Suspicious Transaction Detected',
                'message' => 'A large withdrawal of KES 50,000 was made from your account. If this was not you, please contact us immediately.',
                'priority' => Notification::PRIORITY_URGENT,
                'category' => Notification::CATEGORY_TRANSACTION,
                'action_url' => '/transactions',
                'action_text' => 'View Transaction',
            ],

            // Info notifications
            [
                'type' => Notification::TYPE_INFO,
                'title' => 'New Member Registration',
                'message' => 'A new member has joined your SACCO. Welcome John Mwangi!',
                'priority' => Notification::PRIORITY_NORMAL,
                'category' => Notification::CATEGORY_MEMBER,
                'action_url' => '/members',
                'action_text' => 'View Members',
            ],
            [
                'type' => Notification::TYPE_INFO,
                'title' => 'Monthly Report Available',
                'message' => 'Your monthly SACCO report for January 2024 is now available for download.',
                'priority' => Notification::PRIORITY_NORMAL,
                'category' => Notification::CATEGORY_SYSTEM,
                'action_url' => '/reports',
                'action_text' => 'Download Report',
            ],
            [
                'type' => Notification::TYPE_INFO,
                'title' => 'System Maintenance Complete',
                'message' => 'Scheduled system maintenance has been completed successfully. All services are now available.',
                'priority' => Notification::PRIORITY_NORMAL,
                'category' => Notification::CATEGORY_SYSTEM,
            ],

            // Reminder notifications
            [
                'type' => Notification::TYPE_REMINDER,
                'title' => 'Loan Payment Due Tomorrow',
                'message' => 'Your monthly loan payment of KES 8,500 is due tomorrow. Please ensure sufficient funds are available.',
                'priority' => Notification::PRIORITY_HIGH,
                'category' => Notification::CATEGORY_LOAN,
                'action_url' => '/loans',
                'action_text' => 'Make Payment',
                'expires_at' => Carbon::now()->addDays(1),
            ],
            [
                'type' => Notification::TYPE_REMINDER,
                'title' => 'Savings Goal Contribution',
                'message' => 'Don\'t forget to contribute to your "Emergency Fund" savings goal this month.',
                'priority' => Notification::PRIORITY_NORMAL,
                'category' => Notification::CATEGORY_SAVINGS,
                'action_url' => '/savings/goals',
                'action_text' => 'Contribute Now',
                'expires_at' => Carbon::now()->addDays(3),
            ],
            [
                'type' => Notification::TYPE_REMINDER,
                'title' => 'Annual Meeting Reminder',
                'message' => 'The SACCO annual general meeting is scheduled for next week. Please confirm your attendance.',
                'priority' => Notification::PRIORITY_NORMAL,
                'category' => Notification::CATEGORY_SYSTEM,
                'action_url' => '/notifications',
                'action_text' => 'RSVP',
                'expires_at' => Carbon::now()->addDays(5),
            ],

            // System notifications
            [
                'type' => Notification::TYPE_SYSTEM,
                'title' => 'Password Security Update',
                'message' => 'For your security, please update your password if you haven\'t done so in the last 90 days.',
                'priority' => Notification::PRIORITY_NORMAL,
                'category' => Notification::CATEGORY_SYSTEM,
                'action_url' => '/settings/password',
                'action_text' => 'Update Password',
            ],
            [
                'type' => Notification::TYPE_SYSTEM,
                'title' => 'New Branch Opening',
                'message' => 'We are excited to announce the opening of our new branch in Thika. Visit us for better service!',
                'priority' => Notification::PRIORITY_NORMAL,
                'category' => Notification::CATEGORY_BRANCH,
                'action_url' => '/branches',
                'action_text' => 'View Branches',
            ],
        ];

        foreach ($users as $user) {
            // Create 3-5 random notifications for each user
            $userNotifications = collect($notifications)->random(rand(3, 5));
            
            foreach ($userNotifications as $notificationData) {
                $notification = Notification::create([
                    'user_id' => $user->id,
                    'type' => $notificationData['type'],
                    'title' => $notificationData['title'],
                    'message' => $notificationData['message'],
                    'priority' => $notificationData['priority'],
                    'category' => $notificationData['category'],
                    'action_url' => $notificationData['action_url'] ?? null,
                    'action_text' => $notificationData['action_text'] ?? null,
                    'expires_at' => $notificationData['expires_at'] ?? null,
                    'is_read' => rand(0, 1) === 1, // Random read/unread status
                    'read_at' => rand(0, 1) === 1 ? Carbon::now()->subDays(rand(1, 7)) : null,
                    'created_at' => Carbon::now()->subDays(rand(0, 30)), // Random creation date within last 30 days
                ]);
            }
        }

        $this->command->info('Created sample notifications for users.');
    }
}
