import React, { useState } from 'react';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import { Label } from '@/components/ui/label';
import { AlertTriangle, UserX, Clock, CreditCard, DollarSign } from 'lucide-react';
import { Alert, AlertDescription } from '@/components/ui/alert';

interface MemberDeleteDialogProps {
    isOpen: boolean;
    onClose: () => void;
    onConfirm: (reason: string) => void;
    member: {
        id: number;
        name: string;
        email: string;
        member_number: string;
        membership_status: string;
        accounts?: Array<{
            id: number;
            account_number: string;
            account_type: string;
            balance: number;
        }>;
        loans?: Array<{
            id: number;
            amount: number;
            status: string;
        }>;
    };
    isLoading?: boolean;
}

export default function MemberDeleteDialog({
    isOpen,
    onClose,
    onConfirm,
    member,
    isLoading = false,
}: MemberDeleteDialogProps) {
    const [reason, setReason] = useState('');
    const [isConfirmed, setIsConfirmed] = useState(false);

    const handleConfirm = () => {
        if (reason.trim()) {
            onConfirm(reason.trim());
        }
    };

    const handleClose = () => {
        setReason('');
        setIsConfirmed(false);
        onClose();
    };

    // Calculate total balances and active loans
    const totalBalance = Math.round((member.accounts?.reduce((sum, account) => {
        const balance = parseFloat(account.balance.toString()) || 0;
        return sum + balance;
    }, 0) || 0));
    const activeLoans = member.loans?.filter(loan => ['active', 'disbursed'].includes(loan.status)) || [];
    const totalLoanAmount = Math.round(activeLoans.reduce((sum, loan) => {
        const amount = parseFloat(loan.amount.toString()) || 0;
        return sum + amount;
    }, 0));

    const canDelete = totalBalance === 0 && activeLoans.length === 0;
    const hasActiveDebts = totalBalance > 0 || activeLoans.length > 0;

    return (
        <Dialog open={isOpen} onOpenChange={handleClose}>
            <DialogContent className="sm:max-w-[600px]">
                <DialogHeader>
                    <DialogTitle className="flex items-center gap-2">
                        <UserX className="h-5 w-5 text-destructive" />
                        Delete Member
                    </DialogTitle>
                    <DialogDescription>
                        This action will permanently suspend the member account and schedule it for deletion.
                    </DialogDescription>
                </DialogHeader>

                <div className="space-y-4">
                    {/* Member Information */}
                    <div className="bg-muted p-4 rounded-lg">
                        <h4 className="font-medium mb-2">Member Details</h4>
                        <div className="space-y-1 text-sm">
                            <p><span className="font-medium">Name:</span> {member.name}</p>
                            <p><span className="font-medium">Email:</span> {member.email}</p>
                            <p><span className="font-medium">Member Number:</span> {member.member_number}</p>
                            <p><span className="font-medium">Status:</span> {member.membership_status}</p>
                        </div>
                    </div>

                    {/* Financial Summary */}
                    <div className="bg-muted p-4 rounded-lg">
                        <h4 className="font-medium mb-2">Financial Summary</h4>
                        <div className="space-y-1 text-sm">
                            <p className="flex items-center gap-2">
                                <CreditCard className="h-4 w-4" />
                                <span className="font-medium">Total Account Balance:</span> KSh {totalBalance.toLocaleString()}
                            </p>
                            <p className="flex items-center gap-2">
                                <DollarSign className="h-4 w-4" />
                                <span className="font-medium">Active Loans:</span> {activeLoans.length} (KSh {totalLoanAmount.toLocaleString()})
                            </p>
                        </div>
                    </div>

                    {/* Warning Messages */}
                    {hasActiveDebts && (
                        <Alert variant="destructive">
                            <AlertTriangle className="h-4 w-4" />
                            <AlertDescription>
                                <strong>Cannot delete member with active accounts or loans.</strong>
                                <br />
                                This member has:
                                {totalBalance > 0 && (
                                    <><br />• Account balance: KSh {totalBalance.toLocaleString()}</>
                                )}
                                {activeLoans.length > 0 && (
                                    <><br />• {activeLoans.length} active loan(s) totaling KSh {totalLoanAmount.toLocaleString()}</>
                                )}
                                <br />
                                Please ensure all accounts are closed and loans are settled before proceeding.
                            </AlertDescription>
                        </Alert>
                    )}

                    {canDelete && (
                        <Alert>
                            <Clock className="h-4 w-4" />
                            <AlertDescription>
                                <strong>Member Suspension Process:</strong>
                                <br />
                                • Member account will be immediately suspended
                                <br />
                                • Scheduled for deletion in 3 months
                                <br />
                                • If member logs in during this period, deletion timer resets
                                <br />
                                • Account will be permanently deleted after 3 months of inactivity
                            </AlertDescription>
                        </Alert>
                    )}

                    {/* Reason Input */}
                    <div className="space-y-2">
                        <Label htmlFor="reason">
                            Reason for Member Deletion <span className="text-destructive">*</span>
                        </Label>
                        <Textarea
                            id="reason"
                            placeholder="Please provide a reason for deleting this member..."
                            value={reason}
                            onChange={(e) => setReason(e.target.value)}
                            rows={3}
                        />
                    </div>

                    {/* Confirmation Checkbox */}
                    <div className="flex items-center space-x-2">
                        <input
                            type="checkbox"
                            id="confirm"
                            checked={isConfirmed}
                            onChange={(e) => setIsConfirmed(e.target.checked)}
                            className="rounded border-gray-300"
                        />
                        <Label htmlFor="confirm" className="text-sm">
                            I understand that this action will suspend the member account and schedule it for permanent deletion.
                        </Label>
                    </div>
                </div>

                <DialogFooter>
                    <Button variant="outline" onClick={handleClose} disabled={isLoading}>
                        Cancel
                    </Button>
                    <Button
                        variant="destructive"
                        onClick={handleConfirm}
                        disabled={!canDelete || !reason.trim() || !isConfirmed || isLoading}
                    >
                        {isLoading ? 'Processing...' : 'Suspend & Schedule for Deletion'}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
