import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { 
    ArrowLeft,
    CreditCard,
    DollarSign,
    TrendingUp,
    Calendar,
    User,
    Phone,
    Mail,
    Building2,
    Download,
    MoreHorizontal,
    Eye,
    Edit,
    Trash2,
    Plus
} from 'lucide-react';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { useState } from 'react';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { index as accountsIndex, destroy as accountsDestroy } from '@/routes/accounts';
import AccountDeleteDialog from '@/components/account-delete-dialog';

interface Transaction {
    id: number;
    type: string;
    amount: number;
    description: string;
    created_at: string;
    reference: string;
}

interface Account {
    id: number;
    account_number: string;
    account_type: string;
    balance: number;
    status: string;
    status_reason?: string;
    created_at: string;
    updated_at: string;
    member: {
        id: number;
        name: string;
        email: string;
        member_number: string;
        phone_number: string;
        branch?: {
            id: number;
            name: string;
        } | null;
    };
    transactions: Transaction[];
}

interface AccountInfo {
    display_name: string;
    description: string;
    interest_rate: number;
    minimum_balance: number;
    icon: string;
    color: string;
}

interface Props {
    account: Account;
    accountInfo: AccountInfo;
}

export default function ShowAccount({ account, accountInfo }: Props) {
    const { auth } = usePage().props as { auth: { user: any } };
    const user = auth.user;

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

    const getTransactionTypeBadge = (type: string) => {
        switch (type) {
            case 'deposit':
                return <Badge variant="default">Deposit</Badge>;
            case 'withdrawal':
                return <Badge variant="destructive">Withdrawal</Badge>;
            case 'transfer_in':
                return <Badge variant="secondary">Transfer In</Badge>;
            case 'transfer_out':
                return <Badge variant="outline">Transfer Out</Badge>;
            case 'interest':
                return <Badge variant="default">Interest</Badge>;
            case 'fee':
                return <Badge variant="outline">Fee</Badge>;
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
            hour: '2-digit',
            minute: '2-digit',
        });
    };

    const formatDateShort = (date: string) => {
        return new Date(date).toLocaleDateString('en-KE', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        });
    };

    const [deleteDialogOpen, setDeleteDialogOpen] = useState(false);
    const [isDeleting, setIsDeleting] = useState(false);

    const handleDelete = () => {
        setDeleteDialogOpen(true);
    };

    const handleConfirmDelete = (reason: string) => {
        setIsDeleting(true);
        router.delete(accountsDestroy.url(account.id), {
            data: { reason },
            onSuccess: () => {
                setDeleteDialogOpen(false);
                setIsDeleting(false);
            },
            onError: () => {
                setIsDeleting(false);
            }
        });
    };

    const handleCloseDialog = () => {
        setDeleteDialogOpen(false);
        setIsDeleting(false);
    };

    const getAccountIcon = (iconName: string) => {
        const icons = {
            'building-library': CreditCard,
            'banknotes': DollarSign,
            'safe': CreditCard,
            'academic-cap': CreditCard,
            'flag': CreditCard,
            'briefcase': CreditCard,
        };
        return icons[iconName as keyof typeof icons] || CreditCard;
    };

    const IconComponent = getAccountIcon(accountInfo.icon);

    return (
        <AppLayout>
            <Head title={`Account ${account.account_number}`} />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div className="flex items-center space-x-4">
                        <Link href={accountsIndex.url()}>
                            <Button variant="outline" size="sm">
                                <ArrowLeft className="mr-2 h-4 w-4" />
                                Back to Accounts
                            </Button>
                        </Link>
                        <div>
                            <h1 className="text-3xl font-bold tracking-tight">{account.account_number}</h1>
                            <p className="text-muted-foreground">
                                {accountInfo.display_name} • {account.member.name}
                            </p>
                        </div>
                    </div>
                    <div className="flex items-center space-x-2">
                        <Button variant="outline">
                            <Download className="mr-2 h-4 w-4" />
                            Statement
                        </Button>
                        {user.role !== 'member' && (
                            <DropdownMenu>
                                <DropdownMenuTrigger asChild>
                                    <Button variant="outline">
                                        <MoreHorizontal className="h-4 w-4" />
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent align="end">
                                    <DropdownMenuItem>
                                        <Edit className="mr-2 h-4 w-4" />
                                        Update Status
                                    </DropdownMenuItem>
                                    <DropdownMenuItem 
                                        onClick={handleDelete}
                                        className="text-red-600"
                                    >
                                        <Trash2 className="mr-2 h-4 w-4" />
                                        Close Account
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        )}
                    </div>
                </div>

                <div className="grid gap-6 lg:grid-cols-3">
                    {/* Account Overview */}
                    <div className="lg:col-span-2 space-y-6">
                        {/* Account Details */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center space-x-2">
                                    <div className={`w-8 h-8 rounded-full flex items-center justify-center ${
                                        accountInfo.color === 'blue' ? 'bg-blue-100 text-blue-600' :
                                        accountInfo.color === 'purple' ? 'bg-purple-100 text-purple-600' :
                                        accountInfo.color === 'green' ? 'bg-green-100 text-green-600' :
                                        accountInfo.color === 'yellow' ? 'bg-yellow-100 text-yellow-600' :
                                        accountInfo.color === 'pink' ? 'bg-pink-100 text-pink-600' :
                                        'bg-gray-100 text-gray-600'
                                    }`}>
                                        <IconComponent className="h-4 w-4" />
                                    </div>
                                    <span>Account Details</span>
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div className="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <h4 className="font-semibold text-lg">{formatCurrency(account.balance)}</h4>
                                        <p className="text-sm text-muted-foreground">Current Balance</p>
                                    </div>
                                    <div>
                                        <h4 className="font-semibold">{accountInfo.display_name}</h4>
                                        <p className="text-sm text-muted-foreground">Account Type</p>
                                    </div>
                                    <div>
                                        <h4 className="font-semibold">{accountInfo.interest_rate}%</h4>
                                        <p className="text-sm text-muted-foreground">Interest Rate</p>
                                    </div>
                                    <div>
                                        <h4 className="font-semibold">{formatCurrency(accountInfo.minimum_balance)}</h4>
                                        <p className="text-sm text-muted-foreground">Minimum Balance</p>
                                    </div>
                                </div>
                                <div className="flex items-center space-x-2">
                                    {getStatusBadge(account.status)}
                                    <span className="text-sm text-muted-foreground">
                                        Opened {formatDateShort(account.created_at)}
                                    </span>
                                </div>
                                {account.status_reason && (
                                    <Alert>
                                        <AlertDescription>
                                            <strong>Status Reason:</strong> {account.status_reason}
                                        </AlertDescription>
                                    </Alert>
                                )}
                            </CardContent>
                        </Card>

                        {/* Member Information */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center space-x-2">
                                    <User className="h-5 w-5" />
                                    <span>Member Information</span>
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <h4 className="font-semibold">{account.member.name}</h4>
                                        <p className="text-sm text-muted-foreground">Full Name</p>
                                    </div>
                                    <div>
                                        <h4 className="font-semibold">{account.member.member_number}</h4>
                                        <p className="text-sm text-muted-foreground">Member Number</p>
                                    </div>
                                    <div>
                                        <h4 className="font-semibold">{account.member.email}</h4>
                                        <p className="text-sm text-muted-foreground">Email Address</p>
                                    </div>
                                    <div>
                                        <h4 className="font-semibold">{account.member.phone_number}</h4>
                                        <p className="text-sm text-muted-foreground">Phone Number</p>
                                    </div>
                                    {account.member.branch && (
                                        <div className="md:col-span-2">
                                            <h4 className="font-semibold">{account.member.branch.name}</h4>
                                            <p className="text-sm text-muted-foreground">Branch</p>
                                        </div>
                                    )}
                                </div>
                            </CardContent>
                        </Card>

                        {/* Recent Transactions */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center justify-between">
                                    <span>Recent Transactions</span>
                                    <Button variant="outline" size="sm">
                                        <Plus className="mr-2 h-4 w-4" />
                                        New Transaction
                                    </Button>
                                </CardTitle>
                                <CardDescription>
                                    Last 20 transactions for this account
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                {account.transactions.length > 0 ? (
                                    <div className="space-y-3">
                                        {account.transactions.map((transaction) => (
                                            <div key={transaction.id} className="flex items-center justify-between p-3 border rounded-lg">
                                                <div className="flex items-center space-x-3">
                                                    <div className="w-8 h-8 bg-primary/10 rounded-full flex items-center justify-center">
                                                        <DollarSign className="h-4 w-4 text-primary" />
                                                    </div>
                                                    <div>
                                                        <h4 className="font-medium">{transaction.description}</h4>
                                                        <p className="text-sm text-muted-foreground">
                                                            {transaction.reference} • {formatDate(transaction.created_at)}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div className="flex items-center space-x-3">
                                                    <div className="text-right">
                                                        <p className={`font-medium ${
                                                            transaction.type === 'deposit' || transaction.type === 'transfer_in' || transaction.type === 'interest'
                                                                ? 'text-green-600'
                                                                : 'text-red-600'
                                                        }`}>
                                                            {transaction.type === 'deposit' || transaction.type === 'transfer_in' || transaction.type === 'interest' ? '+' : '-'}
                                                            {formatCurrency(transaction.amount)}
                                                        </p>
                                                        <p className="text-sm text-muted-foreground">Amount</p>
                                                    </div>
                                                    {getTransactionTypeBadge(transaction.type)}
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                ) : (
                                    <div className="text-center py-8 text-muted-foreground">
                                        <DollarSign className="mx-auto h-12 w-12 mb-4" />
                                        <p>No transactions yet</p>
                                        <p className="text-sm">Transactions will appear here once you make deposits or withdrawals</p>
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                    </div>

                    {/* Account Info Sidebar */}
                    <div className="lg:col-span-1">
                        <Card>
                            <CardHeader>
                                <CardTitle>Account Information</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div>
                                    <h4 className="font-semibold">{accountInfo.display_name}</h4>
                                    <p className="text-sm text-muted-foreground mt-1">
                                        {accountInfo.description}
                                    </p>
                                </div>
                                
                                <div className="space-y-3">
                                    <div className="flex justify-between">
                                        <span className="text-sm text-muted-foreground">Account Number:</span>
                                        <span className="font-medium">{account.account_number}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-sm text-muted-foreground">Account Type:</span>
                                        <Badge variant="outline">{account.account_type}</Badge>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-sm text-muted-foreground">Interest Rate:</span>
                                        <span className="font-medium">{accountInfo.interest_rate}%</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-sm text-muted-foreground">Minimum Balance:</span>
                                        <span className="font-medium">{formatCurrency(accountInfo.minimum_balance)}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-sm text-muted-foreground">Status:</span>
                                        {getStatusBadge(account.status)}
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-sm text-muted-foreground">Opened:</span>
                                        <span className="font-medium">{formatDateShort(account.created_at)}</span>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>

            {/* Account Delete Dialog */}
            <AccountDeleteDialog
                isOpen={deleteDialogOpen}
                onClose={handleCloseDialog}
                onConfirm={handleConfirmDelete}
                account={account}
                isLoading={isDeleting}
            />
        </AppLayout>
    );
}

