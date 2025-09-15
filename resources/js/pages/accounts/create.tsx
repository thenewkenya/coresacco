import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Badge } from '@/components/ui/badge';
import { Skeleton } from '@/components/ui/skeleton';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { 
    ArrowLeft,
    CreditCard,
    DollarSign,
    Percent,
    Shield,
    Info,
    CheckCircle
} from 'lucide-react';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { useState } from 'react';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { index as accountsIndex, store as accountsStore } from '@/routes/accounts';

interface AccountType {
    value: string;
    label: string;
    description: string;
    interest_rate: number;
    minimum_balance: number;
    icon: string;
    color: string;
}

interface Member {
    id: number;
    name: string;
    email: string;
    member_number: string;
    accounts?: any[];
}

interface Props {
    members: Member[];
    accountTypes: AccountType[];
    existingTypes: string[];
    multiAllowed: string[];
}

export default function CreateAccount({ members, accountTypes, existingTypes, multiAllowed }: Props) {
    const { auth } = usePage().props as { auth: { user: any } };
    const user = auth.user || {};
    
    const [selectedType, setSelectedType] = useState('');
    const [memberId, setMemberId] = useState(user.role === 'member' ? user.id?.toString() || '' : '');
    const [initialDeposit, setInitialDeposit] = useState('');
    const [notes, setNotes] = useState('');
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [errors, setErrors] = useState<any>({});

    const selectedAccountType = accountTypes.find(type => type.value === selectedType);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        
        if (!selectedType) {
            setErrors({ account_type: 'Please select an account type' });
            return;
        }

        if (user.role !== 'member' && !memberId) {
            setErrors({ member_id: 'Please select a member' });
            return;
        }

        setIsSubmitting(true);
        setErrors({});
        
        const formData = {
            account_type: selectedType,
            member_id: user.role === 'member' ? user.id : memberId,
            initial_deposit: initialDeposit ? parseFloat(initialDeposit) : 0,
            notes: notes,
        };

        router.post(accountsStore.url(), formData, {
            onSuccess: () => {
                setIsSubmitting(false);
            },
            onError: (errors) => {
                setErrors(errors);
                setIsSubmitting(false);
            },
            onFinish: () => {
                setIsSubmitting(false);
            }
        });
    };

    const formatCurrency = (amount: number) => {
        return new Intl.NumberFormat('en-KE', {
            style: 'currency',
            currency: 'KES',
        }).format(amount);
    };

    const getAccountIcon = (iconName: string) => {
        const icons = {
            'building-library': CreditCard,
            'banknotes': DollarSign,
            'safe': Shield,
            'academic-cap': CreditCard,
            'flag': CreditCard,
            'briefcase': CreditCard,
        };
        return icons[iconName as keyof typeof icons] || CreditCard;
    };

    const isTypeAlreadyExists = (type: string) => {
        return existingTypes.includes(type) && !multiAllowed.includes(type);
    };

    const canOpenType = (type: string) => {
        return !isTypeAlreadyExists(type) || multiAllowed.includes(type);
    };

    return (
        <AppLayout>
            <Head title="Open New Account" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div className="flex items-center space-x-4">
                        <Link href={accountsIndex.url()}>
                            <Button variant="outline" size="sm">
                                <ArrowLeft className="mr-2 h-4 w-4" />
                                Back to Accounts
                            </Button>
                        </Link>
                        <div>
                            <h1 className="text-3xl font-bold tracking-tight">Open New Account</h1>
                            <p className="text-muted-foreground">
                                Create a new SACCO account for {user.role === 'member' ? 'yourself' : 'a member'}
                            </p>
                        </div>
                    </div>
                </div>

                <div className="grid gap-6 lg:grid-cols-3">
                    {/* Account Type Selection */}
                    <div className="lg:col-span-2">
                        <Card>
                            <CardHeader>
                                <CardTitle>Select Account Type</CardTitle>
                                <CardDescription>
                                    Choose the type of account you want to open
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <div className="grid gap-4 md:grid-cols-2">
                                    {accountTypes.map((type) => {
                                        const IconComponent = getAccountIcon(type.icon);
                                        const isExisting = isTypeAlreadyExists(type.value);
                                        const canOpen = canOpenType(type.value);
                                        
                                        return (
                                            <div
                                                key={type.value}
                                                className={`p-4 border rounded-lg cursor-pointer transition-colors ${
                                                    selectedType === type.value
                                                        ? 'border-primary bg-primary/5'
                                                        : canOpen
                                                        ? 'hover:border-primary/50'
                                                        : 'opacity-50 cursor-not-allowed'
                                                }`}
                                                onClick={() => canOpen && setSelectedType(type.value)}
                                            >
                                                <div className="flex items-start space-x-3">
                                                    <div className={`w-8 h-8 rounded-full flex items-center justify-center ${
                                                        type.color === 'blue' ? 'bg-blue-100 text-blue-600' :
                                                        type.color === 'purple' ? 'bg-purple-100 text-purple-600' :
                                                        type.color === 'green' ? 'bg-green-100 text-green-600' :
                                                        type.color === 'yellow' ? 'bg-yellow-100 text-yellow-600' :
                                                        type.color === 'pink' ? 'bg-pink-100 text-pink-600' :
                                                        'bg-gray-100 text-gray-600'
                                                    }`}>
                                                        <IconComponent className="h-4 w-4" />
                                                    </div>
                                                    <div className="flex-1">
                                                        <div className="flex items-center space-x-2">
                                                            <h3 className="font-semibold">{type.label}</h3>
                                                            {isExisting && (
                                                                <Badge variant="secondary" className="text-xs">
                                                                    Existing
                                                                </Badge>
                                                            )}
                                                            {!canOpen && (
                                                                <Badge variant="outline" className="text-xs">
                                                                    Not Available
                                                                </Badge>
                                                            )}
                                                        </div>
                                                        <p className="text-sm text-muted-foreground mt-1">
                                                            {type.description}
                                                        </p>
                                                        <div className="flex items-center space-x-4 mt-2 text-xs text-muted-foreground">
                                                            <div className="flex items-center space-x-1">
                                                                <Percent className="h-3 w-3" />
                                                                <span>{type.interest_rate}% interest</span>
                                                            </div>
                                                            <div className="flex items-center space-x-1">
                                                                <DollarSign className="h-3 w-3" />
                                                                <span>Min: {formatCurrency(type.minimum_balance)}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        );
                                    })}
                                </div>
                            </CardContent>
                        </Card>

                        {/* Account Details Form */}
                        {selectedType && (
                            <Card className="mt-6">
                                <CardHeader>
                                    <CardTitle>Account Details</CardTitle>
                                    <CardDescription>
                                        Provide additional information for the new account
                                    </CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <form onSubmit={handleSubmit} className="space-y-4">
                                        {/* Member Selection (for staff only) */}
                                        {user.role !== 'member' && (
                                            <div className="space-y-2">
                                                <Label htmlFor="member">Select Member</Label>
                                                <Select value={memberId} onValueChange={setMemberId}>
                                                    <SelectTrigger>
                                                        <SelectValue placeholder="Choose a member" />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        {members.map((member) => (
                                                            <SelectItem key={member.id} value={member.id.toString()}>
                                                                {member.name} ({member.member_number})
                                                            </SelectItem>
                                                        ))}
                                                    </SelectContent>
                                                </Select>
                                            </div>
                                        )}

                                        {/* Initial Deposit */}
                                        <div className="space-y-2">
                                            <Label htmlFor="initial_deposit">Initial Deposit (Optional)</Label>
                                            <Input
                                                id="initial_deposit"
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                placeholder="0.00"
                                                value={initialDeposit}
                                                onChange={(e) => setInitialDeposit(e.target.value)}
                                            />
                                            <p className="text-xs text-muted-foreground">
                                                Minimum deposit: {selectedAccountType ? formatCurrency(selectedAccountType.minimum_balance) : 'N/A'}
                                            </p>
                                        </div>

                                        {/* Notes */}
                                        <div className="space-y-2">
                                            <Label htmlFor="notes">Notes (Optional)</Label>
                                            <Textarea
                                                id="notes"
                                                placeholder="Any additional notes about this account..."
                                                value={notes}
                                                onChange={(e) => setNotes(e.target.value)}
                                                rows={3}
                                            />
                                        </div>

                                        {/* Error Display */}
                                        {Object.keys(errors).length > 0 && (
                                            <Alert variant="destructive">
                                                <AlertDescription>
                                                    <h4 className="font-medium mb-2">Please fix the following errors:</h4>
                                                    <ul className="text-sm space-y-1">
                                                        {Object.entries(errors).map(([field, message]) => (
                                                            <li key={field}>• {message}</li>
                                                        ))}
                                                    </ul>
                                                </AlertDescription>
                                            </Alert>
                                        )}

                                        {/* Submit Button */}
                                        <div className="flex justify-end space-x-2 pt-4">
                                            <Link href={accountsIndex.url()}>
                                                <Button variant="outline" disabled={isSubmitting}>Cancel</Button>
                                            </Link>
                                            <Button 
                                                type="submit" 
                                                disabled={!selectedType || (user.role !== 'member' && !memberId) || isSubmitting}
                                            >
                                                {isSubmitting ? (
                                                    <>
                                                        <Skeleton className="h-4 w-4 mr-2 bg-white/20" />
                                                        Creating Account...
                                                    </>
                                                ) : (
                                                    <>
                                                        <CheckCircle className="mr-2 h-4 w-4" />
                                                        Open Account
                                                    </>
                                                )}
                                            </Button>
                                        </div>
                                    </form>
                                </CardContent>
                            </Card>
                        )}
                    </div>

                    {/* Account Type Info Sidebar */}
                    {selectedAccountType && (
                        <div className="lg:col-span-1">
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center space-x-2">
                                        <Info className="h-5 w-5" />
                                        <span>Account Information</span>
                                    </CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div>
                                        <h4 className="font-semibold">{selectedAccountType.label}</h4>
                                        <p className="text-sm text-muted-foreground">
                                            {selectedAccountType.description}
                                        </p>
                                    </div>
                                    
                                    <div className="space-y-3">
                                        <div className="flex justify-between">
                                            <span className="text-sm text-muted-foreground">Interest Rate:</span>
                                            <span className="font-medium">{selectedAccountType.interest_rate}%</span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span className="text-sm text-muted-foreground">Minimum Balance:</span>
                                            <span className="font-medium">{formatCurrency(selectedAccountType.minimum_balance)}</span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span className="text-sm text-muted-foreground">Account Type:</span>
                                            <Badge variant="outline">{selectedAccountType.value}</Badge>
                                        </div>
                                    </div>

                                    {isTypeAlreadyExists(selectedAccountType.value) && (
                                        <Alert>
                                            <AlertDescription>
                                                <strong>Note:</strong> You already have a {selectedAccountType.label} account.
                                                {multiAllowed.includes(selectedAccountType.value) 
                                                    ? ' You can open multiple accounts of this type.'
                                                    : ' Only one account of this type is allowed.'
                                                }
                                            </AlertDescription>
                                        </Alert>
                                    )}
                                </CardContent>
                            </Card>
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
