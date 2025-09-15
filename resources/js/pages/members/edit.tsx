import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { ArrowLeft, Save } from 'lucide-react';
import { Head, Link, useForm } from '@inertiajs/react';
import { index as membersIndex, show as membersShow, update as membersUpdate } from '@/routes/members';

interface Member {
    id: number;
    name: string;
    email: string;
    phone_number: string;
    id_number: string;
    address: string;
    membership_status: string;
    branch_id: number;
}

interface Branch {
    id: number;
    name: string;
}

interface Props {
    member: Member;
    branches: Branch[];
}

export default function MembersEdit({ member, branches }: Props) {
    const { data, setData, put, processing, errors } = useForm({
        name: member.name,
        email: member.email,
        phone_number: member.phone_number,
        id_number: member.id_number,
        address: member.address,
        branch_id: member.branch_id.toString(),
        membership_status: member.membership_status,
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(membersUpdate.url(member.id));
    };

    return (
        <AppLayout>
            <Head title={`Edit Member: ${member.name}`} />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div className="flex items-center space-x-4">
                        <Link href={membersShow.url(member.id)}>
                            <Button variant="ghost" size="sm">
                                <ArrowLeft className="mr-2 h-4 w-4" />
                                Back to Member
                            </Button>
                        </Link>
                        <div>
                            <h1 className="text-3xl font-bold tracking-tight">Edit Member</h1>
                            <p className="text-muted-foreground">
                                Update member information
                            </p>
                        </div>
                    </div>
                </div>

                {/* Form */}
                <Card>
                    <CardHeader>
                        <CardTitle>Member Information</CardTitle>
                        <CardDescription>
                            Update the member's details
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={handleSubmit} className="space-y-6">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {/* Personal Information */}
                                <div className="space-y-4">
                                    <h3 className="text-lg font-medium">Personal Information</h3>
                                    
                                    <div className="space-y-2">
                                        <Label htmlFor="name">Full Name *</Label>
                                        <Input
                                            id="name"
                                            value={data.name}
                                            onChange={(e) => setData('name', e.target.value)}
                                            placeholder="Enter full name"
                                            className={errors.name ? 'border-red-500' : ''}
                                        />
                                        {errors.name && (
                                            <p className="text-sm text-red-500">{errors.name}</p>
                                        )}
                                    </div>

                                    <div className="space-y-2">
                                        <Label htmlFor="email">Email Address *</Label>
                                        <Input
                                            id="email"
                                            type="email"
                                            value={data.email}
                                            onChange={(e) => setData('email', e.target.value)}
                                            placeholder="Enter email address"
                                            className={errors.email ? 'border-red-500' : ''}
                                        />
                                        {errors.email && (
                                            <p className="text-sm text-red-500">{errors.email}</p>
                                        )}
                                    </div>

                                    <div className="space-y-2">
                                        <Label htmlFor="phone_number">Phone Number *</Label>
                                        <Input
                                            id="phone_number"
                                            value={data.phone_number}
                                            onChange={(e) => setData('phone_number', e.target.value)}
                                            placeholder="Enter phone number"
                                            className={errors.phone_number ? 'border-red-500' : ''}
                                        />
                                        {errors.phone_number && (
                                            <p className="text-sm text-red-500">{errors.phone_number}</p>
                                        )}
                                    </div>

                                    <div className="space-y-2">
                                        <Label htmlFor="id_number">ID Number *</Label>
                                        <Input
                                            id="id_number"
                                            value={data.id_number}
                                            onChange={(e) => setData('id_number', e.target.value)}
                                            placeholder="Enter ID number"
                                            className={errors.id_number ? 'border-red-500' : ''}
                                        />
                                        {errors.id_number && (
                                            <p className="text-sm text-red-500">{errors.id_number}</p>
                                        )}
                                    </div>
                                </div>

                                {/* Additional Information */}
                                <div className="space-y-4">
                                    <h3 className="text-lg font-medium">Additional Information</h3>
                                    
                                    <div className="space-y-2">
                                        <Label htmlFor="address">Address *</Label>
                                        <Textarea
                                            id="address"
                                            value={data.address}
                                            onChange={(e) => setData('address', e.target.value)}
                                            placeholder="Enter full address"
                                            className={errors.address ? 'border-red-500' : ''}
                                            rows={3}
                                        />
                                        {errors.address && (
                                            <p className="text-sm text-red-500">{errors.address}</p>
                                        )}
                                    </div>

                                    <div className="space-y-2">
                                        <Label htmlFor="branch_id">Branch *</Label>
                                        <Select value={data.branch_id} onValueChange={(value) => setData('branch_id', value)}>
                                            <SelectTrigger className={errors.branch_id ? 'border-red-500' : ''}>
                                                <SelectValue placeholder="Select branch" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                {branches.map((branch) => (
                                                    <SelectItem key={branch.id} value={branch.id.toString()}>
                                                        {branch.name}
                                                    </SelectItem>
                                                ))}
                                            </SelectContent>
                                        </Select>
                                        {errors.branch_id && (
                                            <p className="text-sm text-red-500">{errors.branch_id}</p>
                                        )}
                                    </div>

                                    <div className="space-y-2">
                                        <Label htmlFor="membership_status">Membership Status *</Label>
                                        <Select value={data.membership_status} onValueChange={(value) => setData('membership_status', value)}>
                                            <SelectTrigger className={errors.membership_status ? 'border-red-500' : ''}>
                                                <SelectValue placeholder="Select status" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="active">Active</SelectItem>
                                                <SelectItem value="inactive">Inactive</SelectItem>
                                                <SelectItem value="suspended">Suspended</SelectItem>
                                            </SelectContent>
                                        </Select>
                                        {errors.membership_status && (
                                            <p className="text-sm text-red-500">{errors.membership_status}</p>
                                        )}
                                    </div>
                                </div>
                            </div>

                            {/* Submit Button */}
                            <div className="flex justify-end space-x-2">
                                <Link href={membersShow.url(member.id)}>
                                    <Button variant="outline" type="button">
                                        Cancel
                                    </Button>
                                </Link>
                                <Button type="submit" disabled={processing}>
                                    <Save className="mr-2 h-4 w-4" />
                                    {processing ? 'Updating...' : 'Update Member'}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
