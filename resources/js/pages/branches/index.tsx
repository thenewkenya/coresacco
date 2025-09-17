import { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { 
    Plus, 
    Building2, 
    MapPin, 
    Users, 
    Phone, 
    Search, 
    Eye, 
    Edit, 
    MoreHorizontal,
    TrendingUp,
    DollarSign,
    CreditCard,
    Calendar
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
    manager?: {
        id: number;
        name: string;
        email: string;
    };
    analytics: {
        totalMembers: number;
        totalStaff: number;
        totalDeposits: number;
        activeLoans: number;
        monthlyTransactions: number;
    };
}

export default function BranchesIndex() {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: dashboard().url,
        },
        {
            title: 'Branches',
            href: '#',
        },
    ];

    const [searchTerm, setSearchTerm] = useState('');
    const [statusFilter, setStatusFilter] = useState('all');
    const [cityFilter, setCityFilter] = useState('all');

    // Mock data - in real app, this would come from the backend
    const branches: Branch[] = [
        {
            id: 1,
            name: 'Nairobi Main Branch',
            code: 'BR0001',
            address: 'Kenyatta Avenue, Nairobi CBD',
            city: 'Nairobi',
            phone: '+254 20 1234567',
            email: 'nairobi@sacco.co.ke',
            status: 'active',
            opening_date: '2020-01-15',
            manager: { id: 1, name: 'John Mwangi', email: 'john.mwangi@sacco.co.ke' },
            analytics: { totalMembers: 450, totalStaff: 12, totalDeposits: 25000000, activeLoans: 120, monthlyTransactions: 850 }
        },
        {
            id: 2,
            name: 'Mombasa Branch',
            code: 'BR0002',
            address: 'Moi Avenue, Mombasa',
            city: 'Mombasa',
            phone: '+254 41 2345678',
            email: 'mombasa@sacco.co.ke',
            status: 'active',
            opening_date: '2020-06-20',
            manager: { id: 2, name: 'Mary Wanjiku', email: 'mary.wanjiku@sacco.co.ke' },
            analytics: { totalMembers: 280, totalStaff: 8, totalDeposits: 18000000, activeLoans: 75, monthlyTransactions: 520 }
        },
        {
            id: 3,
            name: 'Kisumu Branch',
            code: 'BR0003',
            address: 'Oginga Odinga Street, Kisumu',
            city: 'Kisumu',
            phone: '+254 57 3456789',
            email: 'kisumu@sacco.co.ke',
            status: 'active',
            opening_date: '2021-03-10',
            manager: { id: 3, name: 'Peter Kimani', email: 'peter.kimani@sacco.co.ke' },
            analytics: { totalMembers: 320, totalStaff: 10, totalDeposits: 20000000, activeLoans: 90, monthlyTransactions: 680 }
        },
        {
            id: 4,
            name: 'Nakuru Branch',
            code: 'BR0004',
            address: 'Kenyatta Avenue, Nakuru',
            city: 'Nakuru',
            phone: '+254 51 4567890',
            email: 'nakuru@sacco.co.ke',
            status: 'active',
            opening_date: '2021-08-15',
            manager: { id: 4, name: 'Grace Akinyi', email: 'grace.akinyi@sacco.co.ke' },
            analytics: { totalMembers: 200, totalStaff: 6, totalDeposits: 12000000, activeLoans: 55, monthlyTransactions: 420 }
        },
        {
            id: 5,
            name: 'Eldoret Branch',
            code: 'BR0005',
            address: 'Uganda Road, Eldoret',
            city: 'Eldoret',
            phone: '+254 53 5678901',
            email: 'eldoret@sacco.co.ke',
            status: 'under_maintenance',
            opening_date: '2022-01-20',
            manager: { id: 5, name: 'David Otieno', email: 'david.otieno@sacco.co.ke' },
            analytics: { totalMembers: 150, totalStaff: 5, totalDeposits: 8000000, activeLoans: 40, monthlyTransactions: 280 }
        },
        {
            id: 6,
            name: 'Thika Branch',
            code: 'BR0006',
            address: 'General Kago Road, Thika',
            city: 'Thika',
            phone: '+254 67 6789012',
            email: 'thika@sacco.co.ke',
            status: 'active',
            opening_date: '2022-05-10',
            manager: { id: 6, name: 'Alice Wanjala', email: 'alice.wanjala@sacco.co.ke' },
            analytics: { totalMembers: 180, totalStaff: 7, totalDeposits: 10000000, activeLoans: 50, monthlyTransactions: 350 }
        }
    ];

    const stats = {
        totalBranches: branches.length,
        activeBranches: branches.filter(b => b.status === 'active').length,
        totalStaff: branches.reduce((sum, b) => sum + b.analytics.totalStaff, 0),
        totalMembers: branches.reduce((sum, b) => sum + b.analytics.totalMembers, 0),
        totalDeposits: branches.reduce((sum, b) => sum + b.analytics.totalDeposits, 0),
        activeLoans: branches.reduce((sum, b) => sum + b.analytics.activeLoans, 0)
    };

    const cities = [...new Set(branches.map(b => b.city))];

    const filteredBranches = branches.filter(branch => {
        const matchesSearch = branch.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
                            branch.code.toLowerCase().includes(searchTerm.toLowerCase()) ||
                            branch.city.toLowerCase().includes(searchTerm.toLowerCase()) ||
                            branch.address.toLowerCase().includes(searchTerm.toLowerCase());
        
        const matchesStatus = statusFilter === 'all' || branch.status === statusFilter;
        const matchesCity = cityFilter === 'all' || branch.city === cityFilter;
        
        return matchesSearch && matchesStatus && matchesCity;
    });

    const formatCurrency = (amount: number) => {
        return new Intl.NumberFormat('en-KE', {
            style: 'currency',
            currency: 'KES',
        }).format(amount);
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

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Branches" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">Branches</h1>
                        <p className="text-muted-foreground">
                            Manage SACCO branches and locations
                        </p>
                    </div>
                    <Link href="/branches/create">
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            Add Branch
                        </Button>
                    </Link>
                </div>

                {/* Stats Cards */}
                <div className="grid auto-rows-min gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Branches</CardTitle>
                            <Building2 className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.totalBranches}</div>
                            <p className="text-xs text-muted-foreground">
                                {stats.activeBranches} active locations
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Staff</CardTitle>
                            <Users className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.totalStaff}</div>
                            <p className="text-xs text-muted-foreground">
                                Across all branches
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Members</CardTitle>
                            <Users className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.totalMembers.toLocaleString()}</div>
                            <p className="text-xs text-muted-foreground">
                                Active members
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Deposits</CardTitle>
                            <DollarSign className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{formatCurrency(stats.totalDeposits)}</div>
                            <p className="text-xs text-muted-foreground">
                                Across all branches
                            </p>
                        </CardContent>
                    </Card>
                </div>

                {/* Search and Filters */}
                <Card>
                    <CardHeader>
                        <CardTitle>Search & Filter Branches</CardTitle>
                        <CardDescription>
                            Find branches by name, location, status, or city
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div className="relative">
                                <Search className="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                                <Input 
                                    placeholder="Search branches..." 
                                    className="pl-8"
                                    value={searchTerm}
                                    onChange={(e) => setSearchTerm(e.target.value)}
                                />
                            </div>
                            <Select value={statusFilter} onValueChange={setStatusFilter}>
                                <SelectTrigger>
                                    <SelectValue placeholder="Filter by status" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">All Status</SelectItem>
                                    <SelectItem value="active">Active</SelectItem>
                                    <SelectItem value="inactive">Inactive</SelectItem>
                                    <SelectItem value="under_maintenance">Under Maintenance</SelectItem>
                                </SelectContent>
                            </Select>
                            <Select value={cityFilter} onValueChange={setCityFilter}>
                                <SelectTrigger>
                                    <SelectValue placeholder="Filter by city" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">All Cities</SelectItem>
                                    {cities.map(city => (
                                        <SelectItem key={city} value={city}>{city}</SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            <Button 
                                variant="outline" 
                                onClick={() => {
                                    setSearchTerm('');
                                    setStatusFilter('all');
                                    setCityFilter('all');
                                }}
                            >
                                Clear Filters
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                {/* Branches Table */}
                <Card>
                    <CardHeader>
                        <CardTitle>Branch Locations</CardTitle>
                        <CardDescription>
                            {filteredBranches.length} of {branches.length} branches
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Branch</TableHead>
                                    <TableHead>Location</TableHead>
                                    <TableHead>Manager</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead className="text-right">Members</TableHead>
                                    <TableHead className="text-right">Staff</TableHead>
                                    <TableHead className="text-right">Deposits</TableHead>
                                    <TableHead className="text-right">Actions</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {filteredBranches.map((branch) => (
                                    <TableRow key={branch.id}>
                                        <TableCell>
                                            <div>
                                                <div className="font-medium">{branch.name}</div>
                                                <div className="text-sm text-muted-foreground">{branch.code}</div>
                                            </div>
                                        </TableCell>
                                        <TableCell>
                                            <div>
                                                <div className="font-medium">{branch.city}</div>
                                                <div className="text-sm text-muted-foreground">{branch.address}</div>
                                            </div>
                                        </TableCell>
                                        <TableCell>
                                            {branch.manager ? (
                                                <div>
                                                    <div className="font-medium">{branch.manager.name}</div>
                                                    <div className="text-sm text-muted-foreground">{branch.manager.email}</div>
                                                </div>
                                            ) : (
                                                <span className="text-muted-foreground">No manager assigned</span>
                                            )}
                                        </TableCell>
                                        <TableCell>{getStatusBadge(branch.status)}</TableCell>
                                        <TableCell className="text-right">{branch.analytics.totalMembers.toLocaleString()}</TableCell>
                                        <TableCell className="text-right">{branch.analytics.totalStaff}</TableCell>
                                        <TableCell className="text-right">{formatCurrency(branch.analytics.totalDeposits)}</TableCell>
                                        <TableCell className="text-right">
                                            <div className="flex items-center justify-end space-x-2">
                                                <Link href={`/branches/${branch.id}`}>
                                                    <Button variant="outline" size="sm">
                                                        <Eye className="h-4 w-4" />
                                                    </Button>
                                                </Link>
                                                <Link href={`/branches/${branch.id}/edit`}>
                                                    <Button variant="outline" size="sm">
                                                        <Edit className="h-4 w-4" />
                                                    </Button>
                                                </Link>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                        
                        {filteredBranches.length === 0 && (
                            <div className="text-center py-8 text-muted-foreground">
                                <Building2 className="mx-auto h-12 w-12 mb-4" />
                                <p>No branches found matching your criteria</p>
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}

