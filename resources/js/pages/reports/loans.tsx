import { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { DatePicker } from '@/components/ui/date-picker';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { 
    ArrowLeft, 
    Download, 
    Calendar, 
    CreditCard, 
    TrendingUp,
    TrendingDown,
    AlertTriangle,
    CheckCircle,
    Clock,
    DollarSign,
    BarChart3,
    PieChart,
    FileText,
    RefreshCw,
    Percent
} from 'lucide-react';

interface LoanData {
    summary: {
        totalLoans: number;
        activeLoans: number;
        totalLoanAmount: number;
        totalOutstanding: number;
        totalDisbursed: number;
        totalRepaid: number;
        defaultRate: number;
        averageLoanSize: number;
        averageInterestRate: number;
    };
    performance: {
        disbursements: Array<{
            month: string;
            count: number;
            amount: number;
            averageSize: number;
        }>;
        repayments: Array<{
            month: string;
            amount: number;
            onTime: number;
            late: number;
            defaulted: number;
        }>;
        defaults: Array<{
            month: string;
            count: number;
            amount: number;
            percentage: number;
        }>;
    };
    portfolio: Array<{
        loanType: string;
        count: number;
        totalAmount: number;
        outstanding: number;
        defaultRate: number;
        averageSize: number;
    }>;
    topBorrowers: Array<{
        id: number;
        name: string;
        accountNumber: string;
        loanAmount: number;
        outstanding: number;
        interestRate: number;
        status: string;
        disbursementDate: string;
    }>;
    riskAnalysis: {
        riskCategories: Array<{
            category: string;
            count: number;
            amount: number;
            percentage: number;
        }>;
        overdueLoans: Array<{
            id: number;
            borrowerName: string;
            loanAmount: number;
            outstanding: number;
            daysOverdue: number;
            lastPayment: string;
        }>;
    };
}

export default function LoanReports() {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: dashboard().url,
        },
        {
            title: 'Reports',
            href: '#',
        },
        {
            title: 'Loan Reports',
            href: '#',
        },
    ];

    const [dateRange, setDateRange] = useState<{ from: Date | undefined; to: Date | undefined }>({
        from: new Date(new Date().getFullYear(), new Date().getMonth(), 1),
        to: new Date()
    });
    const [reportType, setReportType] = useState('summary');
    const [loanTypeFilter, setLoanTypeFilter] = useState('all');
    const [isLoading, setIsLoading] = useState(false);

    // Mock data - in real app, this would come from the backend
    const loanData: LoanData = {
        summary: {
            totalLoans: 450,
            activeLoans: 380,
            totalLoanAmount: 85000000,
            totalOutstanding: 42000000,
            totalDisbursed: 85000000,
            totalRepaid: 43000000,
            defaultRate: 3.2,
            averageLoanSize: 188889,
            averageInterestRate: 12.5
        },
        performance: {
            disbursements: [
                { month: 'Jan', count: 25, amount: 4500000, averageSize: 180000 },
                { month: 'Feb', count: 32, amount: 5800000, averageSize: 181250 },
                { month: 'Mar', count: 28, amount: 5200000, averageSize: 185714 },
                { month: 'Apr', count: 35, amount: 6500000, averageSize: 185714 },
                { month: 'May', count: 30, amount: 5600000, averageSize: 186667 },
                { month: 'Jun', count: 38, amount: 7200000, averageSize: 189474 }
            ],
            repayments: [
                { month: 'Jan', amount: 3200000, onTime: 85, late: 12, defaulted: 3 },
                { month: 'Feb', amount: 3400000, onTime: 88, late: 10, defaulted: 2 },
                { month: 'Mar', amount: 3100000, onTime: 82, late: 15, defaulted: 3 },
                { month: 'Apr', amount: 3600000, onTime: 90, late: 8, defaulted: 2 },
                { month: 'May', amount: 3300000, onTime: 86, late: 11, defaulted: 3 },
                { month: 'Jun', amount: 3800000, onTime: 92, late: 6, defaulted: 2 }
            ],
            defaults: [
                { month: 'Jan', count: 3, amount: 450000, percentage: 2.1 },
                { month: 'Feb', count: 2, amount: 320000, percentage: 1.8 },
                { month: 'Mar', count: 3, amount: 480000, percentage: 2.3 },
                { month: 'Apr', count: 2, amount: 280000, percentage: 1.6 },
                { month: 'May', count: 3, amount: 420000, percentage: 2.0 },
                { month: 'Jun', count: 2, amount: 350000, percentage: 1.9 }
            ]
        },
        portfolio: [
            { loanType: 'Personal', count: 180, totalAmount: 32000000, outstanding: 15000000, defaultRate: 2.8, averageSize: 177778 },
            { loanType: 'Business', count: 120, totalAmount: 28000000, outstanding: 14000000, defaultRate: 3.5, averageSize: 233333 },
            { loanType: 'Emergency', count: 80, totalAmount: 15000000, outstanding: 8000000, defaultRate: 4.2, averageSize: 187500 },
            { loanType: 'Education', count: 50, totalAmount: 8000000, outstanding: 4000000, defaultRate: 1.8, averageSize: 160000 },
            { loanType: 'Agricultural', count: 20, totalAmount: 3000000, outstanding: 1000000, defaultRate: 2.5, averageSize: 150000 }
        ],
        topBorrowers: [
            { id: 1, name: 'John Mwangi', accountNumber: 'ACC001', loanAmount: 2000000, outstanding: 1200000, interestRate: 12.0, status: 'active', disbursementDate: '2023-06-15' },
            { id: 2, name: 'Mary Wanjiku', accountNumber: 'ACC002', loanAmount: 1800000, outstanding: 1100000, interestRate: 11.5, status: 'active', disbursementDate: '2023-08-22' },
            { id: 3, name: 'Peter Kimani', accountNumber: 'ACC003', loanAmount: 1500000, outstanding: 900000, interestRate: 13.0, status: 'active', disbursementDate: '2023-09-10' },
            { id: 4, name: 'Grace Akinyi', accountNumber: 'ACC004', loanAmount: 1200000, outstanding: 750000, interestRate: 12.5, status: 'active', disbursementDate: '2023-10-05' },
            { id: 5, name: 'David Otieno', accountNumber: 'ACC005', loanAmount: 1000000, outstanding: 600000, interestRate: 11.0, status: 'active', disbursementDate: '2023-11-18' }
        ],
        riskAnalysis: {
            riskCategories: [
                { category: 'Low Risk', count: 280, amount: 52000000, percentage: 62.4 },
                { category: 'Medium Risk', count: 120, amount: 25000000, percentage: 29.4 },
                { category: 'High Risk', count: 50, amount: 8000000, percentage: 8.2 }
            ],
            overdueLoans: [
                { id: 1, borrowerName: 'Alice Wanjala', loanAmount: 500000, outstanding: 300000, daysOverdue: 45, lastPayment: '2023-11-15' },
                { id: 2, borrowerName: 'Robert Mwangi', loanAmount: 800000, outstanding: 450000, daysOverdue: 32, lastPayment: '2023-12-01' },
                { id: 3, borrowerName: 'Susan Kimani', loanAmount: 300000, outstanding: 200000, daysOverdue: 28, lastPayment: '2023-12-05' },
                { id: 4, borrowerName: 'Michael Otieno', loanAmount: 600000, outstanding: 350000, daysOverdue: 21, lastPayment: '2023-12-12' },
                { id: 5, borrowerName: 'Jane Akinyi', loanAmount: 400000, outstanding: 250000, daysOverdue: 15, lastPayment: '2023-12-18' }
            ]
        }
    };

    const formatCurrency = (amount: number) => {
        return new Intl.NumberFormat('en-KE', {
            style: 'currency',
            currency: 'KES',
        }).format(amount);
    };

    const formatPercentage = (value: number) => {
        return `${value.toFixed(1)}%`;
    };

    const handleExport = (format: 'pdf' | 'excel') => {
        setIsLoading(true);
        // In real app, this would call the backend export endpoint
        setTimeout(() => {
            setIsLoading(false);
            console.log(`Exporting ${format} report...`);
        }, 1000);
    };

    const getStatusBadge = (status: string) => {
        switch (status) {
            case 'active':
                return <Badge variant="default">Active</Badge>;
            case 'completed':
                return <Badge variant="secondary">Completed</Badge>;
            case 'defaulted':
                return <Badge variant="destructive">Defaulted</Badge>;
            case 'overdue':
                return <Badge variant="destructive">Overdue</Badge>;
            default:
                return <Badge variant="outline">{status}</Badge>;
        }
    };

    const getRiskBadge = (category: string) => {
        switch (category) {
            case 'Low Risk':
                return <Badge variant="default">Low Risk</Badge>;
            case 'Medium Risk':
                return <Badge variant="outline">Medium Risk</Badge>;
            case 'High Risk':
                return <Badge variant="destructive">High Risk</Badge>;
            default:
                return <Badge variant="outline">{category}</Badge>;
        }
    };

    const getOverdueBadge = (days: number) => {
        if (days <= 30) {
            return <Badge variant="outline">{days} days</Badge>;
        } else if (days <= 60) {
            return <Badge variant="secondary">{days} days</Badge>;
        } else {
            return <Badge variant="destructive">{days} days</Badge>;
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Loan Reports" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div className="flex items-center space-x-4">
                        <Link href="/reports">
                            <Button variant="outline" size="sm">
                                <ArrowLeft className="mr-2 h-4 w-4" />
                                Back to Reports
                            </Button>
                        </Link>
                        <div>
                            <h1 className="text-3xl font-bold tracking-tight">Loan Reports</h1>
                            <p className="text-muted-foreground">
                                Loan performance, portfolio analysis, and risk assessment
                            </p>
                        </div>
                    </div>
                    <div className="flex items-center space-x-2">
                        <Button 
                            variant="outline" 
                            onClick={() => handleExport('pdf')}
                            disabled={isLoading}
                        >
                            <FileText className="mr-2 h-4 w-4" />
                            Export PDF
                        </Button>
                        <Button 
                            variant="outline" 
                            onClick={() => handleExport('excel')}
                            disabled={isLoading}
                        >
                            <Download className="mr-2 h-4 w-4" />
                            Export Excel
                        </Button>
                    </div>
                </div>

                {/* Filters */}
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center">
                            <Calendar className="mr-2 h-5 w-5" />
                            Report Filters
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label className="text-sm font-medium mb-2 block">Date Range</label>
                                <div className="flex space-x-2">
                                    <DatePicker
                                        date={dateRange.from}
                                        setDate={(date) => setDateRange(prev => ({ ...prev, from: date }))}
                                        placeholder="From Date"
                                    />
                                    <DatePicker
                                        date={dateRange.to}
                                        setDate={(date) => setDateRange(prev => ({ ...prev, to: date }))}
                                        placeholder="To Date"
                                    />
                                </div>
                            </div>
                            <div>
                                <label className="text-sm font-medium mb-2 block">Report Type</label>
                                <Select value={reportType} onValueChange={setReportType}>
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select report type" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="summary">Summary</SelectItem>
                                        <SelectItem value="performance">Performance</SelectItem>
                                        <SelectItem value="portfolio">Portfolio</SelectItem>
                                        <SelectItem value="risk">Risk Analysis</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div>
                                <label className="text-sm font-medium mb-2 block">Loan Type</label>
                                <Select value={loanTypeFilter} onValueChange={setLoanTypeFilter}>
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select loan type" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="all">All Types</SelectItem>
                                        <SelectItem value="personal">Personal</SelectItem>
                                        <SelectItem value="business">Business</SelectItem>
                                        <SelectItem value="emergency">Emergency</SelectItem>
                                        <SelectItem value="education">Education</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div className="flex items-end">
                                <Button className="w-full">
                                    <RefreshCw className="mr-2 h-4 w-4" />
                                    Generate Report
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Summary Cards */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Loans</CardTitle>
                            <CreditCard className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{loanData.summary.totalLoans}</div>
                            <div className="flex items-center text-xs text-muted-foreground mt-1">
                                <span>{loanData.summary.activeLoans} active</span>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Outstanding</CardTitle>
                            <DollarSign className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{formatCurrency(loanData.summary.totalOutstanding)}</div>
                            <div className="flex items-center text-xs text-muted-foreground mt-1">
                                <span>{formatPercentage((loanData.summary.totalOutstanding / loanData.summary.totalLoanAmount) * 100)} of total</span>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Default Rate</CardTitle>
                            <AlertTriangle className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{formatPercentage(loanData.summary.defaultRate)}</div>
                            <div className="flex items-center text-xs text-muted-foreground mt-1">
                                <TrendingDown className="h-3 w-3 text-green-600 mr-1" />
                                <span>-0.3% from last month</span>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Average Interest</CardTitle>
                            <Percent className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{formatPercentage(loanData.summary.averageInterestRate)}</div>
                            <div className="flex items-center text-xs text-muted-foreground mt-1">
                                <span>Avg loan: {formatCurrency(loanData.summary.averageLoanSize)}</span>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Loan Performance */}
                <Card>
                    <CardHeader>
                        <CardTitle>Loan Performance Trends</CardTitle>
                        <CardDescription>Monthly disbursements, repayments, and defaults</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="space-y-6">
                            {/* Disbursements */}
                            <div>
                                <h4 className="font-semibold text-lg mb-3">Disbursements</h4>
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead>Month</TableHead>
                                            <TableHead className="text-right">Count</TableHead>
                                            <TableHead className="text-right">Amount</TableHead>
                                            <TableHead className="text-right">Average Size</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        {loanData.performance.disbursements.map((disbursement, index) => (
                                            <TableRow key={index}>
                                                <TableCell className="font-medium">{disbursement.month}</TableCell>
                                                <TableCell className="text-right">{disbursement.count}</TableCell>
                                                <TableCell className="text-right">{formatCurrency(disbursement.amount)}</TableCell>
                                                <TableCell className="text-right">{formatCurrency(disbursement.averageSize)}</TableCell>
                                            </TableRow>
                                        ))}
                                    </TableBody>
                                </Table>
                            </div>

                            {/* Repayments */}
                            <div>
                                <h4 className="font-semibold text-lg mb-3">Repayments</h4>
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead>Month</TableHead>
                                            <TableHead className="text-right">Amount</TableHead>
                                            <TableHead className="text-right">On Time</TableHead>
                                            <TableHead className="text-right">Late</TableHead>
                                            <TableHead className="text-right">Defaulted</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        {loanData.performance.repayments.map((repayment, index) => (
                                            <TableRow key={index}>
                                                <TableCell className="font-medium">{repayment.month}</TableCell>
                                                <TableCell className="text-right">{formatCurrency(repayment.amount)}</TableCell>
                                                <TableCell className="text-right">
                                                    <Badge variant="default">{repayment.onTime}%</Badge>
                                                </TableCell>
                                                <TableCell className="text-right">
                                                    <Badge variant="outline">{repayment.late}%</Badge>
                                                </TableCell>
                                                <TableCell className="text-right">
                                                    <Badge variant="destructive">{repayment.defaulted}%</Badge>
                                                </TableCell>
                                            </TableRow>
                                        ))}
                                    </TableBody>
                                </Table>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Portfolio Analysis */}
                <Card>
                    <CardHeader>
                        <CardTitle>Loan Portfolio Analysis</CardTitle>
                        <CardDescription>Breakdown by loan type</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Loan Type</TableHead>
                                    <TableHead className="text-right">Count</TableHead>
                                    <TableHead className="text-right">Total Amount</TableHead>
                                    <TableHead className="text-right">Outstanding</TableHead>
                                    <TableHead className="text-right">Default Rate</TableHead>
                                    <TableHead className="text-right">Average Size</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {loanData.portfolio.map((portfolio, index) => (
                                    <TableRow key={index}>
                                        <TableCell className="font-medium">{portfolio.loanType}</TableCell>
                                        <TableCell className="text-right">{portfolio.count}</TableCell>
                                        <TableCell className="text-right">{formatCurrency(portfolio.totalAmount)}</TableCell>
                                        <TableCell className="text-right">{formatCurrency(portfolio.outstanding)}</TableCell>
                                        <TableCell className="text-right">
                                            <Badge variant={portfolio.defaultRate <= 3 ? "default" : "destructive"}>
                                                {formatPercentage(portfolio.defaultRate)}
                                            </Badge>
                                        </TableCell>
                                        <TableCell className="text-right">{formatCurrency(portfolio.averageSize)}</TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                {/* Top Borrowers */}
                <Card>
                    <CardHeader>
                        <CardTitle>Top Borrowers</CardTitle>
                        <CardDescription>Borrowers with highest loan amounts</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Borrower</TableHead>
                                    <TableHead>Account Number</TableHead>
                                    <TableHead className="text-right">Loan Amount</TableHead>
                                    <TableHead className="text-right">Outstanding</TableHead>
                                    <TableHead className="text-right">Interest Rate</TableHead>
                                    <TableHead>Disbursement Date</TableHead>
                                    <TableHead>Status</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {loanData.topBorrowers.map((borrower) => (
                                    <TableRow key={borrower.id}>
                                        <TableCell className="font-medium">{borrower.name}</TableCell>
                                        <TableCell>{borrower.accountNumber}</TableCell>
                                        <TableCell className="text-right">{formatCurrency(borrower.loanAmount)}</TableCell>
                                        <TableCell className="text-right">{formatCurrency(borrower.outstanding)}</TableCell>
                                        <TableCell className="text-right">{formatPercentage(borrower.interestRate)}</TableCell>
                                        <TableCell>{new Date(borrower.disbursementDate).toLocaleDateString()}</TableCell>
                                        <TableCell>{getStatusBadge(borrower.status)}</TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                {/* Risk Analysis */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    {/* Risk Categories */}
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center">
                                <PieChart className="mr-2 h-5 w-5" />
                                Risk Categories
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-3">
                                {loanData.riskAnalysis.riskCategories.map((category, index) => (
                                    <div key={index} className="flex items-center justify-between">
                                        <div className="flex items-center space-x-2">
                                            <div className={`w-3 h-3 rounded-full ${
                                                category.category === 'Low Risk' ? 'bg-green-500' :
                                                category.category === 'Medium Risk' ? 'bg-yellow-500' : 'bg-red-500'
                                            }`}></div>
                                            <span className="text-sm font-medium">{category.category}</span>
                                        </div>
                                        <div className="text-right">
                                            <div className="text-sm font-medium">{category.count} loans</div>
                                            <div className="text-xs text-muted-foreground">{formatPercentage(category.percentage)}</div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </CardContent>
                    </Card>

                    {/* Overdue Loans */}
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center">
                                <AlertTriangle className="mr-2 h-5 w-5" />
                                Overdue Loans
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-3">
                                {loanData.riskAnalysis.overdueLoans.map((loan) => (
                                    <div key={loan.id} className="flex items-center justify-between p-3 border rounded-lg">
                                        <div>
                                            <div className="font-medium text-sm">{loan.borrowerName}</div>
                                            <div className="text-xs text-muted-foreground">
                                                Outstanding: {formatCurrency(loan.outstanding)}
                                            </div>
                                        </div>
                                        <div className="text-right">
                                            {getOverdueBadge(loan.daysOverdue)}
                                            <div className="text-xs text-muted-foreground mt-1">
                                                Last: {new Date(loan.lastPayment).toLocaleDateString()}
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}

