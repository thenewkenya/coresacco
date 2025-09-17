import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { 
    ArrowLeft,
    ArrowLeftRight,
    DollarSign,
    Calendar,
    User,
    CreditCard,
    FileText,
    CheckCircle,
    XCircle,
    Clock,
    Smartphone
} from 'lucide-react';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { index as transactionsIndex, receipt as transactionsReceipt, approve as transactionsApprove, reject as transactionsReject } from '@/routes/transactions';
import { useState, useEffect } from 'react';
import { type BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';

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
    updated_at: string;
    account: {
        id: number;
        account_number: string;
        account_type: string;
        balance: number;
    };
    member: {
        id: number;
        name: string;
        member_number: string;
        email: string;
        phone_number: string;
    };
}

interface Props {
    transaction: Transaction;
}

export default function ShowTransaction({ transaction }: Props) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: dashboard().url,
        },
        {
            title: 'Transactions',
            href: transactionsIndex().url,
        },
        {
            title: transaction.reference_number,
            href: '#',
        },
    ];
    const { auth } = usePage().props as { auth: { user: any } };
    const user = auth.user;
    
    const [currentTransaction, setCurrentTransaction] = useState(transaction);
    const [isPolling, setIsPolling] = useState(false);

    // Poll for M-Pesa transaction status updates
    useEffect(() => {
        if (currentTransaction.payment_method === 'mpesa' && currentTransaction.status === 'pending') {
            setIsPolling(true);
            
            const pollInterval = setInterval(async () => {
                try {
                    const response = await fetch(`/api/transactions/${currentTransaction.id}/status`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        },
                    });
                    
                    if (response.ok) {
                        const data = await response.json();
                        setCurrentTransaction(prev => ({
                            ...prev,
                            status: data.status,
                            description: data.description,
                            balance_after: data.balance_after,
                            updated_at: data.updated_at,
                        }));
                        
                        // If transaction is completed, redirect to receipt after a short delay
                        if (data.status === 'completed') {
                            setIsPolling(false);
                            setTimeout(() => {
                                router.visit(transactionsReceipt.url(currentTransaction.id));
                            }, 2000);
                        }
                    }
                } catch (error) {
                    console.error('Error polling transaction status:', error);
                }
            }, 3000); // Poll every 3 seconds
            
            return () => {
                clearInterval(pollInterval);
                setIsPolling(false);
            };
        }
    }, [currentTransaction.id, currentTransaction.payment_method, currentTransaction.status]);

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
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    };

    const handleApprove = () => {
        if (confirm('Are you sure you want to approve this transaction?')) {
            router.post(transactionsApprove.url(transaction.id));
        }
    };

    const handleReject = () => {
        if (confirm('Are you sure you want to reject this transaction?')) {
            router.post(transactionsReject.url(transaction.id));
        }
    };

    const getStatusIcon = (status: string) => {
        switch (status) {
            case 'completed':
                return <CheckCircle className="h-5 w-5 text-green-600" />;
            case 'pending':
                return <Clock className="h-5 w-5 text-yellow-600" />;
            case 'failed':
                return <XCircle className="h-5 w-5 text-red-600" />;
            default:
                return <Clock className="h-5 w-5 text-gray-600" />;
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Transaction ${transaction.reference_number}`} />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div className="flex items-center space-x-4">
                        <Link href={transactionsIndex.url()}>
                            <Button variant="outline" size="sm">
                                <ArrowLeft className="mr-2 h-4 w-4" />
                                Back to Transactions
                            </Button>
                        </Link>
                        <div>
                            <h1 className="text-3xl font-bold tracking-tight">{currentTransaction.reference_number}</h1>
                            <p className="text-muted-foreground">
                                Transaction Details • {currentTransaction.member.name}
                            </p>
                        </div>
                    </div>
                    <div className="flex items-center space-x-2">
                        {currentTransaction.status === 'completed' && (
                            <Link href={transactionsReceipt.url(currentTransaction.id)}>
                                <Button variant="outline">
                                    <FileText className="mr-2 h-4 w-4" />
                                    Receipt
                                </Button>
                            </Link>
                        )}
                        {user.role !== 'member' && currentTransaction.status === 'pending' && currentTransaction.payment_method !== 'mpesa' && (
                            <>
                                <Button onClick={handleApprove} className="bg-green-600 hover:bg-green-700">
                                    <CheckCircle className="mr-2 h-4 w-4" />
                                    Approve
                                </Button>
                                <Button onClick={handleReject} variant="destructive">
                                    <XCircle className="mr-2 h-4 w-4" />
                                    Reject
                                </Button>
                            </>
                        )}
                        {/* M-Pesa Status Message */}
                        {currentTransaction.status === 'pending' && currentTransaction.payment_method === 'mpesa' && (
                            <div className="mt-4">
                                <Alert>
                                    <Smartphone className="h-4 w-4" />
                                    <AlertDescription>
                                        <strong>M-Pesa Payment Pending</strong><br />
                                        This transaction will be automatically confirmed once the M-Pesa payment is completed on your phone. 
                                        No manual approval is required.
                                        {isPolling && (
                                            <div className="mt-2 text-sm text-blue-600">
                                                🔄 Checking for payment confirmation...
                                            </div>
                                        )}
                                    </AlertDescription>
                                </Alert>
                            </div>
                        )}
                    </div>
                </div>

                <div className="grid gap-6 lg:grid-cols-3">
                    {/* Transaction Details */}
                    <div className="lg:col-span-2 space-y-6">
                        {/* Transaction Overview */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center space-x-2">
                                    <div className="w-8 h-8 bg-primary/10 rounded-full flex items-center justify-center">
                                        <ArrowLeftRight className="h-4 w-4 text-primary" />
                                    </div>
                                    <span>Transaction Details</span>
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div className="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <h4 className="font-semibold text-lg">
                                            {currentTransaction.type === 'deposit' || currentTransaction.type === 'interest' ? '+' : '-'}
                                            {formatCurrency(currentTransaction.amount)}
                                        </h4>
                                        <p className="text-sm text-muted-foreground">Transaction Amount</p>
                                    </div>
                                    <div>
                                        <h4 className="font-semibold">{currentTransaction.description}</h4>
                                        <p className="text-sm text-muted-foreground">Description</p>
                                    </div>
                                    <div>
                                        <h4 className="font-semibold">{formatCurrency(currentTransaction.balance_before)}</h4>
                                        <p className="text-sm text-muted-foreground">Balance Before</p>
                                    </div>
                                    <div>
                                        <h4 className="font-semibold">{formatCurrency(currentTransaction.balance_after)}</h4>
                                        <p className="text-sm text-muted-foreground">Balance After</p>
                                    </div>
                                </div>
                                <div className="flex items-center space-x-2">
                                    {getTransactionTypeBadge(currentTransaction.type)}
                                    {getStatusBadge(currentTransaction.status)}
                                    <div className="flex items-center space-x-1">
                                        {getStatusIcon(currentTransaction.status)}
                                        <span className="text-sm text-muted-foreground">
                                            {formatDate(currentTransaction.created_at)}
                                        </span>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        {/* Account Information */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center space-x-2">
                                    <CreditCard className="h-5 w-5" />
                                    <span>Account Information</span>
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <h4 className="font-semibold">{currentTransaction.account?.account_number || 'N/A'}</h4>
                                        <p className="text-sm text-muted-foreground">Account Number</p>
                                    </div>
                                    <div>
                                        <h4 className="font-semibold">{formatCurrency(currentTransaction.account?.balance || 0)}</h4>
                                        <p className="text-sm text-muted-foreground">Current Balance</p>
                                    </div>
                                    <div>
                                        {getAccountTypeBadge(currentTransaction.account?.account_type || 'unknown')}
                                        <p className="text-sm text-muted-foreground mt-1">Account Type</p>
                                    </div>
                                </div>
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
                                        <h4 className="font-semibold">{currentTransaction.member.name}</h4>
                                        <p className="text-sm text-muted-foreground">Full Name</p>
                                    </div>
                                    <div>
                                        <h4 className="font-semibold">{currentTransaction.member.member_number}</h4>
                                        <p className="text-sm text-muted-foreground">Member Number</p>
                                    </div>
                                    <div>
                                        <h4 className="font-semibold">{currentTransaction.member.email}</h4>
                                        <p className="text-sm text-muted-foreground">Email Address</p>
                                    </div>
                                    <div>
                                        <h4 className="font-semibold">{currentTransaction.member.phone_number}</h4>
                                        <p className="text-sm text-muted-foreground">Phone Number</p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>

                    {/* Transaction Info Sidebar */}
                    <div className="lg:col-span-1">
                        <Card>
                            <CardHeader>
                                <CardTitle>Transaction Information</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div>
                                    <h4 className="font-semibold">{transaction.reference_number}</h4>
                                    <p className="text-sm text-muted-foreground mt-1">Reference Number</p>
                                </div>
                                
                                <div className="space-y-3">
                                    <div className="flex justify-between">
                                        <span className="text-sm text-muted-foreground">Transaction Type:</span>
                                        {getTransactionTypeBadge(transaction.type)}
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-sm text-muted-foreground">Status:</span>
                                        {getStatusBadge(transaction.status)}
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-sm text-muted-foreground">Amount:</span>
                                        <span className="font-medium">{formatCurrency(transaction.amount)}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-sm text-muted-foreground">Created:</span>
                                        <span className="font-medium">{formatDate(transaction.created_at)}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-sm text-muted-foreground">Updated:</span>
                                        <span className="font-medium">{formatDate(transaction.updated_at)}</span>
                                    </div>
                                </div>

                                {transaction.status === 'pending' && user.role !== 'member' && (
                                    <Alert>
                                        <AlertDescription>
                                            <strong>Pending Approval:</strong> This transaction requires approval before it can be completed.
                                        </AlertDescription>
                                    </Alert>
                                )}

                                {transaction.status === 'completed' && (
                                    <Alert>
                                        <AlertDescription>
                                            <strong>Completed:</strong> This transaction has been successfully processed.
                                        </AlertDescription>
                                    </Alert>
                                )}
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}

