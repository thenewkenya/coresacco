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
    ArrowLeftRight,
    DollarSign,
    CreditCard,
    User,
    CheckCircle,
    Smartphone,
    Wifi
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
import { index as transactionsIndex, store as transactionsStore } from '@/routes/transactions';

interface Account {
    id: number;
    account_number: string;
    account_type: string;
    balance: number;
    member?: {
        id: number;
        name: string;
        member_number: string;
    };
}

interface Props {
    accounts: Account[];
    transactionTypes: Record<string, string>;
    paymentMethods: Record<string, string>;
}

export default function CreateTransaction({ accounts, transactionTypes, paymentMethods }: Props) {
    const { auth } = usePage().props as { auth: { user: any } };
    const user = auth.user;
    
    const [selectedAccount, setSelectedAccount] = useState('');
    const [transactionType, setTransactionType] = useState('');
    const [amount, setAmount] = useState('');
    const [description, setDescription] = useState('');
    const [referenceNumber, setReferenceNumber] = useState('');
    const [paymentMethod, setPaymentMethod] = useState('');
    const [phoneNumber, setPhoneNumber] = useState(user?.phone_number || '');
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [errors, setErrors] = useState<any>({});

    const selectedAccountData = accounts.find(account => account.id.toString() === selectedAccount);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        
        if (!selectedAccount) {
            setErrors({ account_id: 'Please select an account' });
            return;
        }

        if (!transactionType) {
            setErrors({ type: 'Please select a transaction type' });
            return;
        }

        if (!amount || parseFloat(amount) <= 0) {
            setErrors({ amount: 'Please enter a valid amount' });
            return;
        }

        if (!description.trim()) {
            setErrors({ description: 'Please enter a description' });
            return;
        }

        // Validate payment method for deposits
        if (transactionType === 'deposit' && !paymentMethod) {
            setErrors({ payment_method: 'Please select a payment method' });
            return;
        }

        // Validate phone number for M-Pesa payments
        if (paymentMethod === 'mpesa' && !phoneNumber.trim()) {
            setErrors({ phone_number: 'Please enter your phone number' });
            return;
        }

        setIsSubmitting(true);
        setErrors({});
        
        const formData = {
            account_id: selectedAccount,
            type: transactionType,
            amount: parseFloat(amount),
            description: description.trim(),
            reference_number: referenceNumber.trim() || undefined,
            payment_method: paymentMethod || undefined,
            phone_number: phoneNumber.trim() || undefined,
        };

        router.post(transactionsStore.url(), formData, {
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

    const getAccountTypeBadge = (type: string) => {
        const variants = {
            savings: 'secondary',
            shares: 'default',
            deposits: 'default',
            junior: 'outline',
            goal_based: 'default',
            business: 'secondary',
        } as const;
        
        return (
            <Badge variant={variants[type as keyof typeof variants] || 'outline'}>
                {type.replace('_', ' ').toUpperCase()}
            </Badge>
        );
    };

    return (
        <AppLayout>
            <Head title="Create Transaction" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div className="flex items-center space-x-4">
                        <Link href={transactionsIndex.url()}>
                            <Button variant="outline" size="sm">
                                <ArrowLeft className="mr-2 h-4 w-4" />
                                Back to Transactions
                            </Button>
                        </Link>
                        <div>
                            <h1 className="text-3xl font-bold tracking-tight">Create Transaction</h1>
                            <p className="text-muted-foreground">
                                Record a new financial transaction
                            </p>
                        </div>
                    </div>
                </div>

                <div className="grid gap-6 lg:grid-cols-3">
                    {/* Transaction Form */}
                    <div className="lg:col-span-2">
                        <Card>
                            <CardHeader>
                                <CardTitle>Transaction Details</CardTitle>
                                <CardDescription>
                                    Enter the details for the new transaction
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <form onSubmit={handleSubmit} className="space-y-4">
                                    {/* Account Selection */}
                                    <div className="space-y-2">
                                        <Label htmlFor="account">Select Account</Label>
                                        <Select value={selectedAccount} onValueChange={setSelectedAccount}>
                                            <SelectTrigger>
                                                <SelectValue placeholder="Choose an account" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                {accounts.map((account) => (
                                                    <SelectItem key={account.id} value={account.id.toString()}>
                                                        <div className="flex items-center space-x-2">
                                                            <span>{account.account_number}</span>
                                                            <span className="text-muted-foreground">
                                                                ({account.account_type})
                                                            </span>
                                                            {account.member && (
                                                                <span className="text-muted-foreground">
                                                                    - {account.member.name}
                                                                </span>
                                                            )}
                                                        </div>
                                                    </SelectItem>
                                                ))}
                                            </SelectContent>
                                        </Select>
                                        {errors.account_id && (
                                            <p className="text-sm text-red-600">{errors.account_id}</p>
                                        )}
                                    </div>

                                    {/* Transaction Type */}
                                    <div className="space-y-2">
                                        <Label htmlFor="type">Transaction Type</Label>
                                        <Select value={transactionType} onValueChange={setTransactionType}>
                                            <SelectTrigger>
                                                <SelectValue placeholder="Select transaction type" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                {Object.entries(transactionTypes).map(([value, label]) => (
                                                    <SelectItem key={value} value={value}>
                                                        {label}
                                                    </SelectItem>
                                                ))}
                                            </SelectContent>
                                        </Select>
                                        {errors.type && (
                                            <p className="text-sm text-red-600">{errors.type}</p>
                                        )}
                                    </div>

                                    {/* Payment Method - Only show for deposits */}
                                    {transactionType === 'deposit' && (
                                        <div className="space-y-2">
                                            <Label htmlFor="payment_method">Payment Method</Label>
                                            <Select value={paymentMethod} onValueChange={setPaymentMethod}>
                                                <SelectTrigger>
                                                    <SelectValue placeholder="Select payment method" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    {Object.entries(paymentMethods).map(([value, label]) => (
                                                        <SelectItem key={value} value={value}>
                                                            <div className="flex items-center space-x-2">
                                                                {value === 'cash' && <DollarSign className="h-4 w-4" />}
                                                                {value === 'mpesa' && <Smartphone className="h-4 w-4 text-green-600" />}
                                                                {value === 'bank_transfer' && <CreditCard className="h-4 w-4" />}
                                                                <span>{label}</span>
                                                            </div>
                                                        </SelectItem>
                                                    ))}
                                                </SelectContent>
                                            </Select>
                                            {errors.payment_method && (
                                                <p className="text-sm text-red-600">{errors.payment_method}</p>
                                            )}
                                        </div>
                                    )}

                                    {/* Phone Number - Only show for M-Pesa payments */}
                                    {paymentMethod === 'mpesa' && (
                                        <div className="space-y-2">
                                            <Label htmlFor="phone_number">Phone Number</Label>
                                            <Input
                                                id="phone_number"
                                                type="tel"
                                                placeholder="e.g., 254712345678"
                                                value={phoneNumber}
                                                onChange={(e) => setPhoneNumber(e.target.value)}
                                            />
                                            <p className="text-xs text-muted-foreground">
                                                Enter your phone number in international format (254XXXXXXXXX) for M-Pesa
                                            </p>
                                            {errors.phone_number && (
                                                <p className="text-sm text-red-600">{errors.phone_number}</p>
                                            )}
                                        </div>
                                    )}

                                    {/* Amount */}
                                    <div className="space-y-2">
                                        <Label htmlFor="amount">Amount</Label>
                                        <Input
                                            id="amount"
                                            type="number"
                                            step="0.01"
                                            min="0.01"
                                            placeholder="0.00"
                                            value={amount}
                                            onChange={(e) => setAmount(e.target.value)}
                                        />
                                        {errors.amount && (
                                            <p className="text-sm text-red-600">{errors.amount}</p>
                                        )}
                                    </div>

                                    {/* Description */}
                                    <div className="space-y-2">
                                        <Label htmlFor="description">Description</Label>
                                        <Textarea
                                            id="description"
                                            placeholder="Enter transaction description..."
                                            value={description}
                                            onChange={(e) => setDescription(e.target.value)}
                                            rows={3}
                                        />
                                        {errors.description && (
                                            <p className="text-sm text-red-600">{errors.description}</p>
                                        )}
                                    </div>

                                    {/* Reference Number (Optional) */}
                                    <div className="space-y-2">
                                        <Label htmlFor="reference_number">Reference Number (Optional)</Label>
                                        <Input
                                            id="reference_number"
                                            placeholder="Enter reference number..."
                                            value={referenceNumber}
                                            onChange={(e) => setReferenceNumber(e.target.value)}
                                        />
                                        <p className="text-xs text-muted-foreground">
                                            Leave blank to auto-generate
                                        </p>
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
                                        <Link href={transactionsIndex.url()}>
                                            <Button variant="outline" disabled={isSubmitting}>Cancel</Button>
                                        </Link>
                                        <Button 
                                            type="submit" 
                                            disabled={
                                                !selectedAccount || 
                                                !transactionType || 
                                                !amount || 
                                                !description || 
                                                (transactionType === 'deposit' && !paymentMethod) ||
                                                (paymentMethod === 'mpesa' && !phoneNumber) ||
                                                isSubmitting
                                            }
                                        >
                                            {isSubmitting ? (
                                                <>
                                                    <Skeleton className="h-4 w-4 mr-2 bg-white/20" />
                                                    Creating Transaction...
                                                </>
                                            ) : (
                                                <>
                                                    <CheckCircle className="mr-2 h-4 w-4" />
                                                    Create Transaction
                                                </>
                                            )}
                                        </Button>
                                    </div>
                                </form>
                            </CardContent>
                        </Card>
                    </div>

                    {/* Account Info Sidebar */}
                    {selectedAccountData && (
                        <div className="lg:col-span-1">
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center space-x-2">
                                        <CreditCard className="h-5 w-5" />
                                        <span>Account Information</span>
                                    </CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div>
                                        <h4 className="font-semibold">{selectedAccountData.account_number}</h4>
                                        <p className="text-sm text-muted-foreground">Account Number</p>
                                    </div>
                                    
                                    <div className="space-y-3">
                                        <div className="flex justify-between">
                                            <span className="text-sm text-muted-foreground">Account Type:</span>
                                            {getAccountTypeBadge(selectedAccountData.account_type)}
                                        </div>
                                        <div className="flex justify-between">
                                            <span className="text-sm text-muted-foreground">Current Balance:</span>
                                            <span className="font-medium">{formatCurrency(selectedAccountData.balance)}</span>
                                        </div>
                                        {selectedAccountData.member && (
                                            <div className="flex justify-between">
                                                <span className="text-sm text-muted-foreground">Member:</span>
                                                <span className="font-medium">{selectedAccountData.member.name}</span>
                                            </div>
                                        )}
                                    </div>

                                    {transactionType === 'withdrawal' && parseFloat(amount) > 0 && (
                                        <Alert>
                                            <AlertDescription>
                                                <strong>New Balance:</strong> {formatCurrency(selectedAccountData.balance - parseFloat(amount))}
                                                {selectedAccountData.balance - parseFloat(amount) < 0 && (
                                                    <div className="mt-1">
                                                        <strong>Warning:</strong> This will result in a negative balance!
                                                    </div>
                                                )}
                                            </AlertDescription>
                                        </Alert>
                                    )}

                                    {transactionType === 'deposit' && parseFloat(amount) > 0 && (
                                        <Alert>
                                            <AlertDescription>
                                                <strong>New Balance:</strong> {formatCurrency(selectedAccountData.balance + parseFloat(amount))}
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
