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
    Calendar,
    DollarSign,
    TrendingUp,
    TrendingDown,
    PieChart,
} from 'lucide-react';
import { budget as budgetRoute } from '@/routes/savings';
import { create as createBudgetRoute } from '@/routes/savings/budget';

interface BudgetItem {
    id: number;
    category: string;
    budgeted_amount: number;
    spent_amount: number;
    remaining_amount: number;
    percentage_spent: number;
}

interface Budget {
    id: number;
    year: number;
    month: number;
    total_budgeted: number;
    total_spent: number;
    total_remaining: number;
    status: 'draft' | 'active' | 'completed' | 'cancelled';
    items: BudgetItem[];
    user: {
        id: number;
        name: string;
    };
    created_at: string;
    updated_at: string;
}

interface Props {
    budgets: {
        data: Budget[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        from: number;
        to: number;
    };
    stats: {
        totalBudgets: number;
        activeBudgets: number;
    };
    filters: {
        search?: string;
        year?: string;
        month?: string;
        status?: string;
    };
}

export default function BudgetIndex({ budgets, stats, filters }: Props) {
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
            title: 'Budget Planning',
            href: '#',
        },
    ];

    const [search, setSearch] = useState(filters.search || '');
    const [year, setYear] = useState(filters.year || 'all');
    const [month, setMonth] = useState(filters.month || 'all');
    const [status, setStatus] = useState(filters.status || 'all');

    const handleSearch = () => {
        router.get(budgetRoute.url(), {
            search: search || undefined,
            year: year === 'all' ? undefined : year,
            month: month === 'all' ? undefined : month,
            status: status === 'all' ? undefined : status,
        }, {
            preserveState: true,
            replace: true,
        });
    };

    const getStatusVariant = (status: string): "default" | "secondary" | "destructive" | "outline" => {
        switch (status) {
            case 'active': return 'default';
            case 'draft': return 'outline';
            case 'completed': return 'secondary';
            case 'cancelled': return 'destructive';
            default: return 'outline';
        }
    };

    const getMonthName = (month: number) => {
        const months = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];
        return months[month - 1] || 'Unknown';
    };

    const currentYear = new Date().getFullYear();
    const years = Array.from({ length: 5 }, (_, i) => currentYear - 2 + i);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Budget Planning" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">Budget Planning</h1>
                        <p className="text-muted-foreground">
                            Create and manage monthly budgets for members
                        </p>
                    </div>
                    <Button onClick={() => window.location.href = createBudgetRoute.url()}>
                        <Plus className="mr-2 h-4 w-4" />
                        Create Budget
                    </Button>
                </div>

                {/* Stats Cards */}
                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-5">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Budgets</CardTitle>
                            <Calendar className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.totalBudgets}</div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Active Budgets</CardTitle>
                            <TrendingUp className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.activeBudgets}</div>
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
                        <div className="grid gap-4 md:grid-cols-5">
                            <div className="space-y-2">
                                <label className="text-sm font-medium">Search</label>
                                <div className="flex space-x-2">
                                    <Input
                                        placeholder="Search budgets..."
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
                                <label className="text-sm font-medium">Year</label>
                                <Select value={year} onValueChange={setYear}>
                                    <SelectTrigger>
                                        <SelectValue placeholder="All Years" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="all">All Years</SelectItem>
                                        {years.map((year) => (
                                            <SelectItem key={year} value={year.toString()}>
                                                {year}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                            <div className="space-y-2">
                                <label className="text-sm font-medium">Month</label>
                                <Select value={month} onValueChange={setMonth}>
                                    <SelectTrigger>
                                        <SelectValue placeholder="All Months" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="all">All Months</SelectItem>
                                        {Array.from({ length: 12 }, (_, i) => i + 1).map((monthNum) => (
                                            <SelectItem key={monthNum} value={monthNum.toString()}>
                                                {getMonthName(monthNum)}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                            <div className="space-y-2">
                                <label className="text-sm font-medium">Status</label>
                                <Select value={status} onValueChange={setStatus}>
                                    <SelectTrigger>
                                        <SelectValue placeholder="All Status" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="all">All Status</SelectItem>
                                        <SelectItem value="draft">Draft</SelectItem>
                                        <SelectItem value="active">Active</SelectItem>
                                        <SelectItem value="completed">Completed</SelectItem>
                                        <SelectItem value="cancelled">Cancelled</SelectItem>
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

                {/* Budgets Table */}
                <Card>
                    <CardHeader>
                        <CardTitle>Budgets</CardTitle>
                        <CardDescription>
                            Showing {budgets.from} to {budgets.to} of {budgets.total} budgets
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        {budgets.data.length === 0 ? (
                            <div className="text-center py-8">
                                <Calendar className="mx-auto h-12 w-12 text-muted-foreground" />
                                <h3 className="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    No budgets found
                                </h3>
                                <p className="mt-1 text-sm text-muted-foreground">
                                    Get started by creating a new budget.
                                </p>
                                <div className="mt-6">
                                    <Button onClick={() => window.location.href = createBudgetRoute.url()}>
                                        <Plus className="mr-2 h-4 w-4" />
                                        Create Budget
                                    </Button>
                                </div>
                            </div>
                        ) : (
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Period</TableHead>
                                        <TableHead>Member</TableHead>
                                        <TableHead>Budgeted</TableHead>
                                        <TableHead>Spent</TableHead>
                                        <TableHead>Remaining</TableHead>
                                        <TableHead>Status</TableHead>
                                        <TableHead>Created</TableHead>
                                        <TableHead>Actions</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {budgets.data.map((budget) => (
                                        <TableRow key={budget.id}>
                                            <TableCell>
                                                <div className="font-medium">
                                                    {getMonthName(budget.month)} {budget.year}
                                                </div>
                                            </TableCell>
                                            <TableCell>
                                                <div className="font-medium">{budget.user?.name || 'Unknown User'}</div>
                                            </TableCell>
                                            <TableCell>
                                                KSh {(budget.total_budgeted || 0).toLocaleString()}
                                            </TableCell>
                                            <TableCell>
                                                KSh {(budget.total_spent || 0).toLocaleString()}
                                            </TableCell>
                                            <TableCell>
                                                <div className={`font-medium ${
                                                    (budget.total_remaining || 0) >= 0 
                                                        ? 'text-green-600 dark:text-green-400' 
                                                        : 'text-red-600 dark:text-red-400'
                                                }`}>
                                                    KSh {(budget.total_remaining || 0).toLocaleString()}
                                                </div>
                                            </TableCell>
                                            <TableCell>
                                                <Badge variant={getStatusVariant(budget.status)}>
                                                    {budget.status.charAt(0).toUpperCase() + budget.status.slice(1)}
                                                </Badge>
                                            </TableCell>
                                            <TableCell>
                                                {new Date(budget.created_at).toLocaleDateString()}
                                            </TableCell>
                                            <TableCell>
                                                <Link href={`/savings/budget/${budget.id}`}>
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

