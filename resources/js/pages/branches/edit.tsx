import { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { DatePicker } from '@/components/ui/date-picker';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { 
    ArrowLeft, 
    Building2, 
    MapPin, 
    Phone, 
    Mail, 
    User, 
    Calendar,
    Clock,
    Save
} from 'lucide-react';

interface Manager {
    id: number;
    name: string;
    email: string;
}

export default function BranchEdit() {
    // Mock branch data - in real app, this would come from props
    const branch = {
        id: 1,
        name: 'Nairobi Main Branch',
        code: 'BR0001',
        address: 'Kenyatta Avenue, Nairobi CBD',
        city: 'Nairobi',
        phone: '+254 20 1234567',
        email: 'nairobi@sacco.co.ke',
        status: 'active',
        opening_date: new Date('2020-01-15'),
        manager_id: 1,
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
        }
    };

    const [workingHours, setWorkingHours] = useState(branch.working_hours);

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: dashboard().url,
        },
        {
            title: 'Branches',
            href: '#',
        },
        {
            title: branch.name,
            href: '#',
        },
        {
            title: 'Edit',
            href: '#',
        },
    ];

    const { data, setData, put, processing, errors } = useForm({
        name: branch.name,
        code: branch.code,
        address: branch.address,
        city: branch.city,
        phone: branch.phone,
        email: branch.email,
        status: branch.status,
        manager_id: branch.manager_id.toString(),
        opening_date: branch.opening_date,
        working_hours: workingHours,
        coordinates: {
            latitude: branch.coordinates.latitude.toString(),
            longitude: branch.coordinates.longitude.toString()
        }
    });

    // Mock available managers - in real app, this would come from props
    const availableManagers: Manager[] = [
        { id: 1, name: 'John Mwangi', email: 'john.mwangi@sacco.co.ke' },
        { id: 2, name: 'Mary Wanjiku', email: 'mary.wanjiku@sacco.co.ke' },
        { id: 3, name: 'Peter Kimani', email: 'peter.kimani@sacco.co.ke' },
        { id: 4, name: 'Grace Akinyi', email: 'grace.akinyi@sacco.co.ke' },
        { id: 5, name: 'David Otieno', email: 'david.otieno@sacco.co.ke' }
    ];

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(`/branches/${branch.id}`, {
            onSuccess: () => {
                // Handle success
            },
            onError: (errors) => {
                console.log('Validation errors:', errors);
            }
        });
    };

    const updateWorkingHours = (day: string, field: 'open' | 'close', value: string) => {
        const newWorkingHours = {
            ...workingHours,
            [day]: {
                ...workingHours[day as keyof typeof workingHours],
                [field]: value
            }
        };
        setWorkingHours(newWorkingHours);
        setData('working_hours', newWorkingHours);
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
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Edit ${branch.name}`} />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div className="flex items-center space-x-4">
                        <Link href={`/branches/${branch.id}`}>
                            <Button variant="outline" size="sm">
                                <ArrowLeft className="mr-2 h-4 w-4" />
                                Back to Branch
                            </Button>
                        </Link>
                        <div>
                            <h1 className="text-3xl font-bold tracking-tight">Edit Branch</h1>
                            <p className="text-muted-foreground">
                                Update branch information and settings
                            </p>
                        </div>
                    </div>
                </div>

                <form onSubmit={handleSubmit} className="space-y-6">
                    {/* Basic Information */}
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center">
                                <Building2 className="mr-2 h-5 w-5" />
                                Basic Information
                            </CardTitle>
                            <CardDescription>
                                Update the basic details for this branch
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div className="space-y-2">
                                    <Label htmlFor="name">Branch Name *</Label>
                                    <Input
                                        id="name"
                                        value={data.name}
                                        onChange={(e) => setData('name', e.target.value)}
                                        placeholder="e.g., Nairobi Main Branch"
                                        className={errors.name ? 'border-red-500' : ''}
                                    />
                                    {errors.name && (
                                        <Alert variant="destructive">
                                            <AlertDescription>{errors.name}</AlertDescription>
                                        </Alert>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="code">Branch Code *</Label>
                                    <Input
                                        id="code"
                                        value={data.code}
                                        onChange={(e) => setData('code', e.target.value)}
                                        placeholder="e.g., BR0001"
                                        className={errors.code ? 'border-red-500' : ''}
                                    />
                                    {errors.code && (
                                        <Alert variant="destructive">
                                            <AlertDescription>{errors.code}</AlertDescription>
                                        </Alert>
                                    )}
                                </div>
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="address">Address *</Label>
                                <Textarea
                                    id="address"
                                    value={data.address}
                                    onChange={(e) => setData('address', e.target.value)}
                                    placeholder="Enter the complete address"
                                    className={errors.address ? 'border-red-500' : ''}
                                />
                                {errors.address && (
                                    <Alert variant="destructive">
                                        <AlertDescription>{errors.address}</AlertDescription>
                                    </Alert>
                                )}
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div className="space-y-2">
                                    <Label htmlFor="city">City *</Label>
                                    <Input
                                        id="city"
                                        value={data.city}
                                        onChange={(e) => setData('city', e.target.value)}
                                        placeholder="e.g., Nairobi"
                                        className={errors.city ? 'border-red-500' : ''}
                                    />
                                    {errors.city && (
                                        <Alert variant="destructive">
                                            <AlertDescription>{errors.city}</AlertDescription>
                                        </Alert>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="status">Status *</Label>
                                    <Select value={data.status} onValueChange={(value) => setData('status', value)}>
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select status" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="active">Active</SelectItem>
                                            <SelectItem value="inactive">Inactive</SelectItem>
                                            <SelectItem value="under_maintenance">Under Maintenance</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    {errors.status && (
                                        <Alert variant="destructive">
                                            <AlertDescription>{errors.status}</AlertDescription>
                                        </Alert>
                                    )}
                                </div>
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="opening_date">Opening Date *</Label>
                                <DatePicker
                                    date={data.opening_date}
                                    setDate={(date) => setData('opening_date', date || new Date())}
                                    placeholder="Select opening date"
                                />
                                {errors.opening_date && (
                                    <Alert variant="destructive">
                                        <AlertDescription>{errors.opening_date}</AlertDescription>
                                    </Alert>
                                )}
                            </div>
                        </CardContent>
                    </Card>

                    {/* Contact Information */}
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center">
                                <Phone className="mr-2 h-5 w-5" />
                                Contact Information
                            </CardTitle>
                            <CardDescription>
                                Update branch contact details
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div className="space-y-2">
                                    <Label htmlFor="phone">Phone Number *</Label>
                                    <Input
                                        id="phone"
                                        value={data.phone}
                                        onChange={(e) => setData('phone', e.target.value)}
                                        placeholder="e.g., +254 20 1234567"
                                        className={errors.phone ? 'border-red-500' : ''}
                                    />
                                    {errors.phone && (
                                        <Alert variant="destructive">
                                            <AlertDescription>{errors.phone}</AlertDescription>
                                        </Alert>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="email">Email Address *</Label>
                                    <Input
                                        id="email"
                                        type="email"
                                        value={data.email}
                                        onChange={(e) => setData('email', e.target.value)}
                                        placeholder="e.g., nairobi@sacco.co.ke"
                                        className={errors.email ? 'border-red-500' : ''}
                                    />
                                    {errors.email && (
                                        <Alert variant="destructive">
                                            <AlertDescription>{errors.email}</AlertDescription>
                                        </Alert>
                                    )}
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Manager Assignment */}
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center">
                                <User className="mr-2 h-5 w-5" />
                                Manager Assignment
                            </CardTitle>
                            <CardDescription>
                                Assign or change the branch manager
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-2">
                                <Label htmlFor="manager_id">Branch Manager</Label>
                                <Select value={data.manager_id} onValueChange={(value) => setData('manager_id', value)}>
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select a manager" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="none">No manager assigned</SelectItem>
                                        {availableManagers.map((manager) => (
                                            <SelectItem key={manager.id} value={manager.id.toString()}>
                                                {manager.name} ({manager.email})
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                {errors.manager_id && (
                                    <Alert variant="destructive">
                                        <AlertDescription>{errors.manager_id}</AlertDescription>
                                    </Alert>
                                )}
                            </div>
                        </CardContent>
                    </Card>

                    {/* Working Hours */}
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center">
                                <Clock className="mr-2 h-5 w-5" />
                                Working Hours
                            </CardTitle>
                            <CardDescription>
                                Update the operating hours for each day of the week
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-4">
                                {days.map((day) => (
                                    <div key={day.key} className="flex items-center space-x-4">
                                        <div className="w-24">
                                            <Label className="text-sm font-medium">{day.label}</Label>
                                        </div>
                                        <div className="flex items-center space-x-2">
                                            <Input
                                                type="time"
                                                value={workingHours[day.key as keyof typeof workingHours].open}
                                                onChange={(e) => updateWorkingHours(day.key, 'open', e.target.value)}
                                                className="w-32"
                                            />
                                            <span className="text-muted-foreground">to</span>
                                            <Input
                                                type="time"
                                                value={workingHours[day.key as keyof typeof workingHours].close}
                                                onChange={(e) => updateWorkingHours(day.key, 'close', e.target.value)}
                                                className="w-32"
                                            />
                                        </div>
                                        {day.key === 'sunday' && (
                                            <Button
                                                type="button"
                                                variant="outline"
                                                size="sm"
                                                onClick={() => {
                                                    const newWorkingHours = {
                                                        ...workingHours,
                                                        sunday: { open: '', close: '' }
                                                    };
                                                    setWorkingHours(newWorkingHours);
                                                    setData('working_hours', newWorkingHours);
                                                }}
                                            >
                                                Closed
                                            </Button>
                                        )}
                                    </div>
                                ))}
                            </div>
                        </CardContent>
                    </Card>

                    {/* Location Coordinates */}
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center">
                                <MapPin className="mr-2 h-5 w-5" />
                                Location Coordinates
                            </CardTitle>
                            <CardDescription>
                                Update GPS coordinates for mapping
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div className="space-y-2">
                                    <Label htmlFor="latitude">Latitude</Label>
                                    <Input
                                        id="latitude"
                                        type="number"
                                        step="any"
                                        value={data.coordinates.latitude}
                                        onChange={(e) => setData('coordinates', {
                                            ...data.coordinates,
                                            latitude: e.target.value
                                        })}
                                        placeholder="e.g., -1.2921"
                                    />
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="longitude">Longitude</Label>
                                    <Input
                                        id="longitude"
                                        type="number"
                                        step="any"
                                        value={data.coordinates.longitude}
                                        onChange={(e) => setData('coordinates', {
                                            ...data.coordinates,
                                            longitude: e.target.value
                                        })}
                                        placeholder="e.g., 36.8219"
                                    />
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Submit Button */}
                    <div className="flex justify-end space-x-4">
                        <Link href={`/branches/${branch.id}`}>
                            <Button variant="outline" type="button">
                                Cancel
                            </Button>
                        </Link>
                        <Button type="submit" disabled={processing}>
                            <Save className="mr-2 h-4 w-4" />
                            {processing ? 'Updating Branch...' : 'Update Branch'}
                        </Button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
