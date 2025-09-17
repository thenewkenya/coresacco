import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { 
    ArrowLeft,
    ArrowLeftRight,
    DollarSign,
    Calendar,
    User,
    CreditCard,
    Download,
    Printer
} from 'lucide-react';
import { Head, Link } from '@inertiajs/react';
import { index as transactionsIndex, show as transactionsShow } from '@/routes/transactions';
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
    account: {
        id: number;
        account_number: string;
        account_type: string;
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

export default function TransactionReceipt({ transaction }: Props) {
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
            href: transactionsShow({ transaction: transaction.id }).url,
        },
        {
            title: 'Receipt',
            href: '#',
        },
    ];

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
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    };

    const handlePrint = () => {
        window.print();
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Receipt - ${transaction.reference_number}`} />
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
                            <h1 className="text-3xl font-bold tracking-tight">Transaction Receipt</h1>
                            <p className="text-muted-foreground">
                                Receipt for transaction {transaction.reference_number}
                            </p>
                        </div>
                    </div>
                    <div className="flex items-center space-x-2">
                        <Button onClick={handlePrint} variant="outline">
                            <Printer className="mr-2 h-4 w-4" />
                            Print Receipt
                        </Button>
                        <Button variant="outline">
                            <Download className="mr-2 h-4 w-4" />
                            Download PDF
                        </Button>
                    </div>
                </div>

                {/* Receipt Content */}
                <div className="max-w-2xl mx-auto">
                    <Card className="print:shadow-none print:border-0">
                        <CardHeader className="text-center">
                            <CardTitle className="text-2xl font-bold">SACCO TRANSACTION RECEIPT</CardTitle>
                            <CardDescription>
                                Transaction Receipt - {transaction.reference_number}
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-6">
                            {/* Transaction Details */}
                            <div className="space-y-4">
                                <h3 className="font-semibold text-lg border-b pb-2">Transaction Details</h3>
                                <div className="grid gap-3">
                                    <div className="flex justify-between">
                                        <span className="text-muted-foreground">Reference Number:</span>
                                        <span className="font-medium">{transaction.reference_number}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-muted-foreground">Transaction Type:</span>
                                        {getTransactionTypeBadge(transaction.type)}
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-muted-foreground">Amount:</span>
                                        <span className={`font-bold text-lg ${
                                            transaction.type === 'deposit' || transaction.type === 'interest'
                                                ? 'text-green-600'
                                                : 'text-red-600'
                                        }`}>
                                            {transaction.type === 'deposit' || transaction.type === 'interest' ? '+' : '-'}
                                            {formatCurrency(transaction.amount)}
                                        </span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-muted-foreground">Description:</span>
                                        <span className="font-medium text-right max-w-xs">{transaction.description}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-muted-foreground">Date & Time:</span>
                                        <span className="font-medium">{formatDate(transaction.created_at)}</span>
                                    </div>
                                </div>
                            </div>

                            {/* Account Information */}
                            <div className="space-y-4">
                                <h3 className="font-semibold text-lg border-b pb-2">Account Information</h3>
                                <div className="grid gap-3">
                                    <div className="flex justify-between">
                                        <span className="text-muted-foreground">Account Number:</span>
                                        <span className="font-medium">{transaction.account?.account_number || 'N/A'}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-muted-foreground">Account Type:</span>
                                        <span className="font-medium">{transaction.account?.account_type?.replace('_', ' ').toUpperCase() || 'UNKNOWN'}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-muted-foreground">Balance Before:</span>
                                        <span className="font-medium">{formatCurrency(transaction.balance_before)}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-muted-foreground">Balance After:</span>
                                        <span className="font-medium">{formatCurrency(transaction.balance_after)}</span>
                                    </div>
                                </div>
                            </div>

                            {/* Member Information */}
                            <div className="space-y-4">
                                <h3 className="font-semibold text-lg border-b pb-2">Member Information</h3>
                                <div className="grid gap-3">
                                    <div className="flex justify-between">
                                        <span className="text-muted-foreground">Member Name:</span>
                                        <span className="font-medium">{transaction.member.name}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-muted-foreground">Member Number:</span>
                                        <span className="font-medium">{transaction.member.member_number}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-muted-foreground">Email:</span>
                                        <span className="font-medium">{transaction.member.email}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-muted-foreground">Phone:</span>
                                        <span className="font-medium">{transaction.member.phone_number}</span>
                                    </div>
                                </div>
                            </div>

                            {/* Footer */}
                            <div className="text-center pt-6 border-t">
                                <p className="text-sm text-muted-foreground">
                                    This is an official receipt from your SACCO. Please keep this receipt for your records.
                                </p>
                                <p className="text-xs text-muted-foreground mt-2">
                                    Generated on {formatDate(transaction.created_at)}
                                </p>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}

