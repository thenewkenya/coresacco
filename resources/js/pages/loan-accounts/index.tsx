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
    CreditCard,
    TrendingUp,
    TrendingDown
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

interface LoanAccount {
    id: number;
    account_number: string;
    loan_type: string;
    principal_amount: number;
    interest_rate: number;
    term_months: number;
    monthly_payment: number;
    outstanding_principal: number;
    outstanding_interest: number;
    outstanding_fees: number;
    arrears_amount: number;
    arrears_days: number;
    next_payment_date: string;
    status: string;
    disbursement_date: string;
    member: {
        id: number;
        name: string;
        member_number: string;
    };
    loan: {
        id: number;
        loan_type: {
            name: string;
        };
    };
}

interface Stats {
    totalAccounts: number;
    totalDisbursed: number;
    totalOutstanding: number;
    activeAccounts: number;
}

interface Props {
    loanAccounts: {
        data: LoanAccount[];
        links: any[];
        total: number;
        per_page: number;
        current_page: number;
        last_page: number;
        from: number;
        to: number;
    };
    stats: Stats;
    filters: {
        search?: string;
        status?: string;
        loan_type?: string;
    };
    statusOptions: Record<string, string>;
    loanTypeOptions: Record<string, string>;
}

export default function LoanAccountsIndex({ 
    loanAccounts, 
    stats, 
    filters, 
    statusOptions, 
    loanTypeOptions 
}: Props) {
    const [search, setSearch] = useState(filters.search || '');
    const [status, setStatus] = useState(filters.status || 'all');
    const [loanType, setLoanType] = useState(filters.loan_type || 'all');

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

    const handleSearch = () => {
        const params = new URLSearchParams();
        if (search) params.append('search', search);
        if (status !== 'all') params.append('status', status);
        if (loanType !== 'all') params.append('loan_type', loanType);
        
        router.get('/loan-accounts', Object.fromEntries(params), {
            preserveState: true,
            replace: true,
        });
    };

    return (
        <AppLayout>
            <Head title="Loan Accounts" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">Loan Accounts</h1>
                        <p className="text-muted-foreground">
                            Manage and track all loan accounts
                        </p>
                    </div>
                </div>

                {/* Stats Cards */}
                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Accounts</CardTitle>
                            <CreditCard className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.totalAccounts}</div>
                            <p className="text-xs text-muted-foreground">
                                All loan accounts
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Disbursed</CardTitle>
                            <TrendingUp className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{formatCurrency(stats.totalDisbursed)}</div>
                            <p className="text-xs text-muted-foreground">
                                Amount disbursed
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Outstanding</CardTitle>
                            <AlertCircle className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{formatCurrency(stats.totalOutstanding)}</div>
                            <p className="text-xs text-muted-foreground">
                                Still owed
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Active Accounts</CardTitle>
                            <CheckCircle className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.activeAccounts}</div>
                            <p className="text-xs text-muted-foreground">
                                Currently active
                            </p>
                        </CardContent>
                    </Card>
                </div>

                {/* Search and Filters */}
                <Card>
                    <CardHeader>
                        <CardTitle>Search Loan Accounts</CardTitle>
                        <CardDescription>
                            Find loan accounts by account number, member name, or amount
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="flex items-center space-x-2">
                            <div className="relative flex-1">
                                <Search className="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                                <Input 
                                    placeholder="Search loan accounts..." 
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
                                    {Object.entries(loanTypeOptions).map(([value, label]) => (
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

                {/* Loan Accounts Table */}
                <Card>
                    <CardHeader>
                        <CardTitle>Loan Accounts</CardTitle>
                        <CardDescription>
                            All loan accounts in your SACCO ({loanAccounts.total} total)
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        {loanAccounts.data.length > 0 ? (
                            <div className="space-y-4">
                                {loanAccounts.data.map((account) => (
                                    <div key={account.id} className="flex items-center justify-between p-4 border rounded-lg">
                                        <div className="flex items-center space-x-4">
                                            <div className="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center">
                                                <CreditCard className="h-5 w-5 text-primary" />
                                            </div>
                                            <div>
                                                <h3 className="font-semibold">{account.account_number}</h3>
                                                <p className="text-sm text-muted-foreground">
                                                    {account.member.name} • {account.member.member_number}
                                                </p>
                                                <p className="text-sm text-muted-foreground">
                                                    {account.loan.loan_type.name} • Disbursed {formatDateShort(account.disbursement_date)}
                                                </p>
                                            </div>
                                        </div>
                                        <div className="flex items-center space-x-4">
                                            <div className="text-right">
                                                <p className="font-medium">{formatCurrency(account.outstanding_principal)}</p>
                                                <p className="text-sm text-muted-foreground">
                                                    Monthly: {formatCurrency(account.monthly_payment)}
                                                </p>
                                                <p className="text-sm text-muted-foreground">
                                                    Next Due: {formatDate(account.next_payment_date)}
                                                </p>
                                                {account.arrears_amount > 0 && (
                                                    <p className="text-sm text-red-600 font-medium">
                                                        Overdue: {formatCurrency(account.arrears_amount)}
                                                    </p>
                                                )}
                                            </div>
                                            <div className="flex items-center space-x-2">
                                                {getStatusBadge(account.status)}
                                                <Link href={`/loan-accounts/${account.id}`}>
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
                                <p>No loan accounts found</p>
                                <p className="text-sm">Loan accounts will appear here once loans are disbursed</p>
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
