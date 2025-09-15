import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Textarea } from '@/components/ui/textarea';
import { Label } from '@/components/ui/label';
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
import { Head, Link, router, usePage } from '@inertiajs/react';
import { useState } from 'react';
import { index as loansIndex, edit as loansEdit } from '@/routes/loans';

interface Transaction {
    id: number;
    type: string;
    amount: number;
    description: string;
    reference_number: string;
    status: string;
    created_at: string;
    balance_before: number;
    balance_after: number;
}

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
    ledger_entries: LedgerEntry[];
}

interface Loan {
    id: number;
    amount: number;
    interest_rate: number;
    term_period: number;
    status: string;
    disbursement_date: string;
    due_date: string;
    collateral_details: string;
    created_at: string;
    required_savings_multiplier: number;
    minimum_savings_balance: number;
    member_savings_balance: number;
    member_shares_balance: number;
    member_total_balance: number;
    minimum_membership_months: number;
    member_months_in_sacco: number;
    meets_savings_criteria: boolean;
    meets_membership_criteria: boolean;
    criteria_evaluation_notes: string;
    member: {
        id: number;
        name: string;
        member_number: string;
        email: string;
        phone: string;
    };
    loan_type: {
        id: number;
        name: string;
        interest_rate: number;
        description: string;
    };
    transactions: Transaction[];
    loan_account: LoanAccount | null;
}

interface Props {
    loan: Loan;
}

export default function ShowLoan({ loan }: Props) {
    const { auth } = usePage().props as { auth: { user: any } };
    const user = auth.user;
    
    const [notes, setNotes] = useState('');
    const [rejectionReason, setRejectionReason] = useState('');
    const [isUpdating, setIsUpdating] = useState(false);

    const getStatusBadge = (status: string) => {
        switch (status) {
            case 'pending':
                return <Badge variant="outline">Pending</Badge>;
            case 'approved':
                return <Badge variant="secondary">Approved</Badge>;
            case 'disbursed':
                return <Badge variant="default">Disbursed</Badge>;
            case 'active':
                return <Badge variant="default">Active</Badge>;
            case 'completed':
                return <Badge variant="secondary">Completed</Badge>;
            case 'defaulted':
                return <Badge variant="destructive">Defaulted</Badge>;
            case 'rejected':
                return <Badge variant="destructive">Rejected</Badge>;
            default:
                return <Badge variant="outline">{status}</Badge>;
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

    const calculateMonthlyPayment = (principal: number, rate: number, months: number) => {
        if (rate === 0) return principal / months;
        const monthlyRate = rate / 100 / 12;
        return principal * (monthlyRate * Math.pow(1 + monthlyRate, months)) / (Math.pow(1 + monthlyRate, months) - 1);
    };

    const monthlyPayment = calculateMonthlyPayment(loan.amount, loan.interest_rate, loan.term_period);
    const totalPayment = monthlyPayment * loan.term_period;
    const totalInterest = totalPayment - loan.amount;

    const handleApprove = () => {
        setIsUpdating(true);
        router.post(`/loans/${loan.id}/approve`, { notes }, {
            onFinish: () => setIsUpdating(false),
        });
    };

    const handleReject = () => {
        if (!rejectionReason.trim()) {
            alert('Please provide a reason for rejection');
            return;
        }
        setIsUpdating(true);
        router.post(`/loans/${loan.id}/reject`, { rejection_reason: rejectionReason }, {
            onFinish: () => setIsUpdating(false),
        });
    };

    const handleDisburse = () => {
        setIsUpdating(true);
        router.post(`/loans/${loan.id}/disburse`, {}, {
            onFinish: () => setIsUpdating(false),
        });
    };

    return (
        <AppLayout>
            <Head title={`Loan #${loan.id}`} />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div className="flex items-center space-x-4">
                        <Link href={loansIndex.url()}>
                            <Button variant="outline" size="sm">
                                <ArrowLeft className="mr-2 h-4 w-4" />
                                Back to Loans
                            </Button>
                        </Link>
                        <div>
                            <h1 className="text-3xl font-bold tracking-tight">Loan #{loan.id}</h1>
                            <p className="text-muted-foreground">
                                {loan.member.name} • {loan.loan_type.name}
                            </p>
                        </div>
                    </div>
                    <div className="flex items-center space-x-2">
                        {getStatusBadge(loan.status)}
                        {user.role !== 'member' && (
                            <Link href={loansEdit.url(loan.id)}>
                                <Button variant="outline" size="sm">
                                    <Eye className="mr-2 h-4 w-4" />
                                    Edit
                                </Button>
                            </Link>
                        )}
                    </div>
                </div>

                <div className="grid gap-6 lg:grid-cols-3">
                    {/* Main Content */}
                    <div className="lg:col-span-2 space-y-6">
                        {/* Loan Details */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center space-x-2">
                                    <DollarSign className="h-5 w-5" />
                                    <span>Loan Details</span>
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="grid gap-4 md:grid-cols-2">
                                    <div className="space-y-3">
                                        <div className="flex justify-between">
                                            <span className="text-sm text-muted-foreground">Loan Amount:</span>
                                            <span className="font-medium">{formatCurrency(loan.amount)}</span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span className="text-sm text-muted-foreground">Interest Rate:</span>
                                            <span className="font-medium">{loan.interest_rate}%</span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span className="text-sm text-muted-foreground">Term Period:</span>
                                            <span className="font-medium">{loan.term_period} months</span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span className="text-sm text-muted-foreground">Monthly Payment:</span>
                                            <span className="font-medium">{formatCurrency(monthlyPayment)}</span>
                                        </div>
                                    </div>
                                    <div className="space-y-3">
                                        <div className="flex justify-between">
                                            <span className="text-sm text-muted-foreground">Total Interest:</span>
                                            <span className="font-medium">{formatCurrency(totalInterest)}</span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span className="text-sm text-muted-foreground">Total Payment:</span>
                                            <span className="font-medium">{formatCurrency(totalPayment)}</span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span className="text-sm text-muted-foreground">Due Date:</span>
                                            <span className="font-medium">{formatDate(loan.due_date)}</span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span className="text-sm text-muted-foreground">Applied On:</span>
                                            <span className="font-medium">{formatDate(loan.created_at)}</span>
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        {/* Loan Account Information */}
                        {loan.loan_account && (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center space-x-2">
                                        <CreditCard className="h-5 w-5" />
                                        <span>Loan Account Details</span>
                                    </CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="grid gap-4 md:grid-cols-2">
                                        <div className="space-y-3">
                                            <div className="flex justify-between">
                                                <span className="text-sm text-muted-foreground">Account Number:</span>
                                                <span className="font-medium font-mono">{loan.loan_account.account_number}</span>
                                            </div>
                                            <div className="flex justify-between">
                                                <span className="text-sm text-muted-foreground">Loan Type:</span>
                                                <span className="font-medium capitalize">{loan.loan_account.loan_type.replace('_', ' ')}</span>
                                            </div>
                                            <div className="flex justify-between">
                                                <span className="text-sm text-muted-foreground">Interest Basis:</span>
                                                <span className="font-medium capitalize">{loan.loan_account.interest_basis.replace('_', ' ')}</span>
                                            </div>
                                            <div className="flex justify-between">
                                                <span className="text-sm text-muted-foreground">Monthly Payment:</span>
                                                <span className="font-medium">{formatCurrency(loan.loan_account.monthly_payment)}</span>
                                            </div>
                                        </div>
                                        <div className="space-y-3">
                                            <div className="flex justify-between">
                                                <span className="text-sm text-muted-foreground">Outstanding Principal:</span>
                                                <span className="font-medium">{formatCurrency(loan.loan_account.outstanding_principal)}</span>
                                            </div>
                                            <div className="flex justify-between">
                                                <span className="text-sm text-muted-foreground">Outstanding Interest:</span>
                                                <span className="font-medium">{formatCurrency(loan.loan_account.outstanding_interest)}</span>
                                            </div>
                                            <div className="flex justify-between">
                                                <span className="text-sm text-muted-foreground">Total Outstanding:</span>
                                                <span className="font-medium">{formatCurrency(loan.loan_account.outstanding_principal + loan.loan_account.outstanding_interest + loan.loan_account.outstanding_fees)}</span>
                                            </div>
                                            <div className="flex justify-between">
                                                <span className="text-sm text-muted-foreground">Next Payment Due:</span>
                                                <span className="font-medium">{formatDate(loan.loan_account.next_payment_date)}</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    {/* Payment Progress */}
                                    <div className="mt-6">
                                        <div className="flex justify-between text-sm mb-2">
                                            <span>Payment Progress</span>
                                            <span>{formatCurrency(loan.loan_account.amount_paid)} / {formatCurrency(loan.loan_account.total_payable)}</span>
                                        </div>
                                        <div className="w-full bg-gray-200 rounded-full h-2">
                                            <div 
                                                className="bg-blue-600 h-2 rounded-full" 
                                                style={{ 
                                                    width: `${Math.min((loan.loan_account.amount_paid / loan.loan_account.total_payable) * 100, 100)}%` 
                                                }}
                                            ></div>
                                        </div>
                                    </div>

                                    {/* Arrears Warning */}
                                    {loan.loan_account.arrears_amount > 0 && (
                                        <Alert className="mt-4" variant="destructive">
                                            <AlertCircle className="h-4 w-4" />
                                            <AlertDescription>
                                                <strong>Overdue Payment:</strong> {formatCurrency(loan.loan_account.arrears_amount)} 
                                                ({loan.loan_account.arrears_days} days overdue)
                                            </AlertDescription>
                                        </Alert>
                                    )}
                                </CardContent>
                            </Card>
                        )}

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
                                    <div className="space-y-3">
                                        <div className="flex justify-between">
                                            <span className="text-sm text-muted-foreground">Name:</span>
                                            <span className="font-medium">{loan.member.name}</span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span className="text-sm text-muted-foreground">Member Number:</span>
                                            <span className="font-medium">{loan.member.member_number}</span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span className="text-sm text-muted-foreground">Email:</span>
                                            <span className="font-medium">{loan.member.email}</span>
                                        </div>
                                    </div>
                                    <div className="space-y-3">
                                        <div className="flex justify-between">
                                            <span className="text-sm text-muted-foreground">Phone:</span>
                                            <span className="font-medium">{loan.member.phone}</span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span className="text-sm text-muted-foreground">Months in SACCO:</span>
                                            <span className="font-medium">{loan.member_months_in_sacco}</span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span className="text-sm text-muted-foreground">Total Balance:</span>
                                            <span className="font-medium">{formatCurrency(loan.member_total_balance)}</span>
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        {/* Eligibility Criteria */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center space-x-2">
                                    <CheckCircle className="h-5 w-5" />
                                    <span>Eligibility Criteria</span>
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="space-y-4">
                                    <div className="grid gap-4 md:grid-cols-2">
                                        <div className="space-y-3">
                                            <div className="flex items-center justify-between">
                                                <span className="text-sm text-muted-foreground">Savings Criteria:</span>
                                                <div className="flex items-center space-x-2">
                                                    {loan.meets_savings_criteria ? (
                                                        <CheckCircle className="h-4 w-4 text-green-600" />
                                                    ) : (
                                                        <AlertCircle className="h-4 w-4 text-red-600" />
                                                    )}
                                                    <span className={`text-sm font-medium ${loan.meets_savings_criteria ? 'text-green-600' : 'text-red-600'}`}>
                                                        {loan.meets_savings_criteria ? 'Met' : 'Not Met'}
                                                    </span>
                                                </div>
                                            </div>
                                            <div className="flex justify-between">
                                                <span className="text-sm text-muted-foreground">Required:</span>
                                                <span className="font-medium">{formatCurrency(loan.minimum_savings_balance)}</span>
                                            </div>
                                            <div className="flex justify-between">
                                                <span className="text-sm text-muted-foreground">Current:</span>
                                                <span className="font-medium">{formatCurrency(loan.member_total_balance)}</span>
                                            </div>
                                        </div>
                                        <div className="space-y-3">
                                            <div className="flex items-center justify-between">
                                                <span className="text-sm text-muted-foreground">Membership Criteria:</span>
                                                <div className="flex items-center space-x-2">
                                                    {loan.meets_membership_criteria ? (
                                                        <CheckCircle className="h-4 w-4 text-green-600" />
                                                    ) : (
                                                        <AlertCircle className="h-4 w-4 text-red-600" />
                                                    )}
                                                    <span className={`text-sm font-medium ${loan.meets_membership_criteria ? 'text-green-600' : 'text-red-600'}`}>
                                                        {loan.meets_membership_criteria ? 'Met' : 'Not Met'}
                                                    </span>
                                                </div>
                                            </div>
                                            <div className="flex justify-between">
                                                <span className="text-sm text-muted-foreground">Required:</span>
                                                <span className="font-medium">{loan.minimum_membership_months} months</span>
                                            </div>
                                            <div className="flex justify-between">
                                                <span className="text-sm text-muted-foreground">Current:</span>
                                                <span className="font-medium">{loan.member_months_in_sacco} months</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    {/* Show evaluation notes for staff, simplified summary for members */}
                                    {loan.criteria_evaluation_notes && (
                                        <div className="mt-4 p-4 bg-muted/50 rounded-lg border">
                                            <h4 className="font-medium mb-3 text-sm">
                                                {user.role === 'member' ? 'Eligibility Summary:' : 'Evaluation Summary:'}
                                            </h4>
                                            <div className="space-y-2 text-sm">
                                                {loan.criteria_evaluation_notes.split('\n').map((note, index) => (
                                                    <div key={index} className="flex items-start space-x-2">
                                                        <span className="text-muted-foreground mt-0.5">
                                                            {note.startsWith('✓') ? '✓' : note.startsWith('✗') ? '✗' : '•'}
                                                        </span>
                                                        <span className="text-muted-foreground">
                                                            {note.replace(/^[✓✗•]\s*/, '')}
                                                        </span>
                                                    </div>
                                                ))}
                                            </div>
                                            {user.role === 'member' && (
                                                <Alert className="mt-3">
                                                    <AlertDescription>
                                                        💡 <strong>Tip:</strong> You can improve your eligibility by increasing your savings balance or waiting longer as a member.
                                                    </AlertDescription>
                                                </Alert>
                                            )}
                                        </div>
                                    )}
                                </div>
                            </CardContent>
                        </Card>

                        {/* Collateral Details */}
                        {loan.collateral_details && (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center space-x-2">
                                        <FileText className="h-5 w-5" />
                                        <span>Collateral Details</span>
                                    </CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <p className="text-sm text-muted-foreground">{loan.collateral_details}</p>
                                </CardContent>
                            </Card>
                        )}

                        {/* Loan Account Ledger Entries */}
                        {loan.loan_account && loan.loan_account.ledger_entries.length > 0 && (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center space-x-2">
                                        <FileText className="h-5 w-5" />
                                        <span>Loan Account Transactions</span>
                                    </CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="space-y-3">
                                        {loan.loan_account.ledger_entries.slice(0, 10).map((entry) => (
                                            <div key={entry.id} className="flex items-center justify-between p-3 border rounded-lg">
                                                <div>
                                                    <h4 className="font-medium">{entry.description}</h4>
                                                    <p className="text-sm text-muted-foreground">
                                                        {entry.transaction_type.replace('_', ' ')} • {formatDateTime(entry.transaction_date)}
                                                    </p>
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

                        {/* Recent Transactions */}
                        {loan.transactions.length > 0 && (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center space-x-2">
                                        <CreditCard className="h-5 w-5" />
                                        <span>Recent Transactions</span>
                                    </CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="space-y-3">
                                        {loan.transactions.slice(0, 5).map((transaction) => (
                                            <div key={transaction.id} className="flex items-center justify-between p-3 border rounded-lg">
                                                <div>
                                                    <h4 className="font-medium">{transaction.description}</h4>
                                                    <p className="text-sm text-muted-foreground">
                                                        Ref: {transaction.reference_number} • {formatDateTime(transaction.created_at)}
                                                    </p>
                                                </div>
                                                <div className="text-right">
                                                    <p className={`font-medium ${transaction.type === 'loan_disbursement' ? 'text-green-600' : 'text-red-600'}`}>
                                                        {transaction.type === 'loan_disbursement' ? '+' : '-'}{formatCurrency(transaction.amount)}
                                                    </p>
                                                    <p className="text-sm text-muted-foreground">
                                                        Balance: {formatCurrency(transaction.balance_after)}
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
                        {/* Status Management - Staff Only */}
                        {loan.status === 'pending' && user.role !== 'member' && (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center space-x-2">
                                        <Clock className="h-5 w-5" />
                                        <span>Status Management</span>
                                    </CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="space-y-2">
                                        <Label htmlFor="notes">Approval Notes (Optional)</Label>
                                        <Textarea
                                            id="notes"
                                            placeholder="Add notes for approval..."
                                            value={notes}
                                            onChange={(e) => setNotes(e.target.value)}
                                            rows={3}
                                        />
                                    </div>
                                    <div className="space-y-2">
                                        <Button 
                                            onClick={handleApprove} 
                                            disabled={isUpdating}
                                            className="w-full"
                                        >
                                            <CheckCircle className="mr-2 h-4 w-4" />
                                            Approve Loan
                                        </Button>
                                    </div>
                                    <div className="space-y-2">
                                        <Label htmlFor="rejection_reason">Rejection Reason</Label>
                                        <Textarea
                                            id="rejection_reason"
                                            placeholder="Provide reason for rejection..."
                                            value={rejectionReason}
                                            onChange={(e) => setRejectionReason(e.target.value)}
                                            rows={3}
                                        />
                                    </div>
                                    <Button 
                                        onClick={handleReject} 
                                        disabled={isUpdating || !rejectionReason.trim()}
                                        variant="destructive"
                                        className="w-full"
                                    >
                                        <AlertCircle className="mr-2 h-4 w-4" />
                                        Reject Loan
                                    </Button>
                                </CardContent>
                            </Card>
                        )}

                        {/* Member Status Information */}
                        {loan.status === 'pending' && user.role === 'member' && (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center space-x-2">
                                        <Clock className="h-5 w-5" />
                                        <span>Application Status</span>
                                    </CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <p className="text-sm text-muted-foreground mb-4">
                                        Your loan application is currently under review by our staff. 
                                        You will be notified once a decision has been made.
                                    </p>
                                    <div className="space-y-2">
                                        <div className="flex items-center space-x-2">
                                            <Clock className="h-4 w-4 text-yellow-600" />
                                            <span className="text-sm">Status: Under Review</span>
                                        </div>
                                        <div className="flex items-center space-x-2">
                                            <AlertCircle className="h-4 w-4 text-blue-600" />
                                            <span className="text-sm">You will be notified of the decision</span>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>
                        )}

                        {/* Disbursement - Staff Only */}
                        {loan.status === 'approved' && user.role !== 'member' && (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center space-x-2">
                                        <TrendingUp className="h-5 w-5" />
                                        <span>Disburse Loan</span>
                                    </CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <p className="text-sm text-muted-foreground mb-4">
                                        This loan has been approved and is ready for disbursement.
                                    </p>
                                    <Button 
                                        onClick={handleDisburse} 
                                        disabled={isUpdating}
                                        className="w-full"
                                    >
                                        <TrendingUp className="mr-2 h-4 w-4" />
                                        Disburse Loan
                                    </Button>
                                </CardContent>
                            </Card>
                        )}

                        {/* Loan Type Info */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center space-x-2">
                                    <CreditCard className="h-5 w-5" />
                                    <span>Loan Type</span>
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="space-y-3">
                                    <div>
                                        <h4 className="font-semibold">{loan.loan_type.name}</h4>
                                        <p className="text-sm text-muted-foreground mt-1">
                                            {loan.loan_type.description}
                                        </p>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-sm text-muted-foreground">Interest Rate:</span>
                                        <span className="font-medium">{loan.loan_type.interest_rate}%</span>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
