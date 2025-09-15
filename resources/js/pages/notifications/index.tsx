import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { 
    Bell, 
    Check, 
    AlertCircle, 
    Info, 
    Clock,
    Filter,
    Eye,
    EyeOff,
    Trash2,
    ExternalLink,
    RefreshCw
} from 'lucide-react';

interface Notification {
    id: number;
    type: 'alert' | 'info' | 'reminder' | 'system';
    title: string;
    message: string;
    data?: any;
    action_url?: string;
    action_text?: string;
    is_read: boolean;
    read_at?: string;
    expires_at?: string;
    priority: 'low' | 'normal' | 'high' | 'urgent';
    category?: string;
    created_at: string;
    updated_at: string;
}

interface Props {
    notifications: {
        data: Notification[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
    stats: {
        total: number;
        unread: number;
        alerts: number;
        reminders: number;
        urgent: number;
    };
    recentNotifications: Notification[];
    filters: {
        type: string;
        category: string;
        priority: string;
        status: string;
    };
    filterOptions: {
        types: Record<string, string>;
        categories: Record<string, string>;
        priorities: Record<string, string>;
    };
}

export default function NotificationsIndex({ 
    notifications, 
    stats, 
    recentNotifications, 
    filters, 
    filterOptions 
}: Props) {
    const [isLoading, setIsLoading] = useState(false);

    const formatDate = (dateString: string) => {
        if (!dateString) return 'N/A';
        try {
            return new Date(dateString).toLocaleDateString('en-KE', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        } catch {
            return 'Invalid Date';
        }
    };

    const getTypeIcon = (type: string) => {
        switch (type) {
            case 'alert':
                return <AlertCircle className="h-4 w-4 text-red-500" />;
            case 'info':
                return <Info className="h-4 w-4 text-blue-500" />;
            case 'reminder':
                return <Clock className="h-4 w-4 text-yellow-500" />;
            case 'system':
                return <Bell className="h-4 w-4 text-green-500" />;
            default:
                return <Bell className="h-4 w-4 text-gray-500" />;
        }
    };

    const getPriorityBadge = (priority: string) => {
        switch (priority) {
            case 'urgent':
                return <Badge variant="destructive">Urgent</Badge>;
            case 'high':
                return <Badge variant="destructive">High</Badge>;
            case 'normal':
                return <Badge variant="default">Normal</Badge>;
            case 'low':
                return <Badge variant="secondary">Low</Badge>;
            default:
                return <Badge variant="outline">{priority}</Badge>;
        }
    };

    const getTypeBadge = (type: string) => {
        switch (type) {
            case 'alert':
                return <Badge variant="destructive">Alert</Badge>;
            case 'info':
                return <Badge variant="default">Info</Badge>;
            case 'reminder':
                return <Badge variant="outline">Reminder</Badge>;
            case 'system':
                return <Badge variant="secondary">System</Badge>;
            default:
                return <Badge variant="outline">{type}</Badge>;
        }
    };

    const handleMarkAsRead = async (notificationId: number) => {
        if (!notificationId) return;
        setIsLoading(true);
        try {
            await router.post(`/notifications/${notificationId}/read`, {}, {
                preserveState: true,
                onSuccess: () => setIsLoading(false),
                onError: () => setIsLoading(false)
            });
        } catch (error) {
            setIsLoading(false);
        }
    };

    const handleMarkAsUnread = async (notificationId: number) => {
        if (!notificationId) return;
        setIsLoading(true);
        try {
            await router.post(`/notifications/${notificationId}/unread`, {}, {
                preserveState: true,
                onSuccess: () => setIsLoading(false),
                onError: () => setIsLoading(false)
            });
        } catch (error) {
            setIsLoading(false);
        }
    };

    const handleMarkAllAsRead = async () => {
        setIsLoading(true);
        try {
            await router.post('/notifications/mark-all-read', {}, {
                preserveState: true,
                onSuccess: () => setIsLoading(false),
                onError: () => setIsLoading(false)
            });
        } catch (error) {
            setIsLoading(false);
        }
    };

    const handleDelete = async (notificationId: number) => {
        if (!notificationId) return;
        setIsLoading(true);
        try {
            await router.delete(`/notifications/${notificationId}`, {
                preserveState: true,
                onSuccess: () => setIsLoading(false),
                onError: () => setIsLoading(false)
            });
        } catch (error) {
            setIsLoading(false);
        }
    };

    const handleFilterChange = (filterType: string, value: string) => {
        const newFilters = { ...filters, [filterType]: value };
        router.get('/notifications', newFilters, {
            preserveState: true,
            replace: true
        });
    };

    const handleRefresh = () => {
        router.reload();
    };

    return (
        <AppLayout>
            <Head title="Notifications" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">Notifications</h1>
                        <p className="text-muted-foreground">
                            Manage system notifications and alerts
                        </p>
                    </div>
                    <div className="flex items-center space-x-2">
                        <Button variant="outline" onClick={handleRefresh} disabled={isLoading}>
                            <RefreshCw className={`mr-2 h-4 w-4 ${isLoading ? 'animate-spin' : ''}`} />
                            Refresh
                        </Button>
                        <Button onClick={handleMarkAllAsRead} disabled={isLoading || !stats?.unread}>
                            <Check className="mr-2 h-4 w-4" />
                            Mark All Read
                        </Button>
                    </div>
                </div>

                {/* Filters */}
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center">
                            <Filter className="mr-2 h-5 w-5" />
                            Filter Notifications
                        </CardTitle>
                        <CardDescription>
                            Filter notifications by type, category, priority, or status
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <div>
                                <label className="text-sm font-medium mb-2 block">Type</label>
                                <Select value={filters?.type || 'all'} onValueChange={(value) => handleFilterChange('type', value)}>
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select type" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {Object.entries(filterOptions?.types || {}).map(([value, label]) => (
                                            <SelectItem key={value} value={value}>{label}</SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                            <div>
                                <label className="text-sm font-medium mb-2 block">Category</label>
                                <Select value={filters?.category || 'all'} onValueChange={(value) => handleFilterChange('category', value)}>
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select category" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {Object.entries(filterOptions?.categories || {}).map(([value, label]) => (
                                            <SelectItem key={value} value={value}>{label}</SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                            <div>
                                <label className="text-sm font-medium mb-2 block">Priority</label>
                                <Select value={filters?.priority || 'all'} onValueChange={(value) => handleFilterChange('priority', value)}>
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select priority" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {Object.entries(filterOptions?.priorities || {}).map(([value, label]) => (
                                            <SelectItem key={value} value={value}>{label}</SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                            <div>
                                <label className="text-sm font-medium mb-2 block">Status</label>
                                <Select value={filters?.status || 'all'} onValueChange={(value) => handleFilterChange('status', value)}>
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select status" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="all">All Status</SelectItem>
                                        <SelectItem value="unread">Unread</SelectItem>
                                        <SelectItem value="read">Read</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div className="flex items-end">
                                <Button 
                                    variant="outline" 
                                    onClick={() => {
                                        router.get('/notifications', {}, {
                                            preserveState: true,
                                            replace: true
                                        });
                                    }}
                                    className="w-full"
                                >
                                    Clear Filters
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Notifications Table */}
                <Card>
                    <CardHeader>
                        <CardTitle>Notifications</CardTitle>
                        <CardDescription>
                            {notifications?.total || 0} notifications found
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        {notifications?.data && notifications.data.length > 0 ? (
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Type</TableHead>
                                        <TableHead>Title</TableHead>
                                        <TableHead>Priority</TableHead>
                                        <TableHead>Status</TableHead>
                                        <TableHead>Date</TableHead>
                                        <TableHead className="text-right">Actions</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {notifications.data.map((notification) => (
                                        <TableRow 
                                            key={notification.id} 
                                            className={notification.is_read ? 'opacity-60' : 'font-medium'}
                                        >
                                            <TableCell>
                                                <div className="flex items-center space-x-2">
                                                    {getTypeIcon(notification.type)}
                                                    {getTypeBadge(notification.type)}
                                                </div>
                                            </TableCell>
                                            <TableCell>
                                                <div>
                                                    <div className="font-medium">{notification.title}</div>
                                                    <div className="text-sm text-muted-foreground mt-1">
                                                        {notification.message}
                                                    </div>
                                                    {notification.action_url && (
                                                        <div className="mt-2">
                                                            <Link 
                                                                href={notification.action_url}
                                                                className="text-sm text-blue-600 hover:text-blue-800 flex items-center"
                                                            >
                                                                <ExternalLink className="h-3 w-3 mr-1" />
                                                                {notification.action_text || 'View Details'}
                                                            </Link>
                                                        </div>
                                                    )}
                                                </div>
                                            </TableCell>
                                            <TableCell>{getPriorityBadge(notification.priority)}</TableCell>
                                            <TableCell>
                                                <Badge variant={notification.is_read ? "secondary" : "default"}>
                                                    {notification.is_read ? 'Read' : 'Unread'}
                                                </Badge>
                                            </TableCell>
                                            <TableCell>
                                                <div className="text-sm">
                                                    {formatDate(notification.created_at)}
                                                </div>
                                            </TableCell>
                                            <TableCell className="text-right">
                                                <div className="flex items-center justify-end space-x-2">
                                                    {notification.is_read ? (
                                                        <Button 
                                                            variant="outline" 
                                                            size="sm"
                                                            onClick={() => handleMarkAsUnread(notification.id)}
                                                            disabled={isLoading}
                                                        >
                                                            <EyeOff className="h-4 w-4" />
                                                        </Button>
                                                    ) : (
                                                        <Button 
                                                            variant="outline" 
                                                            size="sm"
                                                            onClick={() => handleMarkAsRead(notification.id)}
                                                            disabled={isLoading}
                                                        >
                                                            <Eye className="h-4 w-4" />
                                                        </Button>
                                                    )}
                                                    <Button 
                                                        variant="outline" 
                                                        size="sm"
                                                        onClick={() => handleDelete(notification.id)}
                                                        disabled={isLoading}
                                                    >
                                                        <Trash2 className="h-4 w-4" />
                                                    </Button>
                                                </div>
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        ) : (
                            <div className="text-center py-8 text-muted-foreground">
                                <Bell className="mx-auto h-12 w-12 mb-4" />
                                <p>No notifications found</p>
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}