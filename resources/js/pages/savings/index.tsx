import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { 
    Plus, 
    PiggyBank, 
    Target, 
    TrendingUp, 
    DollarSign,
    Search,
    Eye,
    Filter,
    Calendar,
    User,
    CreditCard,
    BarChart3
} from 'lucide-react';
import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { index as savingsIndex, my as savingsMy, goals as savingsGoals, budget as savingsBudget } from '@/routes/savings';

interface Account {
    id: number;
    account_number: string;
    account_type: string;
    balance: number;
    status: string;
    created_at: string;
    member: {
        id: number;
        name: string;
        member_number: string;
    };
}

interface Props {
    accounts: {
        data: Account[];
        links: any[];
        total: number;
        per_page: number;
        current_page: number;
        last_page: number;
        from: number;
        to: number;
    };
    stats: {
        totalAccounts: number;
        activeAccounts: number;
        totalBalance: number;
        thisMonthDeposits: number;
        totalGoals: number;
        activeGoals: number;
        completedGoals: number;
        totalGoalAmount: number;
        totalGoalProgress: number;
    };
    filters: {
        search?: string;
        status?: string;
        account_type?: string;
    };
    accountTypes: string[];
    statusOptions: Record<string, string>;
}

export default function SavingsIndex({ accounts, stats, filters, accountTypes, statusOptions }: Props) {
    const [search, setSearch] = useState(filters.search || '');
    const [status, setStatus] = useState(filters.status || 'all');
    const [accountType, setAccountType] = useState(filters.account_type || 'all');

    const handleSearch = () => {
        router.get(savingsIndex.url(), {
            search: search || undefined,
            status: status === 'all' ? undefined : status,
            account_type: accountType === 'all' ? undefined : accountType,
        }, {
            preserveState: true,
            replace: true,
        });
    };

    const getStatusBadge = (status: string) => {
        switch (status) {
            case 'active':
                return <Badge variant="default">Active</Badge>;
            case 'inactive':
                return <Badge variant="outline">Inactive</Badge>;
            case 'frozen':
                return <Badge variant="destructive">Frozen</Badge>;
            case 'closed':
                return <Badge variant="destructive">Closed</Badge>;
            default:
                return <Badge variant="outline">{status}</Badge>;
        }
    };

    const getAccountTypeBadge = (type: string) => {
        switch (type) {
            case 'savings':
                return <Badge variant="secondary">Savings</Badge>;
            case 'shares':
                return <Badge variant="default">Shares</Badge>;
            default:
                return <Badge variant="outline">{type}</Badge>;
        }
    };

    const formatCurrency = (amount: number) => {
        return new Intl.NumberFormat('en-KE', {
            style: 'currency',
            currency: 'KES',
        }).format(amount);
    };

    const formatDate = (date: string) => {
        return new Date(date).toLocaleDateString('en-KE', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        });
    };

    const goalProgressPercentage = stats.totalGoalAmount > 0 ? (stats.totalGoalProgress / stats.totalGoalAmount) * 100 : 0;

    return (
        <AppLayout>
            <Head title="Savings Management" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">Savings Management</h1>
                        <p className="text-muted-foreground">
                            Manage savings accounts, goals, and budget planning
                        </p>
                    </div>
                    <div className="flex space-x-2">
                        <Link href={savingsGoals.url()}>
                            <Button variant="outline">
                                <Target className="mr-2 h-4 w-4" />
                                View Goals
                            </Button>
                        </Link>
                        <Link href={savingsBudget.url()}>
                            <Button variant="outline">
                                <BarChart3 className="mr-2 h-4 w-4" />
                                Budget Planning
                            </Button>
                        </Link>
                    </div>
                </div>

                {/* Stats Cards */}
                <div className="grid auto-rows-min gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Savings</CardTitle>
                            <PiggyBank className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{formatCurrency(stats.totalBalance)}</div>
                            <p className="text-xs text-muted-foreground">
                                {stats.activeAccounts} active accounts
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Active Goals</CardTitle>
                            <Target className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.activeGoals}</div>
                            <p className="text-xs text-muted-foreground">
                                {stats.completedGoals} completed
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Monthly Deposits</CardTitle>
                            <TrendingUp className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{formatCurrency(stats.thisMonthDeposits)}</div>
                            <p className="text-xs text-muted-foreground">
                                This month
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Goal Progress</CardTitle>
                            <DollarSign className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{goalProgressPercentage.toFixed(1)}%</div>
                            <p className="text-xs text-muted-foreground">
                                {formatCurrency(stats.totalGoalProgress)} of {formatCurrency(stats.totalGoalAmount)}
                            </p>
                        </CardContent>
                    </Card>
                </div>

                {/* Search and Filters */}
                <Card>
                    <CardHeader>
                        <CardTitle>Search Savings Accounts</CardTitle>
                        <CardDescription>
                            Find savings accounts by member name or account number
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="flex items-center space-x-2">
                            <div className="relative flex-1">
                                <Search className="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                                <Input 
                                    placeholder="Search accounts..." 
                                    className="pl-8"
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                    onKeyPress={(e) => e.key === 'Enter' && handleSearch()}
                                />
                            </div>
                            <Select value={status} onValueChange={setStatus}>
                                <SelectTrigger className="w-[180px]">
                                    <SelectValue placeholder="Status" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">All Status</SelectItem>
                                    {Object.entries(statusOptions).map(([value, label]) => (
                                        <SelectItem key={value} value={value}>
                                            {label}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            <Select value={accountType} onValueChange={setAccountType}>
                                <SelectTrigger className="w-[180px]">
                                    <SelectValue placeholder="Account Type" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">All Types</SelectItem>
                                    {accountTypes.map((type) => (
                                        <SelectItem key={type} value={type}>
                                            {type.charAt(0).toUpperCase() + type.slice(1)}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            <Button onClick={handleSearch}>
                                <Filter className="mr-2 h-4 w-4" />
                                Filter
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                {/* Savings Accounts Table */}
                <Card>
                    <CardHeader>
                        <CardTitle>Savings Accounts</CardTitle>
                        <CardDescription>
                            All savings and shares accounts ({accounts.total} total)
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        {accounts.data.length > 0 ? (
                            <div className="space-y-4">
                                {accounts.data.map((account) => (
                                    <div key={account.id} className="flex items-center justify-between p-4 border rounded-lg">
                                        <div className="flex items-center space-x-4">
                                            <div className="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center">
                                                <PiggyBank className="h-5 w-5 text-primary" />
                                            </div>
                                            <div>
                                                <h3 className="font-semibold">{account.account_number}</h3>
                                                <p className="text-sm text-muted-foreground">
                                                    {account.member.name} • {account.member.member_number}
                                                </p>
                                                <p className="text-sm text-muted-foreground">
                                                    Created {formatDate(account.created_at)}
                                                </p>
                                            </div>
                                        </div>
                                        <div className="flex items-center space-x-4">
                                            <div className="text-right">
                                                <p className="font-medium">{formatCurrency(account.balance)}</p>
                                                <div className="flex items-center space-x-2 mt-1">
                                                    {getAccountTypeBadge(account.account_type)}
                                                    {getStatusBadge(account.status)}
                                                </div>
                                            </div>
                                            <div className="flex items-center space-x-2">
                                                <Link href={`/accounts/${account.id}`}>
                                                    <Button variant="outline" size="sm">
                                                        <Eye className="mr-2 h-4 w-4" />
                                                        View Details
                                                    </Button>
                                                </Link>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <div className="text-center py-8 text-muted-foreground">
                                <PiggyBank className="mx-auto h-12 w-12 mb-4" />
                                <p>No savings accounts found</p>
                                <p className="text-sm">Accounts will appear here once they are created</p>
                            </div>
                        )}
                    </CardContent>
                </Card>

                {/* Quick Actions */}
                <div className="grid gap-4 md:grid-cols-3">
                    <Card className="cursor-pointer hover:shadow-md transition-shadow">
                        <Link href={savingsMy.url()}>
                            <CardHeader>
                                <CardTitle className="flex items-center space-x-2">
                                    <User className="h-5 w-5" />
                                    <span>My Savings</span>
                                </CardTitle>
                                <CardDescription>
                                    View your personal savings accounts and goals
                                </CardDescription>
                            </CardHeader>
                        </Link>
                    </Card>
                    <Card className="cursor-pointer hover:shadow-md transition-shadow">
                        <Link href={savingsGoals.url()}>
                            <CardHeader>
                                <CardTitle className="flex items-center space-x-2">
                                    <Target className="h-5 w-5" />
                                    <span>Savings Goals</span>
                                </CardTitle>
                                <CardDescription>
                                    Set and track your financial goals
                                </CardDescription>
                            </CardHeader>
                        </Link>
                    </Card>
                    <Card className="cursor-pointer hover:shadow-md transition-shadow">
                        <Link href={savingsBudget.url()}>
                            <CardHeader>
                                <CardTitle className="flex items-center space-x-2">
                                    <BarChart3 className="h-5 w-5" />
                                    <span>Budget Planning</span>
                                </CardTitle>
                                <CardDescription>
                                    Create and manage your monthly budgets
                                </CardDescription>
                            </CardHeader>
                        </Link>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}