import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { 
    ArrowLeft, 
    Edit, 
    Trash2, 
    Users, 
    CreditCard, 
    DollarSign, 
    Calendar,
    Phone,
    Mail,
    MapPin,
    Building2
} from 'lucide-react';
import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import { index as membersIndex, edit as membersEdit, destroy as membersDestroy } from '@/routes/members';
import MemberDeleteDialog from '@/components/member-delete-dialog';

interface Member {
    id: number;
    name: string;
    email: string;
    member_number: string;
    phone_number: string;
    id_number: string;
    address: string;
    membership_status: string;
    joining_date: string;
    created_at: string;
    branch: {
        id: number;
        name: string;
    } | null;
    accounts: Array<{
        id: number;
        account_number: string;
        account_type: string;
        balance: number;
        status: string;
    }>;
    loans: Array<{
        id: number;
        amount: number;
        status: string;
        created_at: string;
    }>;
}

interface Stats {
    total_deposits: number;
    total_withdrawals: number;
    active_loans: number;
    total_accounts: number;
}

interface Props {
    member: Member;
    stats: Stats;
}

export default function MembersShow({ member, stats }: Props) {
    const [deleteDialogOpen, setDeleteDialogOpen] = useState(false);
    const [isDeleting, setIsDeleting] = useState(false);

    const handleDelete = () => {
        setDeleteDialogOpen(true);
    };

    const handleConfirmDelete = (reason: string) => {
        setIsDeleting(true);
        router.delete(membersDestroy.url(member.id), {
            data: { reason },
            onSuccess: () => {
                setDeleteDialogOpen(false);
                setIsDeleting(false);
            },
            onError: () => {
                setIsDeleting(false);
            }
        });
    };

    const handleCloseDialog = () => {
        setDeleteDialogOpen(false);
        setIsDeleting(false);
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

    return (
        <AppLayout>
            <Head title={`Member: ${member.name}`} />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div className="flex items-center space-x-4">
                        <Link href={membersIndex.url()}>
                            <Button variant="ghost" size="sm">
                                <ArrowLeft className="mr-2 h-4 w-4" />
                                Back to Members
                            </Button>
                        </Link>
                        <div>
                            <h1 className="text-3xl font-bold tracking-tight">{member.name}</h1>
                            <p className="text-muted-foreground">
                                Member #{member.member_number}
                            </p>
                        </div>
                    </div>
                    <div className="flex items-center space-x-2">
                        {getStatusBadge(member.membership_status)}
                        <Link href={membersEdit.url(member.id)}>
                            <Button variant="outline">
                                <Edit className="mr-2 h-4 w-4" />
                                Edit Member
                            </Button>
                        </Link>
                        <Button variant="destructive" onClick={handleDelete}>
                            <Trash2 className="mr-2 h-4 w-4" />
                            Delete
                        </Button>
                    </div>
                </div>

                {/* Stats Cards */}
                <div className="grid auto-rows-min gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Deposits</CardTitle>
                            <DollarSign className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{formatCurrency(stats.total_deposits)}</div>
                            <p className="text-xs text-muted-foreground">
                                All time deposits
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Withdrawals</CardTitle>
                            <DollarSign className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{formatCurrency(stats.total_withdrawals)}</div>
                            <p className="text-xs text-muted-foreground">
                                All time withdrawals
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Active Loans</CardTitle>
                            <CreditCard className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.active_loans}</div>
                            <p className="text-xs text-muted-foreground">
                                Currently active
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Accounts</CardTitle>
                            <CreditCard className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.total_accounts}</div>
                            <p className="text-xs text-muted-foreground">
                                All accounts
                            </p>
                        </CardContent>
                    </Card>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {/* Member Details */}
                    <Card>
                        <CardHeader>
                            <CardTitle>Member Details</CardTitle>
                            <CardDescription>
                                Personal information and contact details
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div className="flex items-center space-x-3">
                                <Users className="h-5 w-5 text-muted-foreground" />
                                <div>
                                    <p className="font-medium">{member.name}</p>
                                    <p className="text-sm text-muted-foreground">Full Name</p>
                                </div>
                            </div>
                            <div className="flex items-center space-x-3">
                                <Mail className="h-5 w-5 text-muted-foreground" />
                                <div>
                                    <p className="font-medium">{member.email}</p>
                                    <p className="text-sm text-muted-foreground">Email Address</p>
                                </div>
                            </div>
                            <div className="flex items-center space-x-3">
                                <Phone className="h-5 w-5 text-muted-foreground" />
                                <div>
                                    <p className="font-medium">{member.phone_number}</p>
                                    <p className="text-sm text-muted-foreground">Phone Number</p>
                                </div>
                            </div>
                            <div className="flex items-center space-x-3">
                                <CreditCard className="h-5 w-5 text-muted-foreground" />
                                <div>
                                    <p className="font-medium">{member.id_number}</p>
                                    <p className="text-sm text-muted-foreground">ID Number</p>
                                </div>
                            </div>
                            <div className="flex items-center space-x-3">
                                <MapPin className="h-5 w-5 text-muted-foreground" />
                                <div>
                                    <p className="font-medium">{member.address}</p>
                                    <p className="text-sm text-muted-foreground">Address</p>
                                </div>
                            </div>
                            <div className="flex items-center space-x-3">
                                <Building2 className="h-5 w-5 text-muted-foreground" />
                                <div>
                                    <p className="font-medium">{member.branch?.name || 'No Branch Assigned'}</p>
                                    <p className="text-sm text-muted-foreground">Branch</p>
                                </div>
                            </div>
                            <div className="flex items-center space-x-3">
                                <Calendar className="h-5 w-5 text-muted-foreground" />
                                <div>
                                    <p className="font-medium">{formatDate(member.joining_date)}</p>
                                    <p className="text-sm text-muted-foreground">Joining Date</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Accounts */}
                    <Card>
                        <CardHeader>
                            <CardTitle>Accounts</CardTitle>
                            <CardDescription>
                                Member's account information
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            {member.accounts.length > 0 ? (
                                <div className="space-y-3">
                                    {member.accounts.map((account) => (
                                        <div key={account.id} className="flex items-center justify-between p-3 border rounded-lg">
                                            <div>
                                                <p className="font-medium">{account.account_number}</p>
                                                <p className="text-sm text-muted-foreground capitalize">
                                                    {account.account_type.replace('_', ' ')}
                                                </p>
                                            </div>
                                            <div className="text-right">
                                                <p className="font-medium">{formatCurrency(account.balance)}</p>
                                                <Badge variant={account.status === 'active' ? 'default' : 'secondary'}>
                                                    {account.status}
                                                </Badge>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <p className="text-muted-foreground">No accounts found</p>
                            )}
                        </CardContent>
                    </Card>
                </div>

                {/* Recent Loans */}
                <Card>
                    <CardHeader>
                        <CardTitle>Recent Loans</CardTitle>
                        <CardDescription>
                            Member's loan history
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        {member.loans.length > 0 ? (
                            <div className="space-y-3">
                                {member.loans.slice(0, 5).map((loan) => (
                                    <div key={loan.id} className="flex items-center justify-between p-3 border rounded-lg">
                                        <div>
                                            <p className="font-medium">{formatCurrency(loan.amount)}</p>
                                            <p className="text-sm text-muted-foreground">
                                                {formatDate(loan.created_at)}
                                            </p>
                                        </div>
                                        <Badge variant={loan.status === 'active' ? 'default' : 'secondary'}>
                                            {loan.status}
                                        </Badge>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <p className="text-muted-foreground">No loans found</p>
                        )}
                    </CardContent>
                </Card>
            </div>

            {/* Member Delete Dialog */}
            <MemberDeleteDialog
                isOpen={deleteDialogOpen}
                onClose={handleCloseDialog}
                onConfirm={handleConfirmDelete}
                member={member}
                isLoading={isDeleting}
            />
        </AppLayout>
    );
}
