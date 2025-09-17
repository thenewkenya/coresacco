import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { 
    Plus, 
    CreditCard, 
    Wallet, 
    TrendingUp, 
    DollarSign,
    Search, 
    Eye, 
    Edit, 
    Trash2,
    Filter,
    MoreHorizontal,
    Building2
} from 'lucide-react';
import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import AccountDeleteDialog from '@/components/account-delete-dialog';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { index as accountsIndex, create as accountsCreate, show as accountsShow, destroy as accountsDestroy } from '@/routes/accounts';
import { type BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';

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

interface Stats {
    totalAccounts: number;
    totalBalance: number;
    activeAccounts: number;
    thisMonthAccounts: number;
}

interface Filters {
    search?: string;
    account_type?: string;
    status?: string;
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
    stats: Stats;
    filters: Filters;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Accounts',
        href: accountsIndex().url,
    },
];

export default function AccountsIndex({ accounts, stats, filters }: Props) {
    const [search, setSearch] = useState(filters.search || '');
    const [accountType, setAccountType] = useState(filters.account_type || 'all');
    const [status, setStatus] = useState(filters.status || 'all');
    const [deleteDialogOpen, setDeleteDialogOpen] = useState(false);
    const [selectedAccount, setSelectedAccount] = useState<Account | null>(null);
    const [isDeleting, setIsDeleting] = useState(false);

    const handleSearch = () => {
        router.get(accountsIndex.url(), {
            search: search || undefined,
            account_type: accountType === 'all' ? undefined : accountType,
            status: status === 'all' ? undefined : status,
        }, {
            preserveState: true,
            replace: true,
        });
    };

    const handleDelete = (account: Account) => {
        setSelectedAccount(account);
        setDeleteDialogOpen(true);
    };

    const handleConfirmDelete = (reason: string) => {
        if (!selectedAccount) return;
        
        setIsDeleting(true);
        router.delete(accountsDestroy.url(selectedAccount.id), {
            data: { reason },
            onSuccess: () => {
                setDeleteDialogOpen(false);
                setSelectedAccount(null);
                setIsDeleting(false);
            },
            onError: () => {
                setIsDeleting(false);
            }
        });
    };

    const handleCloseDialog = () => {
        setDeleteDialogOpen(false);
        setSelectedAccount(null);
        setIsDeleting(false);
    };

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

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Accounts" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">Accounts</h1>
                        <p className="text-muted-foreground">
                            Manage member accounts and account types
                        </p>
                    </div>
                    <Link href={accountsCreate.url()}>
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            Create Account
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
                            <div className="text-2xl font-bold">{stats.totalAccounts}</div>
                            <p className="text-xs text-muted-foreground">
                                All account types
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Active Accounts</CardTitle>
                            <Wallet className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.activeAccounts}</div>
                            <p className="text-xs text-muted-foreground">
                                Currently active
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">New This Month</CardTitle>
                            <DollarSign className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.thisMonthAccounts}</div>
                            <p className="text-xs text-muted-foreground">
                                Opened this month
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Balance</CardTitle>
                            <TrendingUp className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{formatCurrency(stats.totalBalance)}</div>
                            <p className="text-xs text-muted-foreground">
                                All accounts combined
                            </p>
                        </CardContent>
                    </Card>
                </div>

                {/* Search and Filters */}
                <Card>
                    <CardHeader>
                        <CardTitle>Search Accounts</CardTitle>
                        <CardDescription>
                            Find accounts by member name, account number, or type
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
                            <Select value={accountType} onValueChange={setAccountType}>
                                <SelectTrigger className="w-[180px]">
                                    <SelectValue placeholder="Account Type" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">All Types</SelectItem>
                                    <SelectItem value="savings">Savings</SelectItem>
                                    <SelectItem value="shares">Shares</SelectItem>
                                    <SelectItem value="deposits">Deposits</SelectItem>
                                    <SelectItem value="junior">Junior</SelectItem>
                                    <SelectItem value="goal_based">Goal Based</SelectItem>
                                    <SelectItem value="business">Business</SelectItem>
                                </SelectContent>
                            </Select>
                            <Select value={status} onValueChange={setStatus}>
                                <SelectTrigger className="w-[180px]">
                                    <SelectValue placeholder="Status" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">All Status</SelectItem>
                                    <SelectItem value="active">Active</SelectItem>
                                    <SelectItem value="inactive">Inactive</SelectItem>
                                    <SelectItem value="frozen">Frozen</SelectItem>
                                    <SelectItem value="closed">Closed</SelectItem>
                                </SelectContent>
                            </Select>
                            <Button onClick={handleSearch}>
                                <Filter className="mr-2 h-4 w-4" />
                                Filter
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                {/* Accounts Table */}
                <Card>
                    <CardHeader>
                        <CardTitle>Accounts List</CardTitle>
                        <CardDescription>
                            A list of all accounts in your SACCO ({accounts.total} total)
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        {accounts.data.length > 0 ? (
                            <div className="space-y-4">
                                {accounts.data.map((account) => (
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
                                                <DropdownMenu>
                                                    <DropdownMenuTrigger asChild>
                                                        <Button variant="ghost" size="sm">
                                                            <MoreHorizontal className="h-4 w-4" />
                                                        </Button>
                                                    </DropdownMenuTrigger>
                                                    <DropdownMenuContent align="end">
                                                        <DropdownMenuItem asChild>
                                                            <Link href={accountsShow.url(account.id)}>
                                                                <Eye className="mr-2 h-4 w-4" />
                                                                View Details
                                                            </Link>
                                                        </DropdownMenuItem>
                                                        <DropdownMenuItem 
                                                            onClick={() => handleDelete(account)}
                                                            className="text-red-600"
                                                        >
                                                            <Trash2 className="mr-2 h-4 w-4" />
                                                            Close Account
                                                        </DropdownMenuItem>
                                                    </DropdownMenuContent>
                                                </DropdownMenu>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <div className="text-center py-8 text-muted-foreground">
                                <CreditCard className="mx-auto h-12 w-12 mb-4" />
                                <p>No accounts found</p>
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>

            {/* Account Delete Dialog */}
            {selectedAccount && (
                <AccountDeleteDialog
                    isOpen={deleteDialogOpen}
                    onClose={handleCloseDialog}
                    onConfirm={handleConfirmDelete}
                    account={selectedAccount}
                    isLoading={isDeleting}
                />
            )}
        </AppLayout>
    );
}