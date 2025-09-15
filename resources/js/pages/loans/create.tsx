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
    DollarSign,
    Calendar,
    User,
    CreditCard,
    CheckCircle,
    AlertCircle,
    Info
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
import { index as loansIndex, store as loansStore } from '@/routes/loans';

interface LoanType {
    id: number;
    name: string;
    interest_rate: number;
    minimum_amount: number;
    maximum_amount: number;
    term_options: string;
    description: string;
    processing_fee: number;
}

interface Member {
    id: number;
    name: string;
    member_number: string;
    accounts?: any[];
}

interface Props {
    loanTypes: LoanType[];
    members: Member[];
}

export default function CreateLoan({ loanTypes, members }: Props) {
    const { auth } = usePage().props as { auth: { user: any } };
    const user = auth.user || {};
    
    const [selectedMember, setSelectedMember] = useState(user.role === 'member' ? user.id?.toString() || '' : '');
    const [selectedLoanType, setSelectedLoanType] = useState('');
    const [amount, setAmount] = useState('');
    const [termPeriod, setTermPeriod] = useState('');
    const [collateralDetails, setCollateralDetails] = useState('');
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [errors, setErrors] = useState<any>({});

    const selectedLoanTypeData = loanTypes.find(type => type.id.toString() === selectedLoanType);
    const selectedMemberData = members.find(member => member.id.toString() === selectedMember);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        
        if (!selectedMember) {
            setErrors({ member_id: 'Please select a member' });
            return;
        }

        if (!selectedLoanType) {
            setErrors({ loan_type_id: 'Please select a loan type' });
            return;
        }

        if (!amount || parseFloat(amount) < 1000) {
            setErrors({ amount: 'Please enter a valid amount (minimum KSh 1,000)' });
            return;
        }

        if (!termPeriod || parseInt(termPeriod) < 1 || parseInt(termPeriod) > 60) {
            setErrors({ term_period: 'Please enter a valid term period (1-60 months)' });
            return;
        }

        setIsSubmitting(true);
        setErrors({});
        
        const formData = {
            member_id: selectedMember,
            loan_type_id: selectedLoanType,
            amount: parseFloat(amount),
            term_period: parseInt(termPeriod),
            collateral_details: collateralDetails.trim() || undefined,
        };

        router.post(loansStore.url(), formData, {
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

    const calculateMonthlyPayment = (principal: number, rate: number, months: number) => {
        if (rate === 0) return principal / months;
        const monthlyRate = rate / 100 / 12;
        return principal * (monthlyRate * Math.pow(1 + monthlyRate, months)) / (Math.pow(1 + monthlyRate, months) - 1);
    };

    const monthlyPayment = selectedLoanTypeData && amount && termPeriod 
        ? calculateMonthlyPayment(parseFloat(amount), selectedLoanTypeData.interest_rate, parseInt(termPeriod))
        : 0;

    const totalPayment = monthlyPayment * (parseInt(termPeriod) || 0);
    const totalInterest = totalPayment - (parseFloat(amount) || 0);

    return (
        <AppLayout>
            <Head title="Apply for Loan" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div className="flex items-center space-x-4">
                        <Link href={loansIndex.url()}>
                            <Button variant="outline" size="sm">
                                <ArrowLeft className="mr-2 h-4 w-4" />
                                Back to Loans
                            </Button>
                        </Link>
                        <div>
                            <h1 className="text-3xl font-bold tracking-tight">Apply for Loan</h1>
                            <p className="text-muted-foreground">
                                Submit a new loan application
                            </p>
                        </div>
                    </div>
                </div>

                <div className="grid gap-6 lg:grid-cols-3">
                    {/* Loan Application Form */}
                    <div className="lg:col-span-2">
                        <Card>
                            <CardHeader>
                                <CardTitle>Loan Application Details</CardTitle>
                                <CardDescription>
                                    Fill in the details for your loan application
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <form onSubmit={handleSubmit} className="space-y-4">
                                    {/* Member Selection (for staff only) */}
                                    {user.role !== 'member' && (
                                        <div className="space-y-2">
                                            <Label htmlFor="member">Select Member</Label>
                                            <Select value={selectedMember} onValueChange={setSelectedMember}>
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
                                            {errors.member_id && (
                                                <p className="text-sm text-red-600">{errors.member_id}</p>
                                            )}
                                        </div>
                                    )}

                                    {/* Loan Type Selection */}
                                    <div className="space-y-2">
                                        <Label htmlFor="loan_type">Loan Type</Label>
                                        <Select value={selectedLoanType} onValueChange={setSelectedLoanType}>
                                            <SelectTrigger>
                                                <SelectValue placeholder="Select loan type" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                {loanTypes.map((type) => (
                                                    <SelectItem key={type.id} value={type.id.toString()}>
                                                        <div className="flex items-center space-x-2">
                                                            <span>{type.name}</span>
                                                            <span className="text-muted-foreground">
                                                                ({type.interest_rate}% interest)
                                                            </span>
                                                        </div>
                                                    </SelectItem>
                                                ))}
                                            </SelectContent>
                                        </Select>
                                        {errors.loan_type_id && (
                                            <p className="text-sm text-red-600">{errors.loan_type_id}</p>
                                        )}
                                    </div>

                                    {/* Loan Amount */}
                                    <div className="space-y-2">
                                        <Label htmlFor="amount">Loan Amount</Label>
                                        <Input
                                            id="amount"
                                            type="number"
                                            step="100"
                                            min="1000"
                                            placeholder="0.00"
                                            value={amount}
                                            onChange={(e) => setAmount(e.target.value)}
                                        />
                                        {selectedLoanTypeData && (
                                            <p className="text-xs text-muted-foreground">
                                                Range: {formatCurrency(selectedLoanTypeData.minimum_amount)} - {formatCurrency(selectedLoanTypeData.maximum_amount)}
                                            </p>
                                        )}
                                        {errors.amount && (
                                            <p className="text-sm text-red-600">{errors.amount}</p>
                                        )}
                                    </div>

                                    {/* Term Period */}
                                    <div className="space-y-2">
                                        <Label htmlFor="term_period">Repayment Period (Months)</Label>
                                        <Input
                                            id="term_period"
                                            type="number"
                                            min="1"
                                            max="60"
                                            placeholder="12"
                                            value={termPeriod}
                                            onChange={(e) => setTermPeriod(e.target.value)}
                                        />
                                        <p className="text-xs text-muted-foreground">
                                            Enter repayment period in months (1-60)
                                        </p>
                                        {errors.term_period && (
                                            <p className="text-sm text-red-600">{errors.term_period}</p>
                                        )}
                                    </div>

                                    {/* Collateral Details */}
                                    <div className="space-y-2">
                                        <Label htmlFor="collateral_details">Collateral Details (Optional)</Label>
                                        <Textarea
                                            id="collateral_details"
                                            placeholder="Describe any collateral or security for the loan..."
                                            value={collateralDetails}
                                            onChange={(e) => setCollateralDetails(e.target.value)}
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
                                        <Link href={loansIndex.url()}>
                                            <Button variant="outline" disabled={isSubmitting}>Cancel</Button>
                                        </Link>
                                        <Button 
                                            type="submit" 
                                            disabled={!selectedMember || !selectedLoanType || !amount || !termPeriod || isSubmitting}
                                        >
                                            {isSubmitting ? (
                                                <>
                                                    <Skeleton className="h-4 w-4 mr-2 bg-white/20" />
                                                    Submitting Application...
                                                </>
                                            ) : (
                                                <>
                                                    <CheckCircle className="mr-2 h-4 w-4" />
                                                    Submit Application
                                                </>
                                            )}
                                        </Button>
                                    </div>
                                </form>
                            </CardContent>
                        </Card>
                    </div>

                    {/* Loan Information Sidebar */}
                    <div className="lg:col-span-1 space-y-6">
                        {/* Loan Type Info */}
                        {selectedLoanTypeData && (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center space-x-2">
                                        <Info className="h-5 w-5" />
                                        <span>Loan Type Details</span>
                                    </CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div>
                                        <h4 className="font-semibold">{selectedLoanTypeData.name}</h4>
                                        <p className="text-sm text-muted-foreground mt-1">
                                            {selectedLoanTypeData.description}
                                        </p>
                                    </div>
                                    
                                    <div className="space-y-3">
                                        <div className="flex justify-between">
                                            <span className="text-sm text-muted-foreground">Interest Rate:</span>
                                            <span className="font-medium">{selectedLoanTypeData.interest_rate}%</span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span className="text-sm text-muted-foreground">Min Amount:</span>
                                            <span className="font-medium">{formatCurrency(selectedLoanTypeData.minimum_amount)}</span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span className="text-sm text-muted-foreground">Max Amount:</span>
                                            <span className="font-medium">{formatCurrency(selectedLoanTypeData.maximum_amount)}</span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span className="text-sm text-muted-foreground">Processing Fee:</span>
                                            <span className="font-medium">{formatCurrency(selectedLoanTypeData.processing_fee)}</span>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>
                        )}

                        {/* Loan Calculation */}
                        {selectedLoanTypeData && amount && termPeriod && (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center space-x-2">
                                        <DollarSign className="h-5 w-5" />
                                        <span>Loan Calculation</span>
                                    </CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="space-y-3">
                                        <div className="flex justify-between">
                                            <span className="text-sm text-muted-foreground">Loan Amount:</span>
                                            <span className="font-medium">{formatCurrency(parseFloat(amount))}</span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span className="text-sm text-muted-foreground">Interest Rate:</span>
                                            <span className="font-medium">{selectedLoanTypeData.interest_rate}%</span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span className="text-sm text-muted-foreground">Term Period:</span>
                                            <span className="font-medium">{termPeriod} months</span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span className="text-sm text-muted-foreground">Monthly Payment:</span>
                                            <span className="font-medium">{formatCurrency(monthlyPayment)}</span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span className="text-sm text-muted-foreground">Total Interest:</span>
                                            <span className="font-medium">{formatCurrency(totalInterest)}</span>
                                        </div>
                                        <div className="flex justify-between border-t pt-2">
                                            <span className="text-sm font-medium">Total Payment:</span>
                                            <span className="font-bold">{formatCurrency(totalPayment)}</span>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>
                        )}

                        {/* Member Info */}
                        {selectedMemberData && (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center space-x-2">
                                        <User className="h-5 w-5" />
                                        <span>Member Information</span>
                                    </CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div>
                                        <h4 className="font-semibold">{selectedMemberData.name}</h4>
                                        <p className="text-sm text-muted-foreground">Member #{selectedMemberData.member_number}</p>
                                    </div>
                                </CardContent>
                            </Card>
                        )}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}

