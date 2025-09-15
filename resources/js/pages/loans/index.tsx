import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { 
    Plus, 
    DollarSign, 
    Clock, 
    CheckCircle, 
    AlertCircle,
    Search,
    Eye,
    Filter,
    Calendar,
    User,
    CreditCard
} from 'lucide-react';
import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { index as loansIndex, create as loansCreate, show as loansShow } from '@/routes/loans';

interface LoanAccount {
    id: number;
    account_number: string;
    status: string;
    outstanding_principal: number;
    monthly_payment: number;
    next_payment_date: string;
    arrears_amount: number;
    arrears_days: number;
}

interface Loan {
    id: number;
    amount: number;
    interest_rate: number;
    term_period: number;
    status: string;
    due_date: string;
    created_at: string;
    member: {
        id: number;
        name: string;
        member_number: string;
    };
    loan_type: {
        id: number;
        name: string;
        interest_rate: number;
    };
    loan_account: LoanAccount | null;
}

interface LoanType {
    id: number;
    name: string;
    interest_rate: number;
    minimum_amount: number;
    maximum_amount: number;
}

interface Props {
    loans: {
        data: Loan[];
        links: any[];
        total: number;
        per_page: number;
        current_page: number;
        last_page: number;
        from: number;
        to: number;
    };
    loanTypes: LoanType[];
    stats: {
        totalLoans: number;
        totalAmount: number;
        activeLoans: number;
        pendingLoans: number;
    };
    filters: {
        search?: string;
        status?: string;
        loan_type?: string;
    };
    statusOptions: Record<string, string>;
}

export default function LoansIndex({ loans, loanTypes, stats, filters, statusOptions }: Props) {
    const [search, setSearch] = useState(filters.search || '');
    const [status, setStatus] = useState(filters.status || 'all');
    const [loanType, setLoanType] = useState(filters.loan_type || 'all');

    const handleSearch = () => {
        router.get(loansIndex.url(), {
            search: search || undefined,
            status: status === 'all' ? undefined : status,
            loan_type: loanType === 'all' ? undefined : loanType,
        }, {
            preserveState: true,
            replace: true,
        });
    };

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
            month: 'short',
            day: 'numeric',
        });
    };

    const formatDateShort = (date: string) => {
        return new Date(date).toLocaleDateString('en-KE', {
            month: 'short',
            day: 'numeric',
        });
    };

    return (
        <AppLayout>
            <Head title="Loans" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">Loans</h1>
                        <p className="text-muted-foreground">
                            Manage loan applications and active loans
                        </p>
                    </div>
                    <Link href={loansCreate.url()}>
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            Apply for Loan
                        </Button>
                    </Link>
                </div>

                {/* Stats Cards */}
                <div className="grid auto-rows-min gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Loans</CardTitle>
                            <DollarSign className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.totalLoans}</div>
                            <p className="text-xs text-muted-foreground">
                                All time
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Pending Applications</CardTitle>
                            <Clock className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.pendingLoans}</div>
                            <p className="text-xs text-muted-foreground">
                                Requires review
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Active Loans</CardTitle>
                            <CheckCircle className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.activeLoans}</div>
                            <p className="text-xs text-muted-foreground">
                                Currently active
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Amount</CardTitle>
                            <AlertCircle className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{formatCurrency(stats.totalAmount)}</div>
                            <p className="text-xs text-muted-foreground">
                                All loans
                            </p>
                        </CardContent>
                    </Card>
                </div>

                {/* Search and Filters */}
                <Card>
                    <CardHeader>
                        <CardTitle>Search Loans</CardTitle>
                        <CardDescription>
                            Find loans by member name, loan ID, or amount
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="flex items-center space-x-2">
                            <div className="relative flex-1">
                                <Search className="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                                <Input 
                                    placeholder="Search loans..." 
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
                            <Select value={loanType} onValueChange={setLoanType}>
                                <SelectTrigger className="w-[180px]">
                                    <SelectValue placeholder="Loan Type" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">All Types</SelectItem>
                                    {loanTypes.map((type) => (
                                        <SelectItem key={type.id} value={type.id.toString()}>
                                            {type.name}
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

                {/* Loans Table */}
                <Card>
                    <CardHeader>
                        <CardTitle>Loans</CardTitle>
                        <CardDescription>
                            All loans in your SACCO ({loans.total} total)
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        {loans.data.length > 0 ? (
                            <div className="space-y-4">
                                {loans.data.map((loan) => (
                                    <div key={loan.id} className="flex items-center justify-between p-4 border rounded-lg">
                                        <div className="flex items-center space-x-4">
                                            <div className="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center">
                                                <DollarSign className="h-5 w-5 text-primary" />
                                            </div>
                                            <div>
                                                <h3 className="font-semibold">Loan #{loan.id}</h3>
                                                <p className="text-sm text-muted-foreground">
                                                    {loan.member.name} • {loan.member.member_number}
                                                </p>
                                                <p className="text-sm text-muted-foreground">
                                                    {loan.loan_type.name} • Applied {formatDateShort(loan.created_at)}
                                                </p>
                                            </div>
                                        </div>
                                        <div className="flex items-center space-x-4">
                                            <div className="text-right">
                                                <p className="font-medium">{formatCurrency(loan.amount)}</p>
                                                <p className="text-sm text-muted-foreground">
                                                    {loan.interest_rate}% • {loan.term_period} months
                                                </p>
                                                <p className="text-sm text-muted-foreground">
                                                    Due: {formatDate(loan.due_date)}
                                                </p>
                                                {loan.loan_account && (
                                                    <div className="mt-2 text-xs text-muted-foreground">
                                                        <p>Account: {loan.loan_account.account_number}</p>
                                                        <p>Outstanding: {formatCurrency(loan.loan_account.outstanding_principal)}</p>
                                                        {loan.loan_account.arrears_amount > 0 && (
                                                            <p className="text-red-600 font-medium">
                                                                Overdue: {formatCurrency(loan.loan_account.arrears_amount)}
                                                            </p>
                                                        )}
                                                    </div>
                                                )}
                                            </div>
                                            <div className="flex items-center space-x-2">
                                                {getStatusBadge(loan.status)}
                                                {loan.loan_account && (
                                                    <Badge variant="outline" className="text-xs">
                                                        {loan.loan_account.status}
                                                    </Badge>
                                                )}
                                                <Link href={loansShow.url(loan.id)}>
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
                                <DollarSign className="mx-auto h-12 w-12 mb-4" />
                                <p>No loans found</p>
                                <p className="text-sm">Loans will appear here once they are created</p>
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}