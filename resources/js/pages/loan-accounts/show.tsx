import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { 
    ArrowLeft,
    DollarSign,
    Calendar,
    User,
    CreditCard,
    CheckCircle,
    AlertCircle,
    Clock,
    FileText,
    TrendingUp,
    TrendingDown,
    Eye,
    Printer
} from 'lucide-react';
import { Head, Link } from '@inertiajs/react';

interface LedgerEntry {
    id: number;
    transaction_type: string;
    amount: number;
    principal_amount: number;
    interest_amount: number;
    fee_amount: number;
    balance_before: number;
    balance_after: number;
    reference_number: string;
    description: string;
    transaction_date: string;
    created_at: string;
}

interface LoanAccount {
    id: number;
    account_number: string;
    loan_type: string;
    principal_amount: number;
    interest_rate: number;
    interest_basis: string;
    term_months: number;
    monthly_payment: number;
    total_payable: number;
    total_interest: number;
    processing_fee: number;
    insurance_fee: number;
    other_fees: number;
    amount_disbursed: number;
    amount_paid: number;
    principal_paid: number;
    interest_paid: number;
    fees_paid: number;
    outstanding_principal: number;
    outstanding_interest: number;
    outstanding_fees: number;
    arrears_amount: number;
    arrears_days: number;
    disbursement_date: string;
    first_payment_date: string;
    maturity_date: string;
    last_payment_date: string;
    next_payment_date: string;
    status: string;
    payment_schedule: any[];
    notes: string;
    member: {
        id: number;
        name: string;
        member_number: string;
        email: string;
        phone: string;
    };
    loan: {
        id: number;
        loan_type: {
            name: string;
        };
    };
    ledger_entries: LedgerEntry[];
}

interface Arrears {
    arrears_days: number;
    arrears_amount: number;
    overdue_installments: number;
}

interface Props {
    loanAccount: LoanAccount;
    arrears: Arrears;
}

export default function ShowLoanAccount({ loanAccount, arrears }: Props) {
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
        });
    };

    const formatDateTime = (date: string) => {
        return new Date(date).toLocaleString('en-KE', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    };

    const getStatusBadge = (status: string) => {
        switch (status) {
            case 'active':
                return <Badge variant="default">Active</Badge>;
            case 'completed':
                return <Badge variant="secondary">Completed</Badge>;
            case 'defaulted':
                return <Badge variant="destructive">Defaulted</Badge>;
            case 'written_off':
                return <Badge variant="outline">Written Off</Badge>;
            default:
                return <Badge variant="outline">{status}</Badge>;
        }
    };

    const getTransactionTypeLabel = (type: string) => {
        return type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
    };

    const totalOutstanding = loanAccount.outstanding_principal + loanAccount.outstanding_interest + loanAccount.outstanding_fees;
    const paymentProgress = (loanAccount.amount_paid / loanAccount.total_payable) * 100;

    return (
        <AppLayout>
            <Head title={`Loan Account ${loanAccount.account_number}`} />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div className="flex items-center space-x-4">
                        <Link href="/loan-accounts">
                            <Button variant="outline" size="sm">
                                <ArrowLeft className="mr-2 h-4 w-4" />
                                Back to Loan Accounts
                            </Button>
                        </Link>
                        <div>
                            <h1 className="text-3xl font-bold tracking-tight">{loanAccount.account_number}</h1>
                            <p className="text-muted-foreground">
                                {loanAccount.member.name} • {loanAccount.loan.loan_type.name}
                            </p>
                        </div>
                    </div>
                    <div className="flex items-center space-x-2">
                        {getStatusBadge(loanAccount.status)}
                        <Button variant="outline" size="sm">
                            <Printer className="mr-2 h-4 w-4" />
                            Print Statement
                        </Button>
                    </div>
                </div>

                <div className="grid gap-6 lg:grid-cols-3">
                    {/* Main Content */}
                    <div className="lg:col-span-2 space-y-6">
                        {/* Account Details */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center space-x-2">
                                    <CreditCard className="h-5 w-5" />
                                    <span>Account Details</span>
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="grid gap-4 md:grid-cols-2">
                                    <div className="space-y-3">
                                        <div className="flex justify-between">
                                            <span className="text-sm text-muted-foreground">Account Number:</span>
                                            <span className="font-medium font-mono">{loanAccount.account_number}</span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span className="text-sm text-muted-foreground">Loan Type:</span>
                                            <span className="font-medium capitalize">{loanAccount.loan_type.replace('_', ' ')}</span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span className="text-sm text-muted-foreground">Interest Basis:</span>
                                            <span className="font-medium capitalize">{loanAccount.interest_basis.replace('_', ' ')}</span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span className="text-sm text-muted-foreground">Monthly Payment:</span>
                                            <span className="font-medium">{formatCurrency(loanAccount.monthly_payment)}</span>
                                        </div>
                                    </div>
                                    <div className="space-y-3">
                                        <div className="flex justify-between">
                                            <span className="text-sm text-muted-foreground">Principal Amount:</span>
                                            <span className="font-medium">{formatCurrency(loanAccount.principal_amount)}</span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span className="text-sm text-muted-foreground">Interest Rate:</span>
                                            <span className="font-medium">{loanAccount.interest_rate}%</span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span className="text-sm text-muted-foreground">Term Period:</span>
                                            <span className="font-medium">{loanAccount.term_months} months</span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span className="text-sm text-muted-foreground">Total Payable:</span>
                                            <span className="font-medium">{formatCurrency(loanAccount.total_payable)}</span>
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        {/* Outstanding Balance */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center space-x-2">
                                    <DollarSign className="h-5 w-5" />
                                    <span>Outstanding Balance</span>
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="grid gap-4 md:grid-cols-3">
                                    <div className="text-center p-4 bg-muted/50 rounded-lg">
                                        <p className="text-sm text-muted-foreground">Principal</p>
                                        <p className="text-2xl font-bold">{formatCurrency(loanAccount.outstanding_principal)}</p>
                                    </div>
                                    <div className="text-center p-4 bg-muted/50 rounded-lg">
                                        <p className="text-sm text-muted-foreground">Interest</p>
                                        <p className="text-2xl font-bold">{formatCurrency(loanAccount.outstanding_interest)}</p>
                                    </div>
                                    <div className="text-center p-4 bg-muted/50 rounded-lg">
                                        <p className="text-sm text-muted-foreground">Fees</p>
                                        <p className="text-2xl font-bold">{formatCurrency(loanAccount.outstanding_fees)}</p>
                                    </div>
                                </div>
                                <div className="mt-4 text-center">
                                    <p className="text-sm text-muted-foreground">Total Outstanding</p>
                                    <p className="text-3xl font-bold text-primary">{formatCurrency(totalOutstanding)}</p>
                                </div>
                            </CardContent>
                        </Card>

                        {/* Payment Progress */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center space-x-2">
                                    <TrendingUp className="h-5 w-5" />
                                    <span>Payment Progress</span>
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="space-y-4">
                                    <div className="flex justify-between text-sm">
                                        <span>Amount Paid</span>
                                        <span>{formatCurrency(loanAccount.amount_paid)} / {formatCurrency(loanAccount.total_payable)}</span>
                                    </div>
                                    <div className="w-full bg-gray-200 rounded-full h-3">
                                        <div 
                                            className="bg-blue-600 h-3 rounded-full transition-all duration-300" 
                                            style={{ width: `${Math.min(paymentProgress, 100)}%` }}
                                        ></div>
                                    </div>
                                    <div className="grid gap-4 md:grid-cols-3 text-center">
                                        <div>
                                            <p className="text-sm text-muted-foreground">Principal Paid</p>
                                            <p className="font-medium">{formatCurrency(loanAccount.principal_paid)}</p>
                                        </div>
                                        <div>
                                            <p className="text-sm text-muted-foreground">Interest Paid</p>
                                            <p className="font-medium">{formatCurrency(loanAccount.interest_paid)}</p>
                                        </div>
                                        <div>
                                            <p className="text-sm text-muted-foreground">Fees Paid</p>
                                            <p className="font-medium">{formatCurrency(loanAccount.fees_paid)}</p>
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        {/* Ledger Entries */}
                        {loanAccount.ledger_entries.length > 0 && (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center space-x-2">
                                        <FileText className="h-5 w-5" />
                                        <span>Transaction History</span>
                                    </CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="space-y-3">
                                        {loanAccount.ledger_entries.map((entry) => (
                                            <div key={entry.id} className="flex items-center justify-between p-3 border rounded-lg">
                                                <div>
                                                    <h4 className="font-medium">{entry.description}</h4>
                                                    <p className="text-sm text-muted-foreground">
                                                        {getTransactionTypeLabel(entry.transaction_type)} • {formatDateTime(entry.transaction_date)}
                                                    </p>
                                                    {entry.reference_number && (
                                                        <p className="text-xs text-muted-foreground">
                                                            Ref: {entry.reference_number}
                                                        </p>
                                                    )}
                                                </div>
                                                <div className="text-right">
                                                    <p className={`font-medium ${entry.transaction_type === 'disbursement' ? 'text-green-600' : 'text-red-600'}`}>
                                                        {entry.transaction_type === 'disbursement' ? '+' : '-'}{formatCurrency(entry.amount)}
                                                    </p>
                                                    <p className="text-sm text-muted-foreground">
                                                        Balance: {formatCurrency(entry.balance_after)}
                                                    </p>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                </CardContent>
                            </Card>
                        )}
                    </div>

                    {/* Sidebar */}
                    <div className="lg:col-span-1 space-y-6">
                        {/* Member Information */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center space-x-2">
                                    <User className="h-5 w-5" />
                                    <span>Member Information</span>
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="space-y-3">
                                    <div className="flex justify-between">
                                        <span className="text-sm text-muted-foreground">Name:</span>
                                        <span className="font-medium">{loanAccount.member.name}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-sm text-muted-foreground">Member Number:</span>
                                        <span className="font-medium">{loanAccount.member.member_number}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-sm text-muted-foreground">Email:</span>
                                        <span className="font-medium">{loanAccount.member.email}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-sm text-muted-foreground">Phone:</span>
                                        <span className="font-medium">{loanAccount.member.phone}</span>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        {/* Payment Schedule */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center space-x-2">
                                    <Calendar className="h-5 w-5" />
                                    <span>Payment Schedule</span>
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="space-y-3">
                                    <div className="flex justify-between">
                                        <span className="text-sm text-muted-foreground">First Payment:</span>
                                        <span className="font-medium">{formatDate(loanAccount.first_payment_date)}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-sm text-muted-foreground">Next Payment:</span>
                                        <span className="font-medium">{formatDate(loanAccount.next_payment_date)}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-sm text-muted-foreground">Maturity Date:</span>
                                        <span className="font-medium">{formatDate(loanAccount.maturity_date)}</span>
                                    </div>
                                    {loanAccount.last_payment_date && (
                                        <div className="flex justify-between">
                                            <span className="text-sm text-muted-foreground">Last Payment:</span>
                                            <span className="font-medium">{formatDate(loanAccount.last_payment_date)}</span>
                                        </div>
                                    )}
                                </div>
                            </CardContent>
                        </Card>

                        {/* Arrears Alert */}
                        {arrears.arrears_amount > 0 && (
                            <Alert variant="destructive">
                                <AlertCircle className="h-4 w-4" />
                                <AlertDescription>
                                    <strong>Overdue Payment:</strong><br />
                                    {formatCurrency(arrears.arrears_amount)}<br />
                                    ({arrears.arrears_days} days overdue)
                                </AlertDescription>
                            </Alert>
                        )}

                        {/* Loan Type Info */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center space-x-2">
                                    <CreditCard className="h-5 w-5" />
                                    <span>Loan Information</span>
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="space-y-3">
                                    <div>
                                        <h4 className="font-semibold">{loanAccount.loan.loan_type.name}</h4>
                                        <p className="text-sm text-muted-foreground mt-1">
                                            Disbursed on {formatDate(loanAccount.disbursement_date)}
                                        </p>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-sm text-muted-foreground">Processing Fee:</span>
                                        <span className="font-medium">{formatCurrency(loanAccount.processing_fee)}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-sm text-muted-foreground">Insurance Fee:</span>
                                        <span className="font-medium">{formatCurrency(loanAccount.insurance_fee)}</span>
                                    </div>
                                    {loanAccount.notes && (
                                        <div className="mt-4 p-3 bg-muted/50 rounded-lg">
                                            <p className="text-sm text-muted-foreground">{loanAccount.notes}</p>
                                        </div>
                                    )}
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
