<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public $emailNotifications = true;
    public $smsNotifications = false;
    public $pushNotifications = true;
    public $transactionAlerts = true;
    public $loanAlerts = true;
    public $systemAlerts = true;
    public $marketingEmails = false;
    public $weeklyDigest = true;
    public $monthlyReport = true;

    public function mount()
    {
        $user = auth()->user();
        
        // Load user's notification preferences
        $preferences = $user->notification_preferences ?? [];
        
        $this->emailNotifications = $preferences['email_notifications'] ?? true;
        $this->smsNotifications = $preferences['sms_notifications'] ?? false;
        $this->pushNotifications = $preferences['push_notifications'] ?? true;
        $this->transactionAlerts = $preferences['transaction_alerts'] ?? true;
        $this->loanAlerts = $preferences['loan_alerts'] ?? true;
        $this->systemAlerts = $preferences['system_alerts'] ?? true;
        $this->marketingEmails = $preferences['marketing_emails'] ?? false;
        $this->weeklyDigest = $preferences['weekly_digest'] ?? true;
        $this->monthlyReport = $preferences['monthly_report'] ?? true;
    }

    public function save()
    {
        $user = auth()->user();
        
        $preferences = [
            'email_notifications' => $this->emailNotifications,
            'sms_notifications' => $this->smsNotifications,
            'push_notifications' => $this->pushNotifications,
            'transaction_alerts' => $this->transactionAlerts,
            'loan_alerts' => $this->loanAlerts,
            'system_alerts' => $this->systemAlerts,
            'marketing_emails' => $this->marketingEmails,
            'weekly_digest' => $this->weeklyDigest,
            'monthly_report' => $this->monthlyReport,
        ];

        $user->update(['notification_preferences' => $preferences]);
        
        session()->flash('success', 'Notification settings updated successfully!');
    }
}

?><div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" class="!text-zinc-900 dark:!text-zinc-100">Notification Settings</flux:heading>
            <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">Manage how you receive notifications about your SACCO activities</flux:subheading>
        </div>
        <flux:button variant="ghost" :href="route('notifications.index')" icon="arrow-left">
            Back to Notifications
        </flux:button>
    </div>

    <form wire:submit="save" class="space-y-6">
        <!-- General Notification Settings -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center space-x-3 mb-6">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <flux:icon.bell class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <flux:heading size="base" class="dark:text-zinc-100">General Notifications</flux:heading>
                    <flux:subheading class="dark:text-zinc-400">Choose how you want to receive notifications</flux:subheading>
                </div>
            </div>

            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="font-medium text-zinc-900 dark:text-zinc-100">Email Notifications</div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400">Receive notifications via email</div>
                    </div>
                    <flux:checkbox wire:model="emailNotifications" />
                </div>

                <div class="flex items-center justify-between">
                    <div>
                        <div class="font-medium text-zinc-900 dark:text-zinc-100">SMS Notifications</div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400">Receive important alerts via SMS</div>
                    </div>
                    <flux:checkbox wire:model="smsNotifications" />
                </div>

                <div class="flex items-center justify-between">
                    <div>
                        <div class="font-medium text-zinc-900 dark:text-zinc-100">Push Notifications</div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400">Receive browser push notifications</div>
                    </div>
                    <flux:checkbox wire:model="pushNotifications" />
                </div>
            </div>
        </div>

        <!-- Activity Alerts -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center space-x-3 mb-6">
                <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                    <flux:icon.chart-bar class="w-5 h-5 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <flux:heading size="base" class="dark:text-zinc-100">Activity Alerts</flux:heading>
                    <flux:subheading class="dark:text-zinc-400">Choose which activities you want to be notified about</flux:subheading>
                </div>
            </div>

            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="font-medium text-zinc-900 dark:text-zinc-100">Transaction Alerts</div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400">Get notified about deposits, withdrawals, and payments</div>
                    </div>
                    <flux:checkbox wire:model="transactionAlerts" />
                </div>

                <div class="flex items-center justify-between">
                    <div>
                        <div class="font-medium text-zinc-900 dark:text-zinc-100">Loan Alerts</div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400">Get notified about loan applications, approvals, and payments</div>
                    </div>
                    <flux:checkbox wire:model="loanAlerts" />
                </div>

                <div class="flex items-center justify-between">
                    <div>
                        <div class="font-medium text-zinc-900 dark:text-zinc-100">System Alerts</div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400">Get notified about system updates and maintenance</div>
                    </div>
                    <flux:checkbox wire:model="systemAlerts" />
                </div>
            </div>
        </div>

        <!-- Communication Preferences -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center space-x-3 mb-6">
                <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                    <flux:icon.envelope class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                </div>
                <div>
                    <flux:heading size="base" class="dark:text-zinc-100">Communication Preferences</flux:heading>
                    <flux:subheading class="dark:text-zinc-400">Manage your communication preferences</flux:subheading>
                </div>
            </div>

            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="font-medium text-zinc-900 dark:text-zinc-100">Marketing Emails</div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400">Receive promotional emails and offers</div>
                    </div>
                    <flux:checkbox wire:model="marketingEmails" />
                </div>

                <div class="flex items-center justify-between">
                    <div>
                        <div class="font-medium text-zinc-900 dark:text-zinc-100">Weekly Digest</div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400">Receive a weekly summary of your activities</div>
                    </div>
                    <flux:checkbox wire:model="weeklyDigest" />
                </div>

                <div class="flex items-center justify-between">
                    <div>
                        <div class="font-medium text-zinc-900 dark:text-zinc-100">Monthly Report</div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400">Receive detailed monthly financial reports</div>
                    </div>
                    <flux:checkbox wire:model="monthlyReport" />
                </div>
            </div>
        </div>

        <!-- Notification Frequency -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center space-x-3 mb-6">
                <div class="p-2 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                    <flux:icon.clock class="w-5 h-5 text-orange-600 dark:text-orange-400" />
                </div>
                <div>
                    <flux:heading size="base" class="dark:text-zinc-100">Notification Frequency</flux:heading>
                    <flux:subheading class="dark:text-zinc-400">How often you want to receive notifications</flux:subheading>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg">
                    <div class="text-center">
                        <flux:icon.bolt class="w-8 h-8 text-blue-600 dark:text-blue-400 mx-auto mb-2" />
                        <div class="font-medium text-zinc-900 dark:text-zinc-100">Immediate</div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400">Get notified instantly</div>
                    </div>
                </div>

                <div class="p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg">
                    <div class="text-center">
                        <flux:icon.clock class="w-8 h-8 text-green-600 dark:text-green-400 mx-auto mb-2" />
                        <div class="font-medium text-zinc-900 dark:text-zinc-100">Daily Digest</div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400">Once per day summary</div>
                    </div>
                </div>

                <div class="p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg">
                    <div class="text-center">
                        <flux:icon.calendar class="w-8 h-8 text-purple-600 dark:text-purple-400 mx-auto mb-2" />
                        <div class="font-medium text-zinc-900 dark:text-zinc-100">Weekly</div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400">Weekly summary only</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex items-center justify-end space-x-4">
            <flux:button variant="ghost" :href="route('notifications.index')">
                Cancel
            </flux:button>
            <flux:button type="submit" variant="primary" icon="check">
                Save Settings
            </flux:button>
        </div>
    </form>
</div>
