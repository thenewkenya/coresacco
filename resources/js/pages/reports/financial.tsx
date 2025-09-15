import { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
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
    TrendingUp, 
    TrendingDown,
    DollarSign,
    BarChart3,
    PieChart,
    FileText,
    RefreshCw
} from 'lucide-react';

interface FinancialData {
    summary: {
        totalAssets: number;
        totalLiabilities: number;
        netWorth: number;
        monthlyIncome: number;
        monthlyExpenses: number;
        netIncome: number;
        memberDeposits: number;
        loanDisbursements: number;
        interestEarned: number;
        operatingExpenses: number;
    };
    incomeStatement: {
        period: string;
        revenue: {
            memberDeposits: number;
            interestEarned: number;
            fees: number;
            other: number;
        };
        expenses: {
            operatingExpenses: number;
            loanLosses: number;
            administrative: number;
            other: number;
        };
        netIncome: number;
    };
    balanceSheet: {
        assets: {
            cash: number;
            investments: number;
            loansOutstanding: number;
            fixedAssets: number;
            other: number;
        };
        liabilities: {
            memberDeposits: number;
            borrowings: number;
            accruedExpenses: number;
            other: number;
        };
        equity: {
            retainedEarnings: number;
            memberEquity: number;
            other: number;
        };
    };
    cashFlow: Array<{
        month: string;
        operating: number;
        investing: number;
        financing: number;
        netCashFlow: number;
    }>;
    trends: Array<{
        month: string;
        assets: number;
        liabilities: number;
        netWorth: number;
    }>;
}

export default function FinancialReports() {
    const [dateRange, setDateRange] = useState<{ from: Date | undefined; to: Date | undefined }>({
        from: new Date(new Date().getFullYear(), new Date().getMonth(), 1),
        to: new Date()
    });
    const [reportType, setReportType] = useState('summary');
    const [isLoading, setIsLoading] = useState(false);

    // Mock data - in real app, this would come from the backend
    const financialData: FinancialData = {
        summary: {
            totalAssets: 25000000,
            totalLiabilities: 18000000,
            netWorth: 7000000,
            monthlyIncome: 1200000,
            monthlyExpenses: 800000,
            netIncome: 400000,
            memberDeposits: 1500000,
            loanDisbursements: 2000000,
            interestEarned: 180000,
            operatingExpenses: 200000
        },
        incomeStatement: {
            period: 'January 2024',
            revenue: {
                memberDeposits: 1500000,
                interestEarned: 180000,
                fees: 45000,
                other: 15000
            },
            expenses: {
                operatingExpenses: 200000,
                loanLosses: 25000,
                administrative: 120000,
                other: 30000
            },
            netIncome: 400000
        },
        balanceSheet: {
            assets: {
                cash: 5000000,
                investments: 8000000,
                loansOutstanding: 12000000,
                fixedAssets: 2000000,
                other: 1000000
            },
            liabilities: {
                memberDeposits: 15000000,
                borrowings: 2000000,
                accruedExpenses: 500000,
                other: 500000
            },
            equity: {
                retainedEarnings: 4000000,
                memberEquity: 2500000,
                other: 500000
            }
        },
        cashFlow: [
            { month: 'Jan', operating: 400000, investing: -200000, financing: 100000, netCashFlow: 300000 },
            { month: 'Feb', operating: 450000, investing: -150000, financing: 150000, netCashFlow: 450000 },
            { month: 'Mar', operating: 380000, investing: -300000, financing: 200000, netCashFlow: 280000 },
            { month: 'Apr', operating: 420000, investing: -100000, financing: 120000, netCashFlow: 440000 },
            { month: 'May', operating: 480000, investing: -250000, financing: 180000, netCashFlow: 410000 },
            { month: 'Jun', operating: 520000, investing: -200000, financing: 220000, netCashFlow: 540000 }
        ],
        trends: [
            { month: 'Jan', assets: 22000000, liabilities: 16000000, netWorth: 6000000 },
            { month: 'Feb', assets: 22500000, liabilities: 16200000, netWorth: 6300000 },
            { month: 'Mar', assets: 23000000, liabilities: 16500000, netWorth: 6500000 },
            { month: 'Apr', assets: 23500000, liabilities: 16800000, netWorth: 6700000 },
            { month: 'May', assets: 24200000, liabilities: 17200000, netWorth: 7000000 },
            { month: 'Jun', assets: 25000000, liabilities: 18000000, netWorth: 7000000 }
        ]
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

    const getTrendIcon = (value: number) => {
        return value >= 0 ? (
            <TrendingUp className="h-4 w-4 text-green-600" />
        ) : (
            <TrendingDown className="h-4 w-4 text-red-600" />
        );
    };

    return (
        <AppLayout>
            <Head title="Financial Reports" />
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
                            <h1 className="text-3xl font-bold tracking-tight">Financial Reports</h1>
                            <p className="text-muted-foreground">
                                Comprehensive financial analysis and reporting
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
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                                        <SelectItem value="income">Income Statement</SelectItem>
                                        <SelectItem value="balance">Balance Sheet</SelectItem>
                                        <SelectItem value="cashflow">Cash Flow</SelectItem>
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
                            <CardTitle className="text-sm font-medium">Total Assets</CardTitle>
                            <DollarSign className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{formatCurrency(financialData.summary.totalAssets)}</div>
                            <div className="flex items-center text-xs text-muted-foreground mt-1">
                                {getTrendIcon(5.2)}
                                <span className="ml-1">+5.2% from last month</span>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Net Worth</CardTitle>
                            <TrendingUp className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{formatCurrency(financialData.summary.netWorth)}</div>
                            <div className="flex items-center text-xs text-muted-foreground mt-1">
                                {getTrendIcon(3.8)}
                                <span className="ml-1">+3.8% from last month</span>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Monthly Income</CardTitle>
                            <BarChart3 className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{formatCurrency(financialData.summary.monthlyIncome)}</div>
                            <div className="flex items-center text-xs text-muted-foreground mt-1">
                                {getTrendIcon(2.1)}
                                <span className="ml-1">+2.1% from last month</span>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Net Income</CardTitle>
                            <PieChart className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{formatCurrency(financialData.summary.netIncome)}</div>
                            <div className="flex items-center text-xs text-muted-foreground mt-1">
                                {getTrendIcon(1.5)}
                                <span className="ml-1">+1.5% from last month</span>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Income Statement */}
                <Card>
                    <CardHeader>
                        <CardTitle>Income Statement - {financialData.incomeStatement.period}</CardTitle>
                        <CardDescription>Revenue and expenses breakdown</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="space-y-6">
                            {/* Revenue Section */}
                            <div>
                                <h4 className="font-semibold text-lg mb-3">Revenue</h4>
                                <Table>
                                    <TableBody>
                                        <TableRow>
                                            <TableCell className="font-medium">Member Deposits</TableCell>
                                            <TableCell className="text-right">{formatCurrency(financialData.incomeStatement.revenue.memberDeposits)}</TableCell>
                                        </TableRow>
                                        <TableRow>
                                            <TableCell className="font-medium">Interest Earned</TableCell>
                                            <TableCell className="text-right">{formatCurrency(financialData.incomeStatement.revenue.interestEarned)}</TableCell>
                                        </TableRow>
                                        <TableRow>
                                            <TableCell className="font-medium">Fees</TableCell>
                                            <TableCell className="text-right">{formatCurrency(financialData.incomeStatement.revenue.fees)}</TableCell>
                                        </TableRow>
                                        <TableRow>
                                            <TableCell className="font-medium">Other Revenue</TableCell>
                                            <TableCell className="text-right">{formatCurrency(financialData.incomeStatement.revenue.other)}</TableCell>
                                        </TableRow>
                                        <TableRow className="border-t-2">
                                            <TableCell className="font-bold">Total Revenue</TableCell>
                                            <TableCell className="text-right font-bold">
                                                {formatCurrency(
                                                    financialData.incomeStatement.revenue.memberDeposits +
                                                    financialData.incomeStatement.revenue.interestEarned +
                                                    financialData.incomeStatement.revenue.fees +
                                                    financialData.incomeStatement.revenue.other
                                                )}
                                            </TableCell>
                                        </TableRow>
                                    </TableBody>
                                </Table>
                            </div>

                            {/* Expenses Section */}
                            <div>
                                <h4 className="font-semibold text-lg mb-3">Expenses</h4>
                                <Table>
                                    <TableBody>
                                        <TableRow>
                                            <TableCell className="font-medium">Operating Expenses</TableCell>
                                            <TableCell className="text-right">{formatCurrency(financialData.incomeStatement.expenses.operatingExpenses)}</TableCell>
                                        </TableRow>
                                        <TableRow>
                                            <TableCell className="font-medium">Loan Losses</TableCell>
                                            <TableCell className="text-right">{formatCurrency(financialData.incomeStatement.expenses.loanLosses)}</TableCell>
                                        </TableRow>
                                        <TableRow>
                                            <TableCell className="font-medium">Administrative</TableCell>
                                            <TableCell className="text-right">{formatCurrency(financialData.incomeStatement.expenses.administrative)}</TableCell>
                                        </TableRow>
                                        <TableRow>
                                            <TableCell className="font-medium">Other Expenses</TableCell>
                                            <TableCell className="text-right">{formatCurrency(financialData.incomeStatement.expenses.other)}</TableCell>
                                        </TableRow>
                                        <TableRow className="border-t-2">
                                            <TableCell className="font-bold">Total Expenses</TableCell>
                                            <TableCell className="text-right font-bold">
                                                {formatCurrency(
                                                    financialData.incomeStatement.expenses.operatingExpenses +
                                                    financialData.incomeStatement.expenses.loanLosses +
                                                    financialData.incomeStatement.expenses.administrative +
                                                    financialData.incomeStatement.expenses.other
                                                )}
                                            </TableCell>
                                        </TableRow>
                                    </TableBody>
                                </Table>
                            </div>

                            {/* Net Income */}
                            <div className="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg border border-green-200 dark:border-green-800">
                                <div className="flex justify-between items-center">
                                    <span className="font-bold text-lg">Net Income</span>
                                    <span className="font-bold text-2xl text-green-600">
                                        {formatCurrency(financialData.incomeStatement.netIncome)}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Balance Sheet */}
                <Card>
                    <CardHeader>
                        <CardTitle>Balance Sheet - {financialData.incomeStatement.period}</CardTitle>
                        <CardDescription>Assets, liabilities, and equity</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            {/* Assets */}
                            <div>
                                <h4 className="font-semibold text-lg mb-3">Assets</h4>
                                <Table>
                                    <TableBody>
                                        <TableRow>
                                            <TableCell className="font-medium">Cash</TableCell>
                                            <TableCell className="text-right">{formatCurrency(financialData.balanceSheet.assets.cash)}</TableCell>
                                        </TableRow>
                                        <TableRow>
                                            <TableCell className="font-medium">Investments</TableCell>
                                            <TableCell className="text-right">{formatCurrency(financialData.balanceSheet.assets.investments)}</TableCell>
                                        </TableRow>
                                        <TableRow>
                                            <TableCell className="font-medium">Loans Outstanding</TableCell>
                                            <TableCell className="text-right">{formatCurrency(financialData.balanceSheet.assets.loansOutstanding)}</TableCell>
                                        </TableRow>
                                        <TableRow>
                                            <TableCell className="font-medium">Fixed Assets</TableCell>
                                            <TableCell className="text-right">{formatCurrency(financialData.balanceSheet.assets.fixedAssets)}</TableCell>
                                        </TableRow>
                                        <TableRow>
                                            <TableCell className="font-medium">Other Assets</TableCell>
                                            <TableCell className="text-right">{formatCurrency(financialData.balanceSheet.assets.other)}</TableCell>
                                        </TableRow>
                                        <TableRow className="border-t-2">
                                            <TableCell className="font-bold">Total Assets</TableCell>
                                            <TableCell className="text-right font-bold">
                                                {formatCurrency(financialData.summary.totalAssets)}
                                            </TableCell>
                                        </TableRow>
                                    </TableBody>
                                </Table>
                            </div>

                            {/* Liabilities */}
                            <div>
                                <h4 className="font-semibold text-lg mb-3">Liabilities</h4>
                                <Table>
                                    <TableBody>
                                        <TableRow>
                                            <TableCell className="font-medium">Member Deposits</TableCell>
                                            <TableCell className="text-right">{formatCurrency(financialData.balanceSheet.liabilities.memberDeposits)}</TableCell>
                                        </TableRow>
                                        <TableRow>
                                            <TableCell className="font-medium">Borrowings</TableCell>
                                            <TableCell className="text-right">{formatCurrency(financialData.balanceSheet.liabilities.borrowings)}</TableCell>
                                        </TableRow>
                                        <TableRow>
                                            <TableCell className="font-medium">Accrued Expenses</TableCell>
                                            <TableCell className="text-right">{formatCurrency(financialData.balanceSheet.liabilities.accruedExpenses)}</TableCell>
                                        </TableRow>
                                        <TableRow>
                                            <TableCell className="font-medium">Other Liabilities</TableCell>
                                            <TableCell className="text-right">{formatCurrency(financialData.balanceSheet.liabilities.other)}</TableCell>
                                        </TableRow>
                                        <TableRow className="border-t-2">
                                            <TableCell className="font-bold">Total Liabilities</TableCell>
                                            <TableCell className="text-right font-bold">
                                                {formatCurrency(financialData.summary.totalLiabilities)}
                                            </TableCell>
                                        </TableRow>
                                    </TableBody>
                                </Table>
                            </div>

                            {/* Equity */}
                            <div>
                                <h4 className="font-semibold text-lg mb-3">Equity</h4>
                                <Table>
                                    <TableBody>
                                        <TableRow>
                                            <TableCell className="font-medium">Retained Earnings</TableCell>
                                            <TableCell className="text-right">{formatCurrency(financialData.balanceSheet.equity.retainedEarnings)}</TableCell>
                                        </TableRow>
                                        <TableRow>
                                            <TableCell className="font-medium">Member Equity</TableCell>
                                            <TableCell className="text-right">{formatCurrency(financialData.balanceSheet.equity.memberEquity)}</TableCell>
                                        </TableRow>
                                        <TableRow>
                                            <TableCell className="font-medium">Other Equity</TableCell>
                                            <TableCell className="text-right">{formatCurrency(financialData.balanceSheet.equity.other)}</TableCell>
                                        </TableRow>
                                        <TableRow className="border-t-2">
                                            <TableCell className="font-bold">Total Equity</TableCell>
                                            <TableCell className="text-right font-bold">
                                                {formatCurrency(financialData.summary.netWorth)}
                                            </TableCell>
                                        </TableRow>
                                    </TableBody>
                                </Table>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Cash Flow Statement */}
                <Card>
                    <CardHeader>
                        <CardTitle>Cash Flow Statement</CardTitle>
                        <CardDescription>Monthly cash flow analysis</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Month</TableHead>
                                    <TableHead className="text-right">Operating</TableHead>
                                    <TableHead className="text-right">Investing</TableHead>
                                    <TableHead className="text-right">Financing</TableHead>
                                    <TableHead className="text-right">Net Cash Flow</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {financialData.cashFlow.map((flow, index) => (
                                    <TableRow key={index}>
                                        <TableCell className="font-medium">{flow.month}</TableCell>
                                        <TableCell className="text-right">{formatCurrency(flow.operating)}</TableCell>
                                        <TableCell className="text-right">{formatCurrency(flow.investing)}</TableCell>
                                        <TableCell className="text-right">{formatCurrency(flow.financing)}</TableCell>
                                        <TableCell className="text-right font-medium">
                                            <Badge variant={flow.netCashFlow >= 0 ? "default" : "destructive"}>
                                                {formatCurrency(flow.netCashFlow)}
                                            </Badge>
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}

