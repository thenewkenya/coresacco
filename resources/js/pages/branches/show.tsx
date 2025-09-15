import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Label } from '@/components/ui/label';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { 
    ArrowLeft, 
    Edit, 
    Building2, 
    MapPin, 
    Phone, 
    Mail, 
    User, 
    Calendar,
    Clock,
    Users,
    DollarSign,
    CreditCard,
    TrendingUp,
    Activity
} from 'lucide-react';

interface Branch {
    id: number;
    name: string;
    code: string;
    address: string;
    city: string;
    phone: string;
    email: string;
    status: 'active' | 'inactive' | 'under_maintenance';
    opening_date: string;
    working_hours: Record<string, { open: string; close: string }>;
    coordinates?: {
        latitude: number;
        longitude: number;
    };
    manager?: {
        id: number;
        name: string;
        email: string;
        phone: string;
    };
    analytics: {
        totalMembers: number;
        totalStaff: number;
        totalDeposits: number;
        activeLoans: number;
        monthlyTransactions: number;
        monthlyGrowth: number;
    };
    recentTransactions: Array<{
        id: number;
        memberName: string;
        type: string;
        amount: number;
        date: string;
        status: string;
    }>;
    recentMembers: Array<{
        id: number;
        name: string;
        email: string;
        joinDate: string;
        accountBalance: number;
    }>;
    staff: Array<{
        id: number;
        name: string;
        email: string;
        role: string;
        joinDate: string;
    }>;
}

export default function BranchShow() {
    // Mock data - in real app, this would come from props
    const branch: Branch = {
        id: 1,
        name: 'Nairobi Main Branch',
        code: 'BR0001',
        address: 'Kenyatta Avenue, Nairobi CBD',
        city: 'Nairobi',
        phone: '+254 20 1234567',
        email: 'nairobi@sacco.co.ke',
        status: 'active',
        opening_date: '2020-01-15',
        working_hours: {
            monday: { open: '08:00', close: '17:00' },
            tuesday: { open: '08:00', close: '17:00' },
            wednesday: { open: '08:00', close: '17:00' },
            thursday: { open: '08:00', close: '17:00' },
            friday: { open: '08:00', close: '17:00' },
            saturday: { open: '09:00', close: '13:00' },
            sunday: { open: '', close: '' }
        },
        coordinates: {
            latitude: -1.2921,
            longitude: 36.8219
        },
        manager: {
            id: 1,
            name: 'John Mwangi',
            email: 'john.mwangi@sacco.co.ke',
            phone: '+254 700 123456'
        },
        analytics: {
            totalMembers: 450,
            totalStaff: 12,
            totalDeposits: 25000000,
            activeLoans: 120,
            monthlyTransactions: 850,
            monthlyGrowth: 5.2
        },
        recentTransactions: [
            { id: 1, memberName: 'Alice Wanjala', type: 'Deposit', amount: 50000, date: '2024-01-15', status: 'completed' },
            { id: 2, memberName: 'Robert Mwangi', type: 'Withdrawal', amount: 25000, date: '2024-01-15', status: 'completed' },
            { id: 3, memberName: 'Susan Kimani', type: 'Loan Payment', amount: 15000, date: '2024-01-14', status: 'completed' },
            { id: 4, memberName: 'Michael Otieno', type: 'Deposit', amount: 75000, date: '2024-01-14', status: 'completed' },
            { id: 5, memberName: 'Jane Akinyi', type: 'Transfer', amount: 30000, date: '2024-01-13', status: 'completed' }
        ],
        recentMembers: [
            { id: 1, name: 'Grace Wanjiku', email: 'grace.wanjiku@email.com', joinDate: '2024-01-10', accountBalance: 25000 },
            { id: 2, name: 'Peter Kimani', email: 'peter.kimani@email.com', joinDate: '2024-01-08', accountBalance: 50000 },
            { id: 3, name: 'Mary Akinyi', email: 'mary.akinyi@email.com', joinDate: '2024-01-05', accountBalance: 30000 },
            { id: 4, name: 'David Otieno', email: 'david.otieno@email.com', joinDate: '2024-01-03', accountBalance: 40000 },
            { id: 5, name: 'Sarah Mwangi', email: 'sarah.mwangi@email.com', joinDate: '2024-01-01', accountBalance: 60000 }
        ],
        staff: [
            { id: 1, name: 'John Mwangi', email: 'john.mwangi@sacco.co.ke', role: 'Manager', joinDate: '2020-01-15' },
            { id: 2, name: 'Mary Wanjiku', email: 'mary.wanjiku@sacco.co.ke', role: 'Teller', joinDate: '2020-03-20' },
            { id: 3, name: 'Peter Kimani', email: 'peter.kimani@sacco.co.ke', role: 'Loan Officer', joinDate: '2020-06-10' },
            { id: 4, name: 'Grace Akinyi', email: 'grace.akinyi@sacco.co.ke', role: 'Teller', joinDate: '2021-02-15' },
            { id: 5, name: 'David Otieno', email: 'david.otieno@sacco.co.ke', role: 'Customer Service', joinDate: '2021-08-20' }
        ]
    };

    const formatCurrency = (amount: number) => {
        return new Intl.NumberFormat('en-KE', {
            style: 'currency',
            currency: 'KES',
        }).format(amount);
    };

    const formatDate = (dateString: string) => {
        return new Date(dateString).toLocaleDateString();
    };

    const getStatusBadge = (status: string) => {
        switch (status) {
            case 'active':
                return <Badge variant="default">Active</Badge>;
            case 'inactive':
                return <Badge variant="secondary">Inactive</Badge>;
            case 'under_maintenance':
                return <Badge variant="destructive">Under Maintenance</Badge>;
            default:
                return <Badge variant="outline">{status}</Badge>;
        }
    };

    const getTransactionTypeBadge = (type: string) => {
        switch (type) {
            case 'Deposit':
                return <Badge variant="default">Deposit</Badge>;
            case 'Withdrawal':
                return <Badge variant="destructive">Withdrawal</Badge>;
            case 'Loan Payment':
                return <Badge variant="secondary">Loan Payment</Badge>;
            case 'Transfer':
                return <Badge variant="outline">Transfer</Badge>;
            default:
                return <Badge variant="outline">{type}</Badge>;
        }
    };

    const days = [
        { key: 'monday', label: 'Monday' },
        { key: 'tuesday', label: 'Tuesday' },
        { key: 'wednesday', label: 'Wednesday' },
        { key: 'thursday', label: 'Thursday' },
        { key: 'friday', label: 'Friday' },
        { key: 'saturday', label: 'Saturday' },
        { key: 'sunday', label: 'Sunday' }
    ];

    return (
        <AppLayout>
            <Head title={`${branch.name} - Branch Details`} />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div className="flex items-center space-x-4">
                        <Link href="/branches">
                            <Button variant="outline" size="sm">
                                <ArrowLeft className="mr-2 h-4 w-4" />
                                Back to Branches
                            </Button>
                        </Link>
                        <div>
                            <h1 className="text-3xl font-bold tracking-tight">{branch.name}</h1>
                            <p className="text-muted-foreground">
                                Branch Code: {branch.code} • {branch.city}
                            </p>
                        </div>
                    </div>
                    <div className="flex items-center space-x-2">
                        <Link href={`/branches/${branch.id}/edit`}>
                            <Button variant="outline">
                                <Edit className="mr-2 h-4 w-4" />
                                Edit Branch
                            </Button>
                        </Link>
                    </div>
                </div>

                {/* Status and Basic Info */}
                <div className="flex items-center space-x-4">
                    {getStatusBadge(branch.status)}
                    <div className="flex items-center space-x-2 text-sm text-muted-foreground">
                        <Calendar className="h-4 w-4" />
                        <span>Opened {formatDate(branch.opening_date)}</span>
                    </div>
                    <div className="flex items-center space-x-2 text-sm text-muted-foreground">
                        <MapPin className="h-4 w-4" />
                        <span>{branch.address}</span>
                    </div>
                </div>

                {/* Analytics Cards */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Members</CardTitle>
                            <Users className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{branch.analytics.totalMembers.toLocaleString()}</div>
                            <div className="flex items-center text-xs text-muted-foreground mt-1">
                                <TrendingUp className="h-3 w-3 text-green-600 mr-1" />
                                <span>+{branch.analytics.monthlyGrowth}% this month</span>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Staff</CardTitle>
                            <User className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{branch.analytics.totalStaff}</div>
                            <p className="text-xs text-muted-foreground">
                                Active employees
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Deposits</CardTitle>
                            <DollarSign className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{formatCurrency(branch.analytics.totalDeposits)}</div>
                            <p className="text-xs text-muted-foreground">
                                Member deposits
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Active Loans</CardTitle>
                            <CreditCard className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{branch.analytics.activeLoans}</div>
                            <p className="text-xs text-muted-foreground">
                                Outstanding loans
                            </p>
                        </CardContent>
                    </Card>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {/* Branch Information */}
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center">
                                <Building2 className="mr-2 h-5 w-5" />
                                Branch Information
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <Label className="text-sm font-medium text-muted-foreground">Phone</Label>
                                    <div className="flex items-center space-x-2 mt-1">
                                        <Phone className="h-4 w-4 text-muted-foreground" />
                                        <span>{branch.phone}</span>
                                    </div>
                                </div>
                                <div>
                                    <Label className="text-sm font-medium text-muted-foreground">Email</Label>
                                    <div className="flex items-center space-x-2 mt-1">
                                        <Mail className="h-4 w-4 text-muted-foreground" />
                                        <span>{branch.email}</span>
                                    </div>
                                </div>
                            </div>

                            {branch.manager && (
                                <div>
                                    <Label className="text-sm font-medium text-muted-foreground">Branch Manager</Label>
                                    <div className="mt-1">
                                        <div className="font-medium">{branch.manager.name}</div>
                                        <div className="text-sm text-muted-foreground">{branch.manager.email}</div>
                                        <div className="text-sm text-muted-foreground">{branch.manager.phone}</div>
                                    </div>
                                </div>
                            )}

                            {branch.coordinates && (
                                <div>
                                    <Label className="text-sm font-medium text-muted-foreground">Coordinates</Label>
                                    <div className="text-sm mt-1">
                                        {branch.coordinates.latitude}, {branch.coordinates.longitude}
                                    </div>
                                </div>
                            )}
                        </CardContent>
                    </Card>

                    {/* Working Hours */}
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center">
                                <Clock className="mr-2 h-5 w-5" />
                                Working Hours
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-3">
                                {days.map((day) => {
                                    const hours = branch.working_hours[day.key];
                                    return (
                                        <div key={day.key} className="flex items-center justify-between">
                                            <span className="text-sm font-medium">{day.label}</span>
                                            <span className="text-sm text-muted-foreground">
                                                {hours.open && hours.close ? `${hours.open} - ${hours.close}` : 'Closed'}
                                            </span>
                                        </div>
                                    );
                                })}
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {/* Recent Transactions */}
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center">
                                <Activity className="mr-2 h-5 w-5" />
                                Recent Transactions
                            </CardTitle>
                            <CardDescription>
                                Latest transactions at this branch
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Member</TableHead>
                                        <TableHead>Type</TableHead>
                                        <TableHead className="text-right">Amount</TableHead>
                                        <TableHead>Date</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {branch.recentTransactions.map((transaction) => (
                                        <TableRow key={transaction.id}>
                                            <TableCell className="font-medium">{transaction.memberName}</TableCell>
                                            <TableCell>{getTransactionTypeBadge(transaction.type)}</TableCell>
                                            <TableCell className="text-right">{formatCurrency(transaction.amount)}</TableCell>
                                            <TableCell>{formatDate(transaction.date)}</TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        </CardContent>
                    </Card>

                    {/* Recent Members */}
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center">
                                <Users className="mr-2 h-5 w-5" />
                                Recent Members
                            </CardTitle>
                            <CardDescription>
                                Newest members at this branch
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Member</TableHead>
                                        <TableHead>Join Date</TableHead>
                                        <TableHead className="text-right">Balance</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {branch.recentMembers.map((member) => (
                                        <TableRow key={member.id}>
                                            <TableCell>
                                                <div>
                                                    <div className="font-medium">{member.name}</div>
                                                    <div className="text-sm text-muted-foreground">{member.email}</div>
                                                </div>
                                            </TableCell>
                                            <TableCell>{formatDate(member.joinDate)}</TableCell>
                                            <TableCell className="text-right">{formatCurrency(member.accountBalance)}</TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        </CardContent>
                    </Card>
                </div>

                {/* Staff Members */}
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center">
                            <User className="mr-2 h-5 w-5" />
                            Staff Members
                        </CardTitle>
                        <CardDescription>
                            All staff working at this branch
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Name</TableHead>
                                    <TableHead>Role</TableHead>
                                    <TableHead>Email</TableHead>
                                    <TableHead>Join Date</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {branch.staff.map((staff) => (
                                    <TableRow key={staff.id}>
                                        <TableCell className="font-medium">{staff.name}</TableCell>
                                        <TableCell>
                                            <Badge variant="outline">{staff.role}</Badge>
                                        </TableCell>
                                        <TableCell>{staff.email}</TableCell>
                                        <TableCell>{formatDate(staff.joinDate)}</TableCell>
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
