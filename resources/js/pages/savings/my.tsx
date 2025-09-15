import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Progress } from '@/components/ui/progress';
import { 
    Plus, 
    PiggyBank, 
    Target, 
    TrendingUp, 
    DollarSign,
    Calendar,
    Eye,
    ArrowRight,
    BarChart3,
    Clock,
    CheckCircle,
    AlertCircle
} from 'lucide-react';
import { Head, Link, router } from '@inertiajs/react';
import { goals as savingsGoals, budget as savingsBudget } from '@/routes/savings';
import { create as createBudgetUrl } from '@/routes/savings/budget';
import { create as createGoalUrl } from '@/routes/savings/goals';

interface Account {
    id: number;
    account_number: string;
    account_type: string;
    balance: number;
    status: string;
    created_at: string;
    transactions: Transaction[];
}

interface Transaction {
    id: number;
    type: string;
    amount: number;
    description: string;
    created_at: string;
    balance_after: number;
}

interface Goal {
    id: number;
    title: string;
    target_amount: number;
    current_amount: number;
    target_date: string;
    type: string;
    status: string;
    progress_percentage: number;
}

interface Budget {
    id: number;
    month: number;
    year: number;
    total_income: number;
    total_expenses: number;
    savings_target: number;
    status: string;
    items: BudgetItem[];
}

interface BudgetItem {
    id: number;
    category: string;
    amount: number;
    description: string;
}

interface Props {
    accounts: Account[];
    goals: Goal[];
    budgets: Budget[];
    stats: {
        totalSavings: number;
        growthAmount: number;
        growthPercentage: number;
        activeGoals: number;
        completedGoals: number;
        totalGoalProgress: number;
        totalGoalTarget: number;
        goalProgressPercentage: number;
    };
}

export default function MySavings({ accounts, goals, budgets, stats }: Props) {
    const getStatusBadge = (status: string) => {
        switch (status) {
            case 'active':
                return <Badge variant="default">Active</Badge>;
            case 'completed':
                return <Badge variant="secondary">Completed</Badge>;
            case 'paused':
                return <Badge variant="outline">Paused</Badge>;
            case 'cancelled':
                return <Badge variant="destructive">Cancelled</Badge>;
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

    const getGoalTypeLabel = (type: string) => {
        switch (type) {
            case 'emergency_fund': return 'Emergency Fund';
            case 'home_purchase': return 'Home Purchase';
            case 'education': return 'Education';
            case 'retirement': return 'Retirement';
            case 'custom': return 'Custom';
            default: return type;
        }
    };

    const activeGoals = goals.filter(goal => goal.status === 'active');
    const recentBudgets = budgets.slice(0, 2);

    return (
        <AppLayout>
            <Head title="My Savings" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">My Savings</h1>
                        <p className="text-muted-foreground">
                            Manage your savings accounts, goals, and budget
                        </p>
                    </div>
                    <div className="flex space-x-2">
                        <Link href={createGoalUrl.url()}>
                            <Button variant="outline">
                                <Target className="mr-2 h-4 w-4" />
                                New Goal
                            </Button>
                        </Link>
                        <Link href={createBudgetUrl.url()}>
                            <Button>
                                <Plus className="mr-2 h-4 w-4" />
                                Create Budget
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
                            <div className="text-2xl font-bold">{formatCurrency(stats.totalSavings)}</div>
                            <p className={`text-xs ${stats.growthAmount >= 0 ? 'text-green-600' : 'text-red-600'}`}>
                                {stats.growthAmount >= 0 ? '+' : ''}{formatCurrency(stats.growthAmount)} ({stats.growthPercentage.toFixed(1)}%)
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
                            <CardTitle className="text-sm font-medium">Goal Progress</CardTitle>
                            <TrendingUp className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.goalProgressPercentage.toFixed(1)}%</div>
                            <p className="text-xs text-muted-foreground">
                                {formatCurrency(stats.totalGoalProgress)} of {formatCurrency(stats.totalGoalTarget)}
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Savings Accounts</CardTitle>
                            <DollarSign className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{accounts.length}</div>
                            <p className="text-xs text-muted-foreground">
                                Active accounts
                            </p>
                        </CardContent>
                    </Card>
                </div>

                <div className="grid gap-6 lg:grid-cols-2">
                    {/* Savings Accounts */}
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center space-x-2">
                                <PiggyBank className="h-5 w-5" />
                                <span>My Savings Accounts</span>
                            </CardTitle>
                            <CardDescription>
                                Your savings and shares accounts
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            {accounts.length > 0 ? (
                                <div className="space-y-4">
                                    {accounts.map((account) => (
                                        <div key={account.id} className="flex items-center justify-between p-3 border rounded-lg">
                                            <div className="flex items-center space-x-3">
                                                <div className="w-8 h-8 bg-primary/10 rounded-full flex items-center justify-center">
                                                    <PiggyBank className="h-4 w-4 text-primary" />
                                                </div>
                                                <div>
                                                    <h4 className="font-medium">{account.account_number}</h4>
                                                    <div className="flex items-center space-x-2 mt-1">
                                                        {getAccountTypeBadge(account.account_type)}
                                                        <Badge variant="default">Active</Badge>
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="text-right">
                                                <p className="font-medium">{formatCurrency(account.balance)}</p>
                                                <Link href={`/accounts/${account.id}`}>
                                                    <Button variant="outline" size="sm" className="mt-1">
                                                        <Eye className="mr-1 h-3 w-3" />
                                                        View
                                                    </Button>
                                                </Link>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <div className="text-center py-6 text-muted-foreground">
                                    <PiggyBank className="mx-auto h-8 w-8 mb-2" />
                                    <p className="text-sm">No savings accounts yet</p>
                                </div>
                            )}
                        </CardContent>
                    </Card>

                    {/* Active Goals */}
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center space-x-2">
                                <Target className="h-5 w-5" />
                                <span>Active Goals</span>
                            </CardTitle>
                            <CardDescription>
                                Your current savings goals
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            {activeGoals.length > 0 ? (
                                <div className="space-y-4">
                                    {activeGoals.slice(0, 3).map((goal) => (
                                        <div key={goal.id} className="space-y-2">
                                            <div className="flex items-center justify-between">
                                                <h4 className="font-medium text-sm">{goal.title}</h4>
                                                <span className="text-xs text-muted-foreground">
                                                    {getGoalTypeLabel(goal.type)}
                                                </span>
                                            </div>
                                            <div className="space-y-1">
                                                <div className="flex justify-between text-xs">
                                                    <span>{formatCurrency(goal.current_amount)}</span>
                                                    <span>{formatCurrency(goal.target_amount)}</span>
                                                </div>
                                                <Progress value={goal.progress_percentage || 0} className="h-2" />
                                                <div className="flex justify-between text-xs text-muted-foreground">
                                                    <span>{(goal.progress_percentage || 0).toFixed(1)}%</span>
                                                    <span>Due: {formatDate(goal.target_date)}</span>
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                    {activeGoals.length > 3 && (
                                        <div className="pt-2 border-t">
                                            <Link href={savingsGoals.url()}>
                                                <Button variant="outline" size="sm" className="w-full">
                                                    View All Goals ({activeGoals.length})
                                                    <ArrowRight className="ml-2 h-3 w-3" />
                                                </Button>
                                            </Link>
                                        </div>
                                    )}
                                </div>
                            ) : (
                                <div className="text-center py-6 text-muted-foreground">
                                    <Target className="mx-auto h-8 w-8 mb-2" />
                                    <p className="text-sm mb-3">No active goals yet</p>
                                    <Link href={createGoalUrl.url()}>
                                        <Button size="sm">Create Your First Goal</Button>
                                    </Link>
                                </div>
                            )}
                        </CardContent>
                    </Card>
                </div>

                {/* Recent Budgets */}
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center space-x-2">
                            <BarChart3 className="h-5 w-5" />
                            <span>Recent Budgets</span>
                        </CardTitle>
                        <CardDescription>
                            Your latest budget plans
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        {recentBudgets.length > 0 ? (
                            <div className="space-y-4">
                                {recentBudgets.map((budget) => {
                                    const monthNames = [
                                        'January', 'February', 'March', 'April', 'May', 'June',
                                        'July', 'August', 'September', 'October', 'November', 'December'
                                    ];
                                    const remainingIncome = budget.total_income - budget.total_expenses;
                                    const savingsAchieved = remainingIncome - budget.savings_target;
                                    
                                    return (
                                        <div key={budget.id} className="flex items-center justify-between p-3 border rounded-lg">
                                            <div>
                                                <h4 className="font-medium">
                                                    {monthNames[budget.month - 1]} {budget.year}
                                                </h4>
                                                <p className="text-sm text-muted-foreground">
                                                    Income: {formatCurrency(budget.total_income)} • 
                                                    Expenses: {formatCurrency(budget.total_expenses)}
                                                </p>
                                            </div>
                                            <div className="text-right">
                                                <p className={`font-medium ${savingsAchieved >= 0 ? 'text-green-600' : 'text-red-600'}`}>
                                                    {formatCurrency(savingsAchieved)}
                                                </p>
                                                <p className="text-xs text-muted-foreground">
                                                    vs target: {formatCurrency(budget.savings_target)}
                                                </p>
                                            </div>
                                        </div>
                                    );
                                })}
                                <div className="pt-2 border-t">
                                    <Link href={savingsBudget.url()}>
                                        <Button variant="outline" size="sm" className="w-full">
                                            View All Budgets
                                            <ArrowRight className="ml-2 h-3 w-3" />
                                        </Button>
                                    </Link>
                                </div>
                            </div>
                        ) : (
                            <div className="text-center py-6 text-muted-foreground">
                                <BarChart3 className="mx-auto h-8 w-8 mb-2" />
                                <p className="text-sm mb-3">No budgets created yet</p>
                                    <Link href={createBudgetUrl.url()}>
                                        <Button size="sm">Create Your First Budget</Button>
                                    </Link>
                            </div>
                        )}
                    </CardContent>
                </Card>

                {/* Quick Actions */}
                <div className="grid gap-4 md:grid-cols-3">
                    <Card className="cursor-pointer hover:shadow-md transition-shadow">
                        <Link href={savingsGoals.url()}>
                            <CardHeader>
                                <CardTitle className="flex items-center space-x-2">
                                    <Target className="h-5 w-5" />
                                    <span>Manage Goals</span>
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
                                    Create and manage your budgets
                                </CardDescription>
                            </CardHeader>
                        </Link>
                    </Card>
                    <Card className="cursor-pointer hover:shadow-md transition-shadow">
                        <Link href="/transactions/create">
                            <CardHeader>
                                <CardTitle className="flex items-center space-x-2">
                                    <Plus className="h-5 w-5" />
                                    <span>Make Transaction</span>
                                </CardTitle>
                                <CardDescription>
                                    Deposit or withdraw from your accounts
                                </CardDescription>
                            </CardHeader>
                        </Link>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}
