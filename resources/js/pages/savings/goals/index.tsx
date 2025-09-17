import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';
import { index as savingsIndex } from '@/routes/savings';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Progress } from '@/components/ui/progress';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import {
    Plus,
    Search,
    Filter,
    Target,
    Calendar,
    DollarSign,
    TrendingUp,
    Users,
} from 'lucide-react';
import { goals as goalsRoute } from '@/routes/savings';
import { create as createGoalRoute } from '@/routes/savings/goals';

interface Goal {
    id: number;
    title: string;
    description: string;
    target_amount: number;
    current_amount: number;
    target_date: string;
    status: 'active' | 'completed' | 'paused' | 'cancelled';
    type: 'short_term' | 'medium_term' | 'long_term';
    auto_save_frequency: 'weekly' | 'monthly' | 'quarterly' | 'none';
    member: {
        id: number;
        name: string;
        member_number: string;
    };
    progress_percentage: number;
    created_at: string;
    updated_at: string;
}

interface Props {
    goals: {
        data: Goal[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        from: number;
        to: number;
    };
    stats: {
        totalGoals: number;
        activeGoals: number;
        completedGoals: number;
        totalTargetAmount: number;
        totalCurrentAmount: number;
    };
    filters: {
        search?: string;
        status?: string;
        type?: string;
    };
}

export default function GoalsIndex({ goals, stats, filters }: Props) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: dashboard().url,
        },
        {
            title: 'Savings',
            href: savingsIndex().url,
        },
        {
            title: 'Goals',
            href: '#',
        },
    ];

    const [search, setSearch] = useState(filters.search || '');
    const [status, setStatus] = useState(filters.status || 'all');
    const [type, setType] = useState(filters.type || 'all');

    const handleSearch = () => {
        router.get(goalsRoute.url(), {
            search: search || undefined,
            status: status === 'all' ? undefined : status,
            type: type === 'all' ? undefined : type,
        }, {
            preserveState: true,
            replace: true,
        });
    };

    const getStatusVariant = (status: string): "default" | "secondary" | "destructive" | "outline" => {
        switch (status) {
            case 'active': return 'default';
            case 'completed': return 'secondary';
            case 'paused': return 'outline';
            case 'cancelled': return 'destructive';
            default: return 'outline';
        }
    };

    const getTypeVariant = (type: string): "default" | "secondary" | "destructive" | "outline" => {
        switch (type) {
            case 'short_term': return 'default';
            case 'medium_term': return 'secondary';
            case 'long_term': return 'default';
            default: return 'outline';
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Savings Goals" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">Savings Goals</h1>
                        <p className="text-muted-foreground">
                            Track and manage savings goals for members
                        </p>
                    </div>
                    <Link href={createGoalRoute.url()}>
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            Create Goal
                        </Button>
                    </Link>
                </div>

                {/* Stats Cards */}
                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-5">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Goals</CardTitle>
                            <Target className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.totalGoals}</div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Active Goals</CardTitle>
                            <TrendingUp className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.activeGoals}</div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Completed</CardTitle>
                            <Calendar className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.completedGoals}</div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Target Amount</CardTitle>
                            <DollarSign className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">
                                KSh {stats.totalTargetAmount.toLocaleString()}
                            </div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Current Amount</CardTitle>
                            <Users className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">
                                KSh {stats.totalCurrentAmount.toLocaleString()}
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Filters */}
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center space-x-2">
                            <Filter className="h-5 w-5" />
                            <span>Filters</span>
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div className="grid gap-4 md:grid-cols-4">
                            <div className="space-y-2">
                                <label className="text-sm font-medium">Search</label>
                                <div className="flex space-x-2">
                                    <Input
                                        placeholder="Search goals or members..."
                                        value={search}
                                        onChange={(e) => setSearch(e.target.value)}
                                        onKeyPress={(e) => e.key === 'Enter' && handleSearch()}
                                    />
                                    <Button variant="outline" size="sm" onClick={handleSearch}>
                                        <Search className="h-4 w-4" />
                                    </Button>
                                </div>
                            </div>
                            <div className="space-y-2">
                                <label className="text-sm font-medium">Status</label>
                                <Select value={status} onValueChange={setStatus}>
                                    <SelectTrigger>
                                        <SelectValue placeholder="All Status" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="all">All Status</SelectItem>
                                        <SelectItem value="active">Active</SelectItem>
                                        <SelectItem value="completed">Completed</SelectItem>
                                        <SelectItem value="paused">Paused</SelectItem>
                                        <SelectItem value="cancelled">Cancelled</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div className="space-y-2">
                                <label className="text-sm font-medium">Type</label>
                                <Select value={type} onValueChange={setType}>
                                    <SelectTrigger>
                                        <SelectValue placeholder="All Types" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="all">All Types</SelectItem>
                                        <SelectItem value="short_term">Short Term</SelectItem>
                                        <SelectItem value="medium_term">Medium Term</SelectItem>
                                        <SelectItem value="long_term">Long Term</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div className="flex items-end">
                                <Button onClick={handleSearch} className="w-full">
                                    Apply Filters
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Goals Table */}
                <Card>
                    <CardHeader>
                        <CardTitle>Savings Goals</CardTitle>
                        <CardDescription>
                            Showing {goals.from} to {goals.to} of {goals.total} goals
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        {goals.data.length === 0 ? (
                            <div className="text-center py-8">
                                <Target className="mx-auto h-12 w-12 text-muted-foreground" />
                                <h3 className="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    No goals found
                                </h3>
                                <p className="mt-1 text-sm text-muted-foreground">
                                    Get started by creating a new savings goal.
                                </p>
                                <div className="mt-6">
                                    <Link href={createGoalRoute.url()}>
                                        <Button>
                                            <Plus className="mr-2 h-4 w-4" />
                                            Create Goal
                                        </Button>
                                    </Link>
                                </div>
                            </div>
                        ) : (
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Goal</TableHead>
                                        <TableHead>Member</TableHead>
                                        <TableHead>Target Amount</TableHead>
                                        <TableHead>Current Amount</TableHead>
                                        <TableHead>Progress</TableHead>
                                        <TableHead>Status</TableHead>
                                        <TableHead>Type</TableHead>
                                        <TableHead>Target Date</TableHead>
                                        <TableHead>Actions</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {goals.data.map((goal) => (
                                        <TableRow key={goal.id}>
                                            <TableCell>
                                                <div>
                                                    <div className="font-medium">{goal.title}</div>
                                                    <div className="text-sm text-muted-foreground">
                                                        {goal.description}
                                                    </div>
                                                </div>
                                            </TableCell>
                                            <TableCell>
                                                <div>
                                                    <div className="font-medium">{goal.member.name}</div>
                                                    <div className="text-sm text-muted-foreground">
                                                        {goal.member.member_number}
                                                    </div>
                                                </div>
                                            </TableCell>
                                            <TableCell>
                                                KSh {goal.target_amount.toLocaleString()}
                                            </TableCell>
                                            <TableCell>
                                                KSh {goal.current_amount.toLocaleString()}
                                            </TableCell>
                                            <TableCell>
                                                <div className="space-y-1">
                                                    <Progress value={goal.progress_percentage || 0} className="w-20" />
                                                    <div className="text-sm text-muted-foreground">
                                                        {(goal.progress_percentage || 0).toFixed(1)}%
                                                    </div>
                                                </div>
                                            </TableCell>
                                            <TableCell>
                                                <Badge variant={getStatusVariant(goal.status)}>
                                                    {goal.status.charAt(0).toUpperCase() + goal.status.slice(1)}
                                                </Badge>
                                            </TableCell>
                                            <TableCell>
                                                <Badge variant={getTypeVariant(goal.type)}>
                                                    {goal.type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}
                                                </Badge>
                                            </TableCell>
                                            <TableCell>
                                                {new Date(goal.target_date).toLocaleDateString()}
                                            </TableCell>
                                            <TableCell>
                                                <Link href={`/savings/goals/${goal.id}`}>
                                                    <Button variant="outline" size="sm">
                                                        View
                                                    </Button>
                                                </Link>
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        )}
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}

