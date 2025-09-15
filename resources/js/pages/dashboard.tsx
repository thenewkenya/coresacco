import * as React from 'react';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Pagination, PaginationContent, PaginationItem, PaginationLink, PaginationNext, PaginationPrevious, PaginationEllipsis } from '@/components/ui/pagination';
import { 
    Users, 
    PiggyBank, 
    TrendingUp, 
    DollarSign, 
    ArrowUpRight, 
    ArrowDownRight,
    CreditCard,
    FileText,
    AlertCircle,
    Clock,
    Wallet
} from 'lucide-react';
// Removed heavy chart imports - using CSS-based visualizations instead

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

interface Stats {
    total_members: number;
    total_accounts: number;
    total_savings: number;
    total_shares: number;
    active_loans: number;
    total_loan_amount: number;
    monthly_transactions: number;
    monthly_transaction_amount: number;
    savings_growth: number;
    total_balance?: number;
    total_loan_outstanding?: number;
}

interface TransactionTrend {
    date: string;
    deposits: number;
    withdrawals: number;
}

interface RecentTransaction {
    id: number;
    type: string;
    amount: number;
    description: string;
    created_at: string;
    account: {
        account_number: string;
        member: {
            name: string;
        };
    };
}

interface Account {
    id: number;
    account_number: string;
    account_type: string;
    balance: number;
    status: string;
}

interface LoanAccount {
    id: number;
    account_number: string;
    loan_type: string;
    outstanding_principal: number;
    monthly_payment: number;
    next_payment_date: string;
    status: string;
    arrears_amount: number;
    arrears_days: number;
}

interface PendingLoan {
    id: number;
    amount: number;
    loan_type: {
        name: string;
    };
    created_at: string;
}

interface AccountBalance {
    type: string;
    balance: number;
    count: number;
}

interface BudgetItem {
    id: number;
    category: string;
    amount: number;
    is_recurring: boolean;
}

interface Budget {
    id: number;
    month: number;
    year: number;
    total_income: number;
    total_expenses: number;
    savings_target: number;
    notes?: string;
    status: string;
    items: BudgetItem[];
}

interface Props {
    userRole: 'member' | 'admin';
    stats: Stats;
    transaction_trends: TransactionTrend[];
    recent_transactions: RecentTransaction[] | {
        data: RecentTransaction[];
        links: any[];
        total: number;
        per_page: number;
        current_page: number;
        last_page: number;
        from: number;
        to: number;
    };
    account_balances?: AccountBalance[];
    loan_accounts?: LoanAccount[];
    pending_loans?: PendingLoan[];
    current_budget?: Budget | null;
    monthly_expenses?: number;
}

// Utility functions moved outside components to prevent recreation
const formatDate = (dateString: string) => 
    new Date(dateString).toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric' 
    });

const formatDateKE = (date: string) => {
    return new Date(date).toLocaleDateString('en-KE', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

const formatCurrencyUtil = (amount: number) => {
    return new Intl.NumberFormat('en-KE', {
        style: 'currency',
        currency: 'KES',
    }).format(amount);
};

const formatCurrencyShort = (value: number) => `KES ${value.toLocaleString()}`;

// Memoized components for better performance
const StatsCard = React.memo(({ 
    title, 
    value, 
    icon: Icon, 
    description, 
    trend 
}: { 
    title: string; 
    value: string | number; 
    icon: React.ComponentType<any>; 
    description: string;
    trend?: { value: number; isPositive: boolean };
}) => (
    <Card>
        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">{title}</CardTitle>
            <Icon className="h-4 w-4 text-muted-foreground" />
        </CardHeader>
        <CardContent>
            <div className="text-2xl font-bold">{value}</div>
            <p className={`text-xs flex items-center ${
                trend ? (trend.isPositive ? 'text-green-600' : 'text-red-600') : 'text-muted-foreground'
            }`}>
                {trend && (
                    trend.isPositive ? (
                        <ArrowUpRight className="h-3 w-3 mr-1" />
                    ) : (
                        <ArrowDownRight className="h-3 w-3 mr-1" />
                    )
                )}
                {trend ? `${Math.abs(trend.value).toFixed(1)}% from last month` : description}
            </p>
        </CardContent>
    </Card>
));

const SimpleChart = React.memo(({ 
    monthlyTransactions, 
    monthlyAmount, 
    formatCurrency 
}: { 
    monthlyTransactions: number; 
    monthlyAmount: number; 
    formatCurrency: (amount: number) => string;
}) => {
    // Generate consistent data based on actual stats
    const chartData = React.useMemo(() => {
        const baseValue = monthlyTransactions / 7; // Average daily transactions
        return Array.from({ length: 7 }, (_, i) => {
            const day = new Date();
            day.setDate(day.getDate() - (6 - i));
            const dayName = day.toLocaleDateString('en-KE', { weekday: 'short' });
            // Add some variation but keep it realistic
            const variation = (Math.sin(i * 0.8) * 0.3 + 0.7) * 100;
            const value = Math.max(10, Math.floor(baseValue * variation / 100));
            
            return {
                day: dayName,
                value,
                percentage: Math.min(100, Math.max(10, value * 100 / Math.max(baseValue, 1)))
            };
        });
    }, [monthlyTransactions]);

    return (
        <div className="space-y-4">
            {/* Simple bar chart using CSS */}
            <div className="space-y-2">
                <h4 className="text-sm font-medium">Last 7 Days Activity</h4>
                <div className="space-y-1">
                    {chartData.map((item, i) => (
                        <div key={i} className="flex items-center space-x-2">
                            <div className="w-12 text-xs text-muted-foreground">
                                {item.day}
                            </div>
                            <div className="flex-1 bg-gray-200 rounded-full h-2">
                                <div 
                                    className="h-2 rounded-full transition-all duration-300"
                                    style={{ 
                                        width: `${item.percentage}%`,
                                        backgroundColor: 'var(--color-chart-1)'
                                    }}
                                />
                            </div>
                            <div className="w-16 text-xs text-right text-muted-foreground">
                                {item.value}
                            </div>
                        </div>
                    ))}
                </div>
            </div>
            
            {/* Summary metrics */}
            <div className="grid grid-cols-2 gap-4 pt-4 border-t">
                <div className="text-center">
                    <div className="text-lg font-semibold text-green-600">
                        {formatCurrency(monthlyAmount)}
                    </div>
                    <div className="text-xs text-muted-foreground">This Month</div>
                </div>
                <div className="text-center">
                    <div className="text-lg font-semibold text-blue-600">
                        {monthlyTransactions}
                    </div>
                    <div className="text-xs text-muted-foreground">Transactions</div>
                </div>
            </div>
        </div>
    );
});

// Ultra-lightweight CSS-based Transaction Trends
const TransactionTrendsChart = React.memo(({ data }: { data: TransactionTrend[] }) => {
    const chartData = React.useMemo(() => {
        if (!data || data.length === 0) return [];
        
        // Take only last 7 points
        const limitedData = data.slice(-7);
        const maxValue = Math.max(...limitedData.flatMap(d => [d.deposits, d.withdrawals]));
        
        return limitedData.map(item => ({
            date: new Date(item.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }),
            deposits: item.deposits,
            withdrawals: item.withdrawals,
            depositsHeight: maxValue > 0 ? (item.deposits / maxValue) * 100 : 0,
            withdrawalsHeight: maxValue > 0 ? (item.withdrawals / maxValue) * 100 : 0
        }));
    }, [data]);

    if (!chartData || chartData.length === 0) {
        return (
            <div className="flex items-center justify-center h-[200px] text-muted-foreground">
                <div className="text-center">
                    <TrendingUp className="mx-auto h-6 w-6 mb-2" />
                    <p className="text-xs">No transaction data available</p>
                </div>
            </div>
        );
    }

    return (
        <div className="h-[200px] w-full">
            <div className="flex items-end justify-between h-full space-x-1">
                {chartData.map((item, index) => (
                    <div key={index} className="flex-1 flex flex-col items-center space-y-1">
                        <div className="flex flex-col items-center space-y-1 w-full">
                            {/* Deposits Bar */}
                            <div 
                                className="w-full rounded-t-sm transition-all duration-300 hover:opacity-80"
                                style={{ 
                                    height: `${Math.max(item.depositsHeight, 2)}px`,
                                    backgroundColor: 'var(--color-chart-1)'
                                }}
                                title={`Deposits: ${formatCurrencyShort(item.deposits)}`}
                            />
                            {/* Withdrawals Bar */}
                            <div 
                                className="w-full rounded-b-sm transition-all duration-300 hover:opacity-80"
                                style={{ 
                                    height: `${Math.max(item.withdrawalsHeight, 2)}px`,
                                    backgroundColor: 'var(--color-chart-2)'
                                }}
                                title={`Withdrawals: ${formatCurrencyShort(item.withdrawals)}`}
                            />
                        </div>
                        <div className="text-xs text-muted-foreground text-center">
                            {item.date}
                        </div>
                    </div>
                ))}
            </div>
            {/* Legend */}
            <div className="flex justify-center space-x-4 mt-2 text-xs">
                <div className="flex items-center space-x-1">
                    <div 
                        className="w-3 h-3 rounded"
                        style={{ backgroundColor: 'var(--color-chart-1)' }}
                    ></div>
                    <span>Deposits</span>
                </div>
                <div className="flex items-center space-x-1">
                    <div 
                        className="w-3 h-3 rounded"
                        style={{ backgroundColor: 'var(--color-chart-2)' }}
                    ></div>
                    <span>Withdrawals</span>
                </div>
            </div>
        </div>
    );
});

// Comprehensive Account Distribution Chart - Shows all account types
const AccountDistributionChart = React.memo(({ accountBalances, totalBalance }: { accountBalances: AccountBalance[]; totalBalance: number }) => {
    const chartData = React.useMemo(() => {
        if (!accountBalances || accountBalances.length === 0) return [];
        
        const chartColors = [
            'var(--color-chart-1)',
            'var(--color-chart-2)', 
            'var(--color-chart-3)',
            'var(--color-chart-4)',
            'var(--color-chart-5)',
            'var(--color-chart-1)', // Cycle back for more than 5 types
            'var(--color-chart-2)',
            'var(--color-chart-3)',
        ];
        
        return accountBalances.map((account, index) => ({
            name: account.type === 'loan_account' ? 'Loan Outstanding' : account.type.charAt(0).toUpperCase() + account.type.slice(1).replace('_', ' '),
            value: Math.round(account.balance),
            percentage: totalBalance > 0 ? Math.round((account.balance / totalBalance) * 100) : 0,
            color: chartColors[index % chartColors.length],
            count: account.count,
            isLoan: account.type === 'loan_account'
        })).filter(item => item.value > 0);
    }, [accountBalances, totalBalance]);

    if (chartData.length === 0) {
        return (
            <div className="flex items-center justify-center h-[200px] text-muted-foreground">
                <div className="text-center">
                    <PiggyBank className="mx-auto h-6 w-6 mb-2" />
                    <p className="text-xs">No account data available</p>
                </div>
            </div>
        );
    }

    return (
        <div className="h-[200px] w-full flex flex-col">
            {/* Scrollable account list */}
            <div className="flex-1 overflow-y-auto space-y-3 pr-2">
                {chartData.map((item, index) => (
                    <div key={item.name} className="space-y-2">
                        <div className="flex justify-between text-sm">
                            <div className="flex items-center space-x-2">
                                <span className={`font-medium ${item.isLoan ? 'text-red-600' : ''}`}>
                                    {item.name}
                                </span>
                                {item.count > 1 && (
                                    <span className={`text-xs px-1.5 py-0.5 rounded ${
                                        item.isLoan 
                                            ? 'text-red-600 bg-red-50' 
                                            : 'text-muted-foreground bg-muted'
                                    }`}>
                                        {item.count} accounts
                                    </span>
                                )}
                            </div>
                            <span className={`text-muted-foreground ${item.isLoan ? 'text-red-600' : ''}`}>
                                {item.percentage}%
                            </span>
                        </div>
                        <div className="w-full bg-gray-200 rounded-full h-3">
                            <div 
                                className="h-3 rounded-full transition-all duration-500"
                                style={{ 
                                    width: `${item.percentage}%`,
                                    backgroundColor: item.color
                                }}
                            />
                        </div>
                        <div className={`text-xs ${item.isLoan ? 'text-red-600' : 'text-muted-foreground'}`}>
                            {item.isLoan ? `-${formatCurrencyShort(item.value)}` : formatCurrencyShort(item.value)}
                        </div>
                    </div>
                ))}
            </div>
            
            {/* Total summary - fixed at bottom */}
            <div className="pt-3 border-t mt-3 flex-shrink-0">
                <div className="flex justify-between items-center">
                    <span className="text-sm font-medium">Total Balance</span>
                    <span className="text-lg font-bold">
                        {formatCurrencyShort(totalBalance)}
                    </span>
                </div>
            </div>
        </div>
    );
});

// Simple chart wrapper - no complex loading states
const SimpleChartWrapper = React.memo(({ children }: { children: React.ReactNode }) => {
    return <>{children}</>;
});

export default function Dashboard({ 
    userRole,
    stats, 
    transaction_trends, 
    recent_transactions,
    account_balances = [],
    loan_accounts = [],
    pending_loans = [],
    current_budget = null,
    monthly_expenses = 0
}: Props) {
    // Memoize expensive calculations
    const formatCurrency = React.useCallback((amount: number) => {
        return formatCurrencyUtil(amount);
    }, []);

    const formatDate = React.useCallback((date: string) => {
        return formatDateKE(date);
    }, []);

    const getTransactionTypeVariant = React.useCallback((type: string) => {
        switch (type.toLowerCase()) {
            case 'deposit':
                return 'default';
            case 'withdrawal':
                return 'destructive';
            case 'transfer':
                return 'secondary';
            default:
                return 'outline';
        }
    }, []);

    // Memoize computed values
    const computedStats = React.useMemo(() => ({
        avgTransaction: stats.monthly_transactions > 0 
            ? stats.monthly_transaction_amount / stats.monthly_transactions
            : 0,
        systemHealth: stats.savings_growth >= 0 ? 'Good' : 'Needs Attention',
        systemHealthDesc: stats.savings_growth >= 0 ? 'Growing' : 'Declining',
        savingsTrend: {
            value: stats.savings_growth,
            isPositive: stats.savings_growth >= 0
        }
    }), [stats]);

    // Memoize recent transactions data for table
    const recentTransactionsData = React.useMemo(() => {
        if (Array.isArray(recent_transactions)) {
            return recent_transactions;
        }
        return recent_transactions?.data || [];
    }, [recent_transactions]);
    
    // Handle pagination for admin view
    const isPaginated = !Array.isArray(recent_transactions);


    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">Dashboard</h1>
                        <p className="text-muted-foreground">
                            Overview of your eSacco system
                        </p>
                    </div>
                    <div className="flex space-x-2">
                        <Link href="/transactions">
                            <Button variant="outline">
                                <FileText className="mr-2 h-4 w-4" />
                                View Transactions
                            </Button>
                        </Link>
                        <Link href="/transactions/create">
                            <Button>
                                <DollarSign className="mr-2 h-4 w-4" />
                                New Transaction
                            </Button>
                        </Link>
                    </div>
                </div>

                {/* Member Dashboard */}
                {userRole === 'member' && (
                    <>
                        {/* Financial Summary */}
                        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            <Card>
                                <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                                    <CardTitle className="text-sm font-medium">Total Balance</CardTitle>
                                    <Wallet className="h-4 w-4 text-muted-foreground" />
                                </CardHeader>
                                <CardContent>
                                    <div className="space-y-1 text-xs text-muted-foreground mb-3">
                                        <div className="flex justify-between">
                                            <span>Savings:</span>
                                            <span>{formatCurrency(stats.total_savings || 0)}</span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span>Shares:</span>
                                            <span>{formatCurrency(stats.total_shares || 0)}</span>
                                        </div>
                                    </div>
                                    <div className="text-2xl font-bold">{formatCurrency(stats.total_balance || 0)}</div>
                                </CardContent>
                            </Card>
                            {/* Enhanced Active Loans Card */}
                            <Card>
                                <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                                    <CardTitle className="text-sm font-medium">Active Loans</CardTitle>
                                    <CreditCard className="h-4 w-4 text-muted-foreground" />
                                </CardHeader>
                                <CardContent>
                                    {stats.active_loans > 0 ? (
                                        <>
                                            <div className="text-2xl font-bold mb-3">{stats.active_loans || 0}</div>
                                            <div className="space-y-2 text-xs text-muted-foreground mb-4">
                                                <div className="flex justify-between">
                                                    <span>Outstanding:</span>
                                                    <span className="font-medium">{formatCurrency(stats.total_loan_outstanding || 0)}</span>
                                                </div>
                                                {loan_accounts.length > 0 && (
                                                    <>
                                                        <div className="flex justify-between">
                                                            <span>Next Payment:</span>
                                                            <span className="font-medium">
                                                                {formatDate(loan_accounts[0]?.next_payment_date || '')}
                                                            </span>
                                                        </div>
                                                        <div className="flex justify-between">
                                                            <span>Monthly Payment:</span>
                                                            <span className="font-medium">
                                                                {formatCurrency(loan_accounts[0]?.monthly_payment || 0)}
                                                            </span>
                                                        </div>
                                                        {loan_accounts[0]?.arrears_amount > 0 && (
                                                            <div className="flex justify-between text-red-600">
                                                                <span>Overdue:</span>
                                                                <span className="font-medium">
                                                                    {formatCurrency(loan_accounts[0].arrears_amount)}
                                                                </span>
                                                            </div>
                                                        )}
                                                    </>
                                                )}
                                            </div>
                                            <Link href={`/loans/${loan_accounts[0]?.id || ''}`}>
                                                <Button className="w-full">
                                                    <DollarSign className="mr-2 h-4 w-4" />
                                                    View Loan Details
                                                </Button>
                                            </Link>
                                        </>
                                    ) : (
                                        <div className="space-y-4">
                                            <div className="text-center py-4">
                                                <CreditCard className="mx-auto h-8 w-8 mb-2 text-muted-foreground" />
                                                <p className="text-sm text-muted-foreground mb-2">No active loans</p>
                                                <p className="text-xs text-muted-foreground">
                                                    Apply for a loan to access funds for your needs
                                                </p>
                                            </div>
                                            <Link href="/loans/create">
                                                <Button className="w-full">
                                                    <CreditCard className="mr-2 h-4 w-4" />
                                                    Apply for Loan
                                                </Button>
                                            </Link>
                                        </div>
                                    )}
                                </CardContent>
                            </Card>

                            {/* Monthly Budget Card */}
                            <Card>
                                <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                                    <CardTitle className="text-sm font-medium">Monthly Budget</CardTitle>
                                    <TrendingUp className="h-4 w-4 text-muted-foreground" />
                                </CardHeader>
                                <CardContent>
                                    {current_budget ? (
                                        <div className="space-y-3">
                                            <div className="text-2xl font-bold mb-3">
                                                {formatCurrency(monthly_expenses)}
                                            </div>
                                            <div className="space-y-2 text-xs text-muted-foreground">
                                                <div className="flex justify-between">
                                                    <span>Budget:</span>
                                                    <span className="font-medium">{formatCurrency(current_budget.total_expenses)}</span>
                                                </div>
                                                <div className="flex justify-between">
                                                    <span>Remaining:</span>
                                                    <span className={`font-medium ${(current_budget.total_expenses - monthly_expenses) >= 0 ? 'text-green-600' : 'text-red-600'}`}>
                                                        {formatCurrency(current_budget.total_expenses - monthly_expenses)}
                                                    </span>
                                                </div>
                                                <div className="flex justify-between">
                                                    <span>Savings Target:</span>
                                                    <span className="font-medium">{formatCurrency(current_budget.savings_target)}</span>
                                                </div>
                                            </div>
                                            <div className="pt-2">
                                                <Link href="/savings/budget">
                                                    <Button className="w-full" variant="outline">
                                                        <TrendingUp className="mr-2 h-4 w-4" />
                                                        View Budget
                                                    </Button>
                                                </Link>
                                            </div>
                                        </div>
                                    ) : (
                                        <div className="space-y-3">
                                            <div className="text-center py-4">
                                                <TrendingUp className="mx-auto h-8 w-8 mb-2 text-muted-foreground" />
                                                <p className="text-sm text-muted-foreground mb-2">No budget for this month</p>
                                                <p className="text-xs text-muted-foreground">
                                                    Create a budget to track your expenses and savings goals
                                                </p>
                                            </div>
                                            <Link href="/savings/budget/create">
                                                <Button className="w-full">
                                                    <TrendingUp className="mr-2 h-4 w-4" />
                                                    Create Budget
                                                </Button>
                                            </Link>
                                        </div>
                                    )}
                                </CardContent>
                            </Card>
                        </div>

                        {/* Charts Section */}
                        <div className="grid gap-4 md:grid-cols-2">
                            {/* Transaction Trends Chart */}
                            <Card>
                                <CardHeader>
                                    <CardTitle>Transaction Trends</CardTitle>
                                    <CardDescription>Your deposits and withdrawals over the last 7 days</CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <SimpleChartWrapper>
                                        <TransactionTrendsChart data={transaction_trends} />
                                    </SimpleChartWrapper>
                                </CardContent>
                            </Card>

                            {/* Account Balance Distribution */}
                            <Card>
                                <CardHeader>
                                    <CardTitle>Account Distribution</CardTitle>
                                    <CardDescription>Breakdown of your account balances</CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <SimpleChartWrapper>
                                        <AccountDistributionChart 
                                            accountBalances={account_balances}
                                            totalBalance={stats.total_balance || 0}
                                        />
                                    </SimpleChartWrapper>
                                </CardContent>
                            </Card>
                        </div>

                        {/* Recent Transactions Table */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center space-x-2">
                                    <FileText className="h-5 w-5" />
                                    <span>Recent Transactions</span>
                                </CardTitle>
                                <CardDescription>
                                    Your latest transaction activity
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                {recentTransactionsData.length > 0 ? (
                                    <div className="space-y-4">
                                        <Table>
                                            <TableHeader>
                                                <TableRow>
                                                    <TableHead>Description</TableHead>
                                                    <TableHead>Type</TableHead>
                                                    <TableHead className="text-right">Amount</TableHead>
                                                    <TableHead className="text-right">Date</TableHead>
                                                </TableRow>
                                            </TableHeader>
                                            <TableBody>
                                                {recentTransactionsData.map((transaction) => (
                                                    <TableRow key={transaction.id}>
                                                        <TableCell className="font-medium">
                                                            {transaction.description}
                                                        </TableCell>
                                                        <TableCell>
                                                            <Badge variant={getTransactionTypeVariant(transaction.type)}>
                                                                {transaction.type}
                                                            </Badge>
                                                        </TableCell>
                                                        <TableCell className="text-right">
                                                            <span className={`font-medium ${
                                                                transaction.type === 'deposit' ? 'text-green-600' : 'text-red-600'
                                                            }`}>
                                                                {transaction.type === 'deposit' ? '+' : '-'}{formatCurrency(transaction.amount)}
                                                            </span>
                                                        </TableCell>
                                                        <TableCell className="text-right text-muted-foreground">
                                                            {formatDate(transaction.created_at)}
                                                        </TableCell>
                                                    </TableRow>
                                                ))}
                                            </TableBody>
                                        </Table>
                                    </div>
                                ) : (
                                    <div className="text-center py-6 text-muted-foreground">
                                        <FileText className="mx-auto h-8 w-8 mb-2" />
                                        <p className="text-sm">No recent transactions</p>
                                    </div>
                                )}
                            </CardContent>
                        </Card>

                        {/* Pending Loan Applications */}
                        {pending_loans.length > 0 && (
                            <Card>
                                <CardHeader>
                                    <CardTitle>Pending Loan Applications</CardTitle>
                                    <CardDescription>
                                        Your loan applications under review
                                    </CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <div className="space-y-4">
                                        {pending_loans.map((loan) => (
                                            <div key={loan.id} className="flex items-center justify-between p-4 border rounded-lg">
                                                <div className="flex items-center space-x-4">
                                                    <div className="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                                                        <Clock className="h-5 w-5 text-yellow-600" />
                                                    </div>
                                                    <div>
                                                        <h3 className="font-semibold">{loan.loan_type.name}</h3>
                                                        <p className="text-sm text-muted-foreground">
                                                            Applied {formatDate(loan.created_at)}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div className="text-right">
                                                    <p className="font-medium">{formatCurrency(loan.amount)}</p>
                                                    <Badge variant="outline">Under Review</Badge>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                </CardContent>
                            </Card>
                        )}
                    </>
                )}

                {/* Admin Dashboard */}
                {userRole === 'admin' && (
                    <>
                        {/* Key Stats Cards - 4 cards */}
                        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                            <StatsCard
                                title="Total Members"
                                value={stats.total_members.toLocaleString()}
                                icon={Users}
                                description="Registered members"
                            />
                            <StatsCard
                                title="Total Savings"
                                value={formatCurrency(stats.total_savings)}
                                icon={PiggyBank}
                                description=""
                                trend={computedStats.savingsTrend}
                            />
                            <StatsCard
                                title="Active Loans"
                                value={stats.active_loans}
                                icon={CreditCard}
                                description={`${formatCurrency(stats.total_loan_amount)} total amount`}
                            />
                            <StatsCard
                                title="Monthly Transactions"
                                value={stats.monthly_transactions}
                                icon={TrendingUp}
                                description={`${formatCurrency(stats.monthly_transaction_amount)} this month`}
                            />
                        </div>

                        {/* Transaction Trends Chart */}
                        <Card>
                            <CardHeader>
                                <CardTitle>Transaction Trends</CardTitle>
                                <CardDescription>
                                    System-wide deposits and withdrawals over the last 7 days
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <SimpleChartWrapper>
                                    <TransactionTrendsChart data={transaction_trends} />
                                </SimpleChartWrapper>
                            </CardContent>
                        </Card>

                        {/* Recent Transactions */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center space-x-2">
                                    <FileText className="h-5 w-5" />
                                    <span>Recent Transactions</span>
                                </CardTitle>
                                <CardDescription>
                                    Latest transaction activity across the system
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                {recentTransactionsData.length > 0 ? (
                                    <div className="space-y-4">
                                        <Table>
                                            <TableHeader>
                                                <TableRow>
                                                    <TableHead>Description</TableHead>
                                                    <TableHead>Member</TableHead>
                                                    <TableHead>Type</TableHead>
                                                    <TableHead className="text-right">Amount</TableHead>
                                                    <TableHead className="text-right">Date</TableHead>
                                                </TableRow>
                                            </TableHeader>
                                            <TableBody>
                                                {recentTransactionsData.map((transaction) => (
                                                    <TableRow key={transaction.id}>
                                                        <TableCell className="font-medium">
                                                            {transaction.description}
                                                        </TableCell>
                                                        <TableCell>
                                                            <div>
                                                                <div className="font-medium">
                                                                    {transaction.account?.member?.name || 'Unknown Member'}
                                                                </div>
                                                                <div className="text-sm text-muted-foreground">
                                                                    {transaction.account?.account_number || 'N/A'}
                                                                </div>
                                                            </div>
                                                        </TableCell>
                                                        <TableCell>
                                                            <Badge variant={getTransactionTypeVariant(transaction.type)}>
                                                                {transaction.type}
                                                            </Badge>
                                                        </TableCell>
                                                        <TableCell className="text-right font-medium">
                                                            {formatCurrency(transaction.amount)}
                                                        </TableCell>
                                                        <TableCell className="text-right text-muted-foreground">
                                                            {formatDate(transaction.created_at)}
                                                        </TableCell>
                                                    </TableRow>
                                                ))}
                                            </TableBody>
                                        </Table>
                                        
                                        {/* Pagination - Only for admin view */}
                                        {isPaginated && recent_transactions && (
                                            <div className="pt-4 border-t">
                                                <Pagination>
                                                    <PaginationContent>
                                                        {/* Previous button */}
                                                        {recent_transactions.current_page > 1 && (
                                                            <PaginationItem>
                                                                <PaginationPrevious 
                                                                    href="#"
                                                                    size="default"
                                                                    onClick={(e) => {
                                                                        e.preventDefault();
                                                                        router.get('/', { page: recent_transactions.current_page - 1 });
                                                                    }}
                                                                />
                                                            </PaginationItem>
                                                        )}
                                                        
                                                        {/* Page numbers */}
                                                        {Array.from({ length: recent_transactions.last_page }, (_, i) => i + 1).map((page) => (
                                                            <PaginationItem key={page}>
                                                                <PaginationLink
                                                                    href="#"
                                                                    size="icon"
                                                                    isActive={page === recent_transactions.current_page}
                                                                    onClick={(e) => {
                                                                        e.preventDefault();
                                                                        router.get('/', { page });
                                                                    }}
                                                                >
                                                                    {page}
                                                                </PaginationLink>
                                                            </PaginationItem>
                                                        ))}
                                                        
                                                        {/* Next button */}
                                                        {recent_transactions.current_page < recent_transactions.last_page && (
                                                            <PaginationItem>
                                                                <PaginationNext 
                                                                    href="#"
                                                                    size="default"
                                                                    onClick={(e) => {
                                                                        e.preventDefault();
                                                                        router.get('/', { page: recent_transactions.current_page + 1 });
                                                                    }}
                                                                />
                                                            </PaginationItem>
                                                        )}
                                                    </PaginationContent>
                                                </Pagination>
                                            </div>
                                        )}
                                    </div>
                                ) : (
                                    <div className="text-center py-6 text-muted-foreground">
                                        <FileText className="mx-auto h-8 w-8 mb-2" />
                                        <p className="text-sm">No recent transactions</p>
                </div>
                                )}
                            </CardContent>
                        </Card>
                    </>
                )}

            </div>
        </AppLayout>
    );
}