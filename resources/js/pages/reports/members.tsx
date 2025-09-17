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
    Users, 
    UserPlus,
    UserMinus,
    TrendingUp,
    BarChart3,
    PieChart,
    FileText,
    RefreshCw,
    MapPin,
    Clock
} from 'lucide-react';

interface MemberData {
    summary: {
        totalMembers: number;
        activeMembers: number;
        newMembersThisMonth: number;
        inactiveMembers: number;
        averageAge: number;
        totalDeposits: number;
        averageDeposit: number;
        memberGrowthRate: number;
    };
    demographics: {
        ageGroups: Array<{
            range: string;
            count: number;
            percentage: number;
        }>;
        genderDistribution: Array<{
            gender: string;
            count: number;
            percentage: number;
        }>;
        locationDistribution: Array<{
            location: string;
            count: number;
            percentage: number;
        }>;
    };
    activity: Array<{
        month: string;
        newMembers: number;
        activeMembers: number;
        deposits: number;
        withdrawals: number;
        netGrowth: number;
    }>;
    topMembers: Array<{
        id: number;
        name: string;
        accountNumber: string;
        totalDeposits: number;
        accountBalance: number;
        joinDate: string;
        status: string;
    }>;
    recentActivity: Array<{
        id: number;
        memberName: string;
        action: string;
        amount: number;
        date: string;
        type: 'deposit' | 'withdrawal' | 'loan' | 'registration';
    }>;
}

export default function MemberReports() {
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
            title: 'Member Reports',
            href: '#',
        },
    ];

    const [dateRange, setDateRange] = useState<{ from: Date | undefined; to: Date | undefined }>({
        from: new Date(new Date().getFullYear(), new Date().getMonth(), 1),
        to: new Date()
    });
    const [reportType, setReportType] = useState('summary');
    const [branchFilter, setBranchFilter] = useState('all');
    const [isLoading, setIsLoading] = useState(false);

    // Mock data - in real app, this would come from the backend
    const memberData: MemberData = {
        summary: {
            totalMembers: 1250,
            activeMembers: 1180,
            newMembersThisMonth: 45,
            inactiveMembers: 70,
            averageAge: 34.5,
            totalDeposits: 45000000,
            averageDeposit: 36000,
            memberGrowthRate: 3.6
        },
        demographics: {
            ageGroups: [
                { range: '18-25', count: 180, percentage: 14.4 },
                { range: '26-35', count: 450, percentage: 36.0 },
                { range: '36-45', count: 380, percentage: 30.4 },
                { range: '46-55', count: 180, percentage: 14.4 },
                { range: '56+', count: 60, percentage: 4.8 }
            ],
            genderDistribution: [
                { gender: 'Male', count: 650, percentage: 52.0 },
                { gender: 'Female', count: 600, percentage: 48.0 }
            ],
            locationDistribution: [
                { location: 'Nairobi', count: 450, percentage: 36.0 },
                { location: 'Mombasa', count: 200, percentage: 16.0 },
                { location: 'Kisumu', count: 150, percentage: 12.0 },
                { location: 'Nakuru', count: 120, percentage: 9.6 },
                { location: 'Eldoret', count: 100, percentage: 8.0 },
                { location: 'Other', count: 230, percentage: 18.4 }
            ]
        },
        activity: [
            { month: 'Jan', newMembers: 35, activeMembers: 1150, deposits: 1200000, withdrawals: 800000, netGrowth: 400000 },
            { month: 'Feb', newMembers: 42, activeMembers: 1165, deposits: 1350000, withdrawals: 900000, netGrowth: 450000 },
            { month: 'Mar', newMembers: 38, activeMembers: 1178, deposits: 1280000, withdrawals: 750000, netGrowth: 530000 },
            { month: 'Apr', newMembers: 45, activeMembers: 1180, deposits: 1420000, withdrawals: 820000, netGrowth: 600000 },
            { month: 'May', newMembers: 52, activeMembers: 1185, deposits: 1580000, withdrawals: 950000, netGrowth: 630000 },
            { month: 'Jun', newMembers: 48, activeMembers: 1180, deposits: 1450000, withdrawals: 880000, netGrowth: 570000 }
        ],
        topMembers: [
            { id: 1, name: 'John Mwangi', accountNumber: 'ACC001', totalDeposits: 2500000, accountBalance: 1800000, joinDate: '2020-01-15', status: 'active' },
            { id: 2, name: 'Mary Wanjiku', accountNumber: 'ACC002', totalDeposits: 2200000, accountBalance: 1650000, joinDate: '2019-08-22', status: 'active' },
            { id: 3, name: 'Peter Kimani', accountNumber: 'ACC003', totalDeposits: 2000000, accountBalance: 1500000, joinDate: '2021-03-10', status: 'active' },
            { id: 4, name: 'Grace Akinyi', accountNumber: 'ACC004', totalDeposits: 1800000, accountBalance: 1350000, joinDate: '2020-11-05', status: 'active' },
            { id: 5, name: 'David Otieno', accountNumber: 'ACC005', totalDeposits: 1600000, accountBalance: 1200000, joinDate: '2021-07-18', status: 'active' }
        ],
        recentActivity: [
            { id: 1, memberName: 'John Mwangi', action: 'Deposit', amount: 50000, date: '2024-01-15', type: 'deposit' },
            { id: 2, memberName: 'Mary Wanjiku', action: 'Loan Application', amount: 200000, date: '2024-01-14', type: 'loan' },
            { id: 3, memberName: 'Peter Kimani', action: 'Withdrawal', amount: 25000, date: '2024-01-14', type: 'withdrawal' },
            { id: 4, memberName: 'Grace Akinyi', action: 'New Member Registration', amount: 0, date: '2024-01-13', type: 'registration' },
            { id: 5, memberName: 'David Otieno', action: 'Deposit', amount: 75000, date: '2024-01-13', type: 'deposit' }
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

    const getStatusBadge = (status: string) => {
        switch (status) {
            case 'active':
                return <Badge variant="default">Active</Badge>;
            case 'inactive':
                return <Badge variant="secondary">Inactive</Badge>;
            case 'suspended':
                return <Badge variant="destructive">Suspended</Badge>;
            default:
                return <Badge variant="outline">{status}</Badge>;
        }
    };

    const getActivityTypeBadge = (type: string) => {
        switch (type) {
            case 'deposit':
                return <Badge variant="default">Deposit</Badge>;
            case 'withdrawal':
                return <Badge variant="destructive">Withdrawal</Badge>;
            case 'loan':
                return <Badge variant="secondary">Loan</Badge>;
            case 'registration':
                return <Badge variant="outline">Registration</Badge>;
            default:
                return <Badge variant="outline">{type}</Badge>;
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Member Reports" />
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
                            <h1 className="text-3xl font-bold tracking-tight">Member Reports</h1>
                            <p className="text-muted-foreground">
                                Member statistics, demographics, and activity analysis
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
                                        <SelectItem value="demographics">Demographics</SelectItem>
                                        <SelectItem value="activity">Activity</SelectItem>
                                        <SelectItem value="top-members">Top Members</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div>
                                <label className="text-sm font-medium mb-2 block">Branch</label>
                                <Select value={branchFilter} onValueChange={setBranchFilter}>
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select branch" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="all">All Branches</SelectItem>
                                        <SelectItem value="nairobi">Nairobi</SelectItem>
                                        <SelectItem value="mombasa">Mombasa</SelectItem>
                                        <SelectItem value="kisumu">Kisumu</SelectItem>
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
                            <CardTitle className="text-sm font-medium">Total Members</CardTitle>
                            <Users className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{memberData.summary.totalMembers.toLocaleString()}</div>
                            <div className="flex items-center text-xs text-muted-foreground mt-1">
                                <TrendingUp className="h-3 w-3 text-green-600 mr-1" />
                                <span>+{memberData.summary.memberGrowthRate}% growth rate</span>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Active Members</CardTitle>
                            <UserPlus className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{memberData.summary.activeMembers.toLocaleString()}</div>
                            <div className="flex items-center text-xs text-muted-foreground mt-1">
                                <span>{formatPercentage((memberData.summary.activeMembers / memberData.summary.totalMembers) * 100)} of total</span>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">New This Month</CardTitle>
                            <UserPlus className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{memberData.summary.newMembersThisMonth}</div>
                            <div className="flex items-center text-xs text-muted-foreground mt-1">
                                <TrendingUp className="h-3 w-3 text-green-600 mr-1" />
                                <span>+12 from last month</span>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Deposits</CardTitle>
                            <BarChart3 className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{formatCurrency(memberData.summary.totalDeposits)}</div>
                            <div className="flex items-center text-xs text-muted-foreground mt-1">
                                <span>Avg: {formatCurrency(memberData.summary.averageDeposit)} per member</span>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Demographics */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    {/* Age Groups */}
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center">
                                <PieChart className="mr-2 h-5 w-5" />
                                Age Distribution
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-3">
                                {memberData.demographics.ageGroups.map((group, index) => (
                                    <div key={index} className="flex items-center justify-between">
                                        <div className="flex items-center space-x-2">
                                            <div className="w-3 h-3 bg-blue-500 rounded-full"></div>
                                            <span className="text-sm font-medium">{group.range}</span>
                                        </div>
                                        <div className="text-right">
                                            <div className="text-sm font-medium">{group.count}</div>
                                            <div className="text-xs text-muted-foreground">{formatPercentage(group.percentage)}</div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </CardContent>
                    </Card>

                    {/* Gender Distribution */}
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center">
                                <Users className="mr-2 h-5 w-5" />
                                Gender Distribution
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-3">
                                {memberData.demographics.genderDistribution.map((gender, index) => (
                                    <div key={index} className="flex items-center justify-between">
                                        <div className="flex items-center space-x-2">
                                            <div className={`w-3 h-3 rounded-full ${gender.gender === 'Male' ? 'bg-blue-500' : 'bg-pink-500'}`}></div>
                                            <span className="text-sm font-medium">{gender.gender}</span>
                                        </div>
                                        <div className="text-right">
                                            <div className="text-sm font-medium">{gender.count}</div>
                                            <div className="text-xs text-muted-foreground">{formatPercentage(gender.percentage)}</div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </CardContent>
                    </Card>

                    {/* Location Distribution */}
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center">
                                <MapPin className="mr-2 h-5 w-5" />
                                Location Distribution
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-3">
                                {memberData.demographics.locationDistribution.map((location, index) => (
                                    <div key={index} className="flex items-center justify-between">
                                        <div className="flex items-center space-x-2">
                                            <div className="w-3 h-3 bg-green-500 rounded-full"></div>
                                            <span className="text-sm font-medium">{location.location}</span>
                                        </div>
                                        <div className="text-right">
                                            <div className="text-sm font-medium">{location.count}</div>
                                            <div className="text-xs text-muted-foreground">{formatPercentage(location.percentage)}</div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Member Activity */}
                <Card>
                    <CardHeader>
                        <CardTitle>Member Activity Trends</CardTitle>
                        <CardDescription>Monthly member activity and growth</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Month</TableHead>
                                    <TableHead className="text-right">New Members</TableHead>
                                    <TableHead className="text-right">Active Members</TableHead>
                                    <TableHead className="text-right">Deposits</TableHead>
                                    <TableHead className="text-right">Withdrawals</TableHead>
                                    <TableHead className="text-right">Net Growth</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {memberData.activity.map((activity, index) => (
                                    <TableRow key={index}>
                                        <TableCell className="font-medium">{activity.month}</TableCell>
                                        <TableCell className="text-right">{activity.newMembers}</TableCell>
                                        <TableCell className="text-right">{activity.activeMembers}</TableCell>
                                        <TableCell className="text-right">{formatCurrency(activity.deposits)}</TableCell>
                                        <TableCell className="text-right">{formatCurrency(activity.withdrawals)}</TableCell>
                                        <TableCell className="text-right">
                                            <Badge variant={activity.netGrowth >= 0 ? "default" : "destructive"}>
                                                {formatCurrency(activity.netGrowth)}
                                            </Badge>
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                {/* Top Members */}
                <Card>
                    <CardHeader>
                        <CardTitle>Top Members by Deposits</CardTitle>
                        <CardDescription>Members with highest total deposits</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Member</TableHead>
                                    <TableHead>Account Number</TableHead>
                                    <TableHead className="text-right">Total Deposits</TableHead>
                                    <TableHead className="text-right">Current Balance</TableHead>
                                    <TableHead>Join Date</TableHead>
                                    <TableHead>Status</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {memberData.topMembers.map((member) => (
                                    <TableRow key={member.id}>
                                        <TableCell className="font-medium">{member.name}</TableCell>
                                        <TableCell>{member.accountNumber}</TableCell>
                                        <TableCell className="text-right">{formatCurrency(member.totalDeposits)}</TableCell>
                                        <TableCell className="text-right">{formatCurrency(member.accountBalance)}</TableCell>
                                        <TableCell>{new Date(member.joinDate).toLocaleDateString()}</TableCell>
                                        <TableCell>{getStatusBadge(member.status)}</TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                {/* Recent Activity */}
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center">
                            <Clock className="mr-2 h-5 w-5" />
                            Recent Member Activity
                        </CardTitle>
                        <CardDescription>Latest member transactions and activities</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Member</TableHead>
                                    <TableHead>Action</TableHead>
                                    <TableHead className="text-right">Amount</TableHead>
                                    <TableHead>Date</TableHead>
                                    <TableHead>Type</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {memberData.recentActivity.map((activity) => (
                                    <TableRow key={activity.id}>
                                        <TableCell className="font-medium">{activity.memberName}</TableCell>
                                        <TableCell>{activity.action}</TableCell>
                                        <TableCell className="text-right">
                                            {activity.amount > 0 ? formatCurrency(activity.amount) : '-'}
                                        </TableCell>
                                        <TableCell>{new Date(activity.date).toLocaleDateString()}</TableCell>
                                        <TableCell>{getActivityTypeBadge(activity.type)}</TableCell>
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

