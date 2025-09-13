import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Bell, Check, AlertCircle, Info, Mail } from 'lucide-react';
import { Badge } from '@/components/ui/badge';

export default function NotificationsIndex() {
    return (
        <AppLayout>
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">Notifications</h1>
                        <p className="text-muted-foreground">
                            Manage system notifications and alerts
                        </p>
                    </div>
                    <Button>
                        <Check className="mr-2 h-4 w-4" />
                        Mark All Read
                    </Button>
                </div>

                {/* Stats Cards */}
                <div className="grid auto-rows-min gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Unread</CardTitle>
                            <Bell className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">12</div>
                            <p className="text-xs text-muted-foreground">
                                New notifications
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Alerts</CardTitle>
                            <AlertCircle className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">3</div>
                            <p className="text-xs text-muted-foreground">
                                Require attention
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Info</CardTitle>
                            <Info className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">8</div>
                            <p className="text-xs text-muted-foreground">
                                General updates
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Emails</CardTitle>
                            <Mail className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">24</div>
                            <p className="text-xs text-muted-foreground">
                                Sent this week
                            </p>
                        </CardContent>
                    </Card>
                </div>

                {/* Notification Types */}
                <div className="grid gap-4 md:grid-cols-3">
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center">
                                <AlertCircle className="mr-2 h-5 w-5 text-red-500" />
                                Alerts
                            </CardTitle>
                            <CardDescription>
                                Critical notifications requiring immediate attention
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-2">
                                <div className="flex items-center justify-between">
                                    <span className="text-sm">Overdue loan payments</span>
                                    <Badge variant="destructive">3</Badge>
                                </div>
                                <div className="flex items-center justify-between">
                                    <span className="text-sm">Low account balances</span>
                                    <Badge variant="destructive">2</Badge>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center">
                                <Info className="mr-2 h-5 w-5 text-blue-500" />
                                Information
                            </CardTitle>
                            <CardDescription>
                                General updates and system notifications
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-2">
                                <div className="flex items-center justify-between">
                                    <span className="text-sm">New member registrations</span>
                                    <Badge variant="secondary">5</Badge>
                                </div>
                                <div className="flex items-center justify-between">
                                    <span className="text-sm">Monthly reports ready</span>
                                    <Badge variant="secondary">1</Badge>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center">
                                <Bell className="mr-2 h-5 w-5 text-green-500" />
                                Reminders
                            </CardTitle>
                            <CardDescription>
                                Scheduled reminders and follow-ups
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-2">
                                <div className="flex items-center justify-between">
                                    <span className="text-sm">Loan payment due</span>
                                    <Badge variant="outline">4</Badge>
                                </div>
                                <div className="flex items-center justify-between">
                                    <span className="text-sm">Meeting reminders</span>
                                    <Badge variant="outline">2</Badge>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Recent Notifications */}
                <Card>
                    <CardHeader>
                        <CardTitle>Recent Notifications</CardTitle>
                        <CardDescription>
                            Latest notifications from your SACCO
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="text-center py-8 text-muted-foreground">
                            <Bell className="mx-auto h-12 w-12 mb-4" />
                            <p>Recent notifications will be listed here</p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}

