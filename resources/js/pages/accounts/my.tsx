import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { 
    CreditCard, 
    Wallet, 
    TrendingUp, 
    DollarSign,
    Eye, 
    Plus,
    Building2
} from 'lucide-react';
import { Head, Link } from '@inertiajs/react';
import { create as accountsCreate, show as accountsShow } from '@/routes/accounts';

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
        email: string;
        member_number: string;
    } | null;
}

interface Props {
    accounts: Account[];
}

export default function MyAccounts({ accounts }: Props) {
    const getStatusBadge = (status: string) => {
        switch (status) {
            case 'active':
                return <Badge variant="default">Active</Badge>;
            case 'inactive':
                return <Badge variant="secondary">Inactive</Badge>;
            case 'frozen':
                return <Badge variant="destructive">Frozen</Badge>;
            case 'closed':
                return <Badge variant="outline">Closed</Badge>;
            default:
                return <Badge variant="outline">{status}</Badge>;
        }
    };

    const getAccountTypeBadge = (type: string) => {
        const variants = {
            savings: 'secondary',
            shares: 'default',
            deposits: 'default',
            junior: 'outline',
            goal_based: 'default',
            business: 'secondary',
        } as const;
        
        return (
            <Badge variant={variants[type as keyof typeof variants] || 'outline'}>
                {type.replace('_', ' ').toUpperCase()}
            </Badge>
        );
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

    const totalBalance = accounts.reduce((sum, account) => sum + account.balance, 0);
    const activeAccounts = accounts.filter(account => account.status === 'active').length;

    return (
        <AppLayout>
            <Head title="My Accounts" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">My Accounts</h1>
                        <p className="text-muted-foreground">
                            View and manage your SACCO accounts
                        </p>
                    </div>
                    <Link href={accountsCreate.url()}>
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            Open New Account
                        </Button>
                    </Link>
                </div>

                {/* Stats Cards */}
                <div className="grid auto-rows-min gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Accounts</CardTitle>
                            <CreditCard className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{accounts.length}</div>
                            <p className="text-xs text-muted-foreground">
                                Your accounts
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Active Accounts</CardTitle>
                            <Wallet className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{activeAccounts}</div>
                            <p className="text-xs text-muted-foreground">
                                Currently active
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Balance</CardTitle>
                            <DollarSign className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{formatCurrency(totalBalance)}</div>
                            <p className="text-xs text-muted-foreground">
                                Across all accounts
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Average Balance</CardTitle>
                            <TrendingUp className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">
                                {accounts.length > 0 ? formatCurrency(totalBalance / accounts.length) : formatCurrency(0)}
                            </div>
                            <p className="text-xs text-muted-foreground">
                                Per account
                            </p>
                        </CardContent>
                    </Card>
                </div>

                {/* Accounts List */}
                <Card>
                    <CardHeader>
                        <CardTitle>Your Accounts</CardTitle>
                        <CardDescription>
                            Manage your SACCO accounts and view balances
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        {accounts.length > 0 ? (
                            <div className="space-y-4">
                                {accounts.map((account) => (
                                    <div key={account.id} className="flex items-center justify-between p-4 border rounded-lg">
                                        <div className="flex items-center space-x-4">
                                            <div className="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center">
                                                <CreditCard className="h-5 w-5 text-primary" />
                                            </div>
                                            <div>
                                                <h3 className="font-semibold">{account.account_number}</h3>
                                                <p className="text-sm text-muted-foreground">
                                                    {account.member?.name || 'No Member'} • {account.member?.member_number || 'N/A'}
                                                </p>
                                                <p className="text-sm text-muted-foreground">
                                                    Opened {formatDate(account.created_at)}
                                                </p>
                                            </div>
                                        </div>
                                        <div className="flex items-center space-x-4">
                                            <div className="text-right">
                                                <p className="font-medium">{formatCurrency(account.balance)}</p>
                                                <p className="text-sm text-muted-foreground">Balance</p>
                                            </div>
                                            <div className="flex items-center space-x-2">
                                                {getAccountTypeBadge(account.account_type)}
                                                {getStatusBadge(account.status)}
                                                <Link href={accountsShow.url(account.id)}>
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
                                <CreditCard className="mx-auto h-12 w-12 mb-4" />
                                <p className="text-lg font-medium mb-2">No accounts yet</p>
                                <p className="mb-4">You haven't opened any accounts with the SACCO yet.</p>
                                <Link href={accountsCreate.url()}>
                                    <Button>
                                        <Plus className="mr-2 h-4 w-4" />
                                        Open Your First Account
                                    </Button>
                                </Link>
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}

