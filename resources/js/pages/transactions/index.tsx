import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { 
    Plus, 
    ArrowLeftRight, 
    TrendingUp, 
    TrendingDown, 
    DollarSign,
    Search,
    Eye,
    Filter,
    Calendar,
    User,
    CreditCard
} from 'lucide-react';
import { Head, Link, router } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { index as transactionsIndex, create as transactionsCreate, show as transactionsShow } from '@/routes/transactions';

interface Transaction {
    id: number;
    type: string;
    amount: number;
    description: string;
    reference_number: string;
    status: string;
    balance_before: number;
    balance_after: number;
    created_at: string;
    payment_method?: string;
    account: {
        id: number;
        account_number: string;
        account_type: string;
    };
    member: {
        id: number;
        name: string;
        member_number: string;
    };
}

interface Props {
    transactions: {
        data: Transaction[];
        links: any[];
        total: number;
        per_page: number;
        current_page: number;
        last_page: number;
        from: number;
        to: number;
    };
    filters: {
        search?: string;
        status?: string;
        type?: string;
    };
    statusOptions: Record<string, string>;
    typeOptions: Record<string, string>;
}

export default function TransactionsIndex({ transactions, filters, statusOptions, typeOptions }: Props) {
    const [search, setSearch] = useState(filters.search || '');
    const [status, setStatus] = useState(filters.status || 'all');
    const [type, setType] = useState(filters.type || 'all');
    const [currentTransactions, setCurrentTransactions] = useState(transactions);
    const [isPolling, setIsPolling] = useState(false);

    const handleSearch = () => {
        router.get(transactionsIndex.url(), {
            search: search || undefined,
            status: status === 'all' ? undefined : status,
            type: type === 'all' ? undefined : type,
        }, {
            preserveState: true,
            replace: true,
        });
    };

    // Poll for M-Pesa transaction status updates
    useEffect(() => {
        const pendingMpesaTransactions = currentTransactions.data.filter(
            t => t.payment_method === 'mpesa' && t.status === 'pending'
        );

        if (pendingMpesaTransactions.length > 0) {
            setIsPolling(true);
            
            const pollInterval = setInterval(async () => {
                try {
                    // Poll each pending M-Pesa transaction
                    const promises = pendingMpesaTransactions.map(async (transaction) => {
                        const response = await fetch(`/api/transactions/${transaction.id}/status`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                            },
                        });
                        
                        if (response.ok) {
                            return await response.json();
                        }
                        return null;
                    });

                    const results = await Promise.all(promises);
                    const updatedTransactions = results.filter(result => result !== null);

                    if (updatedTransactions.length > 0) {
                        // Update the transactions list with new statuses
                        setCurrentTransactions(prev => ({
                            ...prev,
                            data: prev.data.map(transaction => {
                                const updated = updatedTransactions.find(u => u.id === transaction.id);
                                if (updated) {
                                    return {
                                        ...transaction,
                                        status: updated.status,
                                        description: updated.description,
                                        balance_after: updated.balance_after,
                                    };
                                }
                                return transaction;
                            })
                        }));
                    }
                } catch (error) {
                    console.error('Error polling transaction statuses:', error);
                }
            }, 5000); // Poll every 5 seconds
            
            return () => {
                clearInterval(pollInterval);
                setIsPolling(false);
            };
        } else {
            setIsPolling(false);
        }
    }, [currentTransactions.data]);

    const getStatusBadge = (status: string) => {
        switch (status) {
            case 'completed':
                return <Badge variant="default">Completed</Badge>;
            case 'pending':
                return <Badge variant="outline">Pending</Badge>;
            case 'failed':
                return <Badge variant="destructive">Failed</Badge>;
            case 'reversed':
                return <Badge variant="outline">Reversed</Badge>;
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
            case 'transfer':
                return <Badge variant="secondary">Transfer</Badge>;
            case 'fee':
                return <Badge variant="outline">Fee</Badge>;
            case 'interest':
                return <Badge variant="default">Interest</Badge>;
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
            month: 'short',
            day: 'numeric',
        });
    };

    // Calculate statistics from transactions
    const totalTransactions = transactions.total;
    const totalDeposits = transactions.data
        .filter(t => t.type === 'deposit')
        .reduce((sum, t) => sum + parseFloat(t.amount.toString()), 0);
    const totalWithdrawals = transactions.data
        .filter(t => t.type === 'withdrawal')
        .reduce((sum, t) => sum + parseFloat(t.amount.toString()), 0);
    const netFlow = totalDeposits - totalWithdrawals;

    return (
        <AppLayout>
            <Head title="Transactions" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">Transactions</h1>
                        <p className="text-muted-foreground">
                            View and manage all SACCO transactions
                        </p>
                    </div>
                    <Link href={transactionsCreate.url()}>
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            New Transaction
                        </Button>
                    </Link>
                </div>

                {/* Stats Cards */}
                <div className="grid auto-rows-min gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Transactions</CardTitle>
                            <ArrowLeftRight className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{totalTransactions}</div>
                            <p className="text-xs text-muted-foreground">
                                All time
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Deposits</CardTitle>
                            <TrendingUp className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{formatCurrency(totalDeposits)}</div>
                            <p className="text-xs text-muted-foreground">
                                This page
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Withdrawals</CardTitle>
                            <TrendingDown className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{formatCurrency(totalWithdrawals)}</div>
                            <p className="text-xs text-muted-foreground">
                                This page
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Net Flow</CardTitle>
                            <DollarSign className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className={`text-2xl font-bold ${netFlow >= 0 ? 'text-green-600' : 'text-red-600'}`}>
                                {formatCurrency(netFlow)}
                            </div>
                            <p className="text-xs text-muted-foreground">
                                This page
                            </p>
                        </CardContent>
                    </Card>
                </div>

                {/* Search and Filters */}
                <Card>
                    <CardHeader>
                        <CardTitle>Search Transactions</CardTitle>
                        <CardDescription>
                            Find transactions by member, amount, or transaction ID
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="flex items-center space-x-2">
                            <div className="relative flex-1">
                                <Search className="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                                <Input 
                                    placeholder="Search transactions..." 
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
                            <Select value={type} onValueChange={setType}>
                                <SelectTrigger className="w-[180px]">
                                    <SelectValue placeholder="Type" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">All Types</SelectItem>
                                    {Object.entries(typeOptions).map(([value, label]) => (
                                        <SelectItem key={value} value={value}>
                                            {label}
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

                {/* Transactions Table */}
                <Card>
                    <CardHeader>
                        <CardTitle>Recent Transactions</CardTitle>
                        <CardDescription>
                            Latest transactions in your SACCO ({currentTransactions.total} total)
                            {isPolling && (
                                <span className="ml-2 text-blue-600 text-sm">
                                    🔄 Checking for M-Pesa updates...
                                </span>
                            )}
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        {currentTransactions.data.length > 0 ? (
                            <div className="space-y-4">
                                {currentTransactions.data.map((transaction) => (
                                    <div key={transaction.id} className="flex items-center justify-between p-4 border rounded-lg">
                                        <div className="flex items-center space-x-4">
                                            <div className="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center">
                                                <ArrowLeftRight className="h-5 w-5 text-primary" />
                                            </div>
                                            <div>
                                                <h3 className="font-semibold">{transaction.reference_number}</h3>
                                                <p className="text-sm text-muted-foreground">
                                                    {transaction.member?.name || 'Unknown Member'} • {transaction.account?.account_number || 'N/A'}
                                                </p>
                                                <p className="text-sm text-muted-foreground">
                                                    {transaction.description}
                                                    {transaction.payment_method === 'mpesa' && (
                                                        <span className="ml-2 text-green-600 text-xs">
                                                            📱 M-Pesa
                                                        </span>
                                                    )}
                                                </p>
                                            </div>
                                        </div>
                                        <div className="flex items-center space-x-4">
                                            <div className="text-right">
                                                <p className={`font-medium ${
                                                    transaction.type === 'deposit' || transaction.type === 'interest'
                                                        ? 'text-green-600'
                                                        : 'text-red-600'
                                                }`}>
                                                    {transaction.type === 'deposit' || transaction.type === 'interest' ? '+' : '-'}
                                                    {formatCurrency(transaction.amount)}
                                                </p>
                                                <p className="text-sm text-muted-foreground">
                                                    {formatDateShort(transaction.created_at)}
                                                </p>
                                            </div>
                                            <div className="flex items-center space-x-2">
                                                {getTransactionTypeBadge(transaction.type)}
                                                {getStatusBadge(transaction.status)}
                                                <Link href={transactionsShow.url(transaction.id)}>
                                                    <Button variant="outline" size="sm">
                                                        <Eye className="mr-2 h-4 w-4" />
                                                        View
                                                    </Button>
                                                </Link>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <div className="text-center py-8 text-muted-foreground">
                                <ArrowLeftRight className="mx-auto h-12 w-12 mb-4" />
                                <p>No transactions found</p>
                                <p className="text-sm">Transactions will appear here once they are created</p>
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}