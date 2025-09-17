import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { CheckCircle, DollarSign, Calendar, CreditCard } from 'lucide-react';

interface PaymentSuccessDialogProps {
    isOpen: boolean;
    onClose: () => void;
    paymentData: {
        amount: number;
        paymentMethod: string;
        loanType: string;
        accountNumber: string;
        outstandingBalance: number;
        nextPaymentDate: string;
    } | null;
}

export default function PaymentSuccessDialog({ isOpen, onClose, paymentData }: PaymentSuccessDialogProps) {
    const formatCurrency = (amount: number) => {
        // Handle NaN, null, or undefined values
        const safeAmount = isNaN(amount) || amount === null || amount === undefined ? 0 : amount;
        return new Intl.NumberFormat('en-KE', {
            style: 'currency',
            currency: 'KES',
        }).format(safeAmount);
    };

    const formatDate = (date: string) => {
        return new Date(date).toLocaleDateString('en-KE', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        });
    };

    const getPaymentMethodLabel = (method: string) => {
        switch (method) {
            case 'mpesa':
                return 'M-Pesa';
            case 'bank_transfer':
                return 'Bank Transfer';
            case 'cash':
                return 'Cash';
            case 'cheque':
                return 'Cheque';
            default:
                return method;
        }
    };

    if (!paymentData) return null;

    return (
        <Dialog open={isOpen} onOpenChange={onClose}>
            <DialogContent className="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle className="flex items-center space-x-2 text-green-600">
                        <CheckCircle className="h-5 w-5" />
                        <span>Payment Successful!</span>
                    </DialogTitle>
                    <DialogDescription>
                        Your loan payment has been processed successfully.
                    </DialogDescription>
                </DialogHeader>

                <div className="space-y-4">
                    {/* Payment Summary */}
                    <Card>
                        <CardHeader className="pb-3">
                            <CardTitle className="flex items-center space-x-2 text-sm">
                                <DollarSign className="h-4 w-4" />
                                <span>Payment Details</span>
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-3">
                            <div className="flex justify-between items-center">
                                <span className="text-sm text-muted-foreground">Amount Paid:</span>
                                <Badge variant="default">
                                    {formatCurrency(paymentData.amount)}
                                </Badge>
                            </div>
                            <div className="flex justify-between items-center">
                                <span className="text-sm text-muted-foreground">Payment Method:</span>
                                <span className="text-sm font-medium">
                                    {getPaymentMethodLabel(paymentData.paymentMethod)}
                                </span>
                            </div>
                            <div className="flex justify-between items-center">
                                <span className="text-sm text-muted-foreground">Loan Type:</span>
                                <span className="text-sm font-medium">
                                    {paymentData.loanType}
                                </span>
                            </div>
                            <div className="flex justify-between items-center">
                                <span className="text-sm text-muted-foreground">Account:</span>
                                <span className="text-sm font-medium font-mono">
                                    {paymentData.accountNumber}
                                </span>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Outstanding Balance */}
                    <Card>
                        <CardHeader className="pb-3">
                            <CardTitle className="flex items-center space-x-2 text-sm">
                                <CreditCard className="h-4 w-4" />
                                <span>Remaining Balance</span>
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-3">
                            <div className="flex justify-between items-center">
                                <span className="text-sm text-muted-foreground">Outstanding:</span>
                                <Badge variant="outline">
                                    {formatCurrency(paymentData.outstandingBalance)}
                                </Badge>
                            </div>
                            <div className="flex justify-between items-center">
                                <span className="text-sm text-muted-foreground">Next Payment:</span>
                                <span className="text-sm font-medium">
                                    {formatDate(paymentData.nextPaymentDate)}
                                </span>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Action Buttons */}
                    <div className="flex gap-2 pt-4">
                        <Button onClick={onClose} className="flex-1">
                            Close
                        </Button>
                        <Button 
                            variant="outline" 
                            onClick={() => {
                                // You can add functionality to view loan details or generate receipt
                                onClose();
                            }}
                            className="flex-1"
                        >
                            View Loan Details
                        </Button>
                    </div>
                </div>
            </DialogContent>
        </Dialog>
    );
}
