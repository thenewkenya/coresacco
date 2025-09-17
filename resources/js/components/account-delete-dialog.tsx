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
import { AlertTriangle, UserX, Clock } from 'lucide-react';
import { Alert, AlertDescription } from '@/components/ui/alert';

interface AccountDeleteDialogProps {
    isOpen: boolean;
    onClose: () => void;
    onConfirm: (reason: string) => void;
    account: {
        id: number;
        account_number: string;
        account_type: string;
        balance: number;
        member: {
            name: string;
            member_number: string;
        };
    };
    isLoading?: boolean;
}

export default function AccountDeleteDialog({
    isOpen,
    onClose,
    onConfirm,
    account,
    isLoading = false,
}: AccountDeleteDialogProps) {
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

    const canDelete = parseFloat(account.balance.toString()) === 0;
    const hasActiveDebts = parseFloat(account.balance.toString()) > 0;

    return (
        <Dialog open={isOpen} onOpenChange={handleClose}>
            <DialogContent className="sm:max-w-[500px]">
                <DialogHeader>
                    <DialogTitle className="flex items-center gap-2">
                        <UserX className="h-5 w-5 text-destructive" />
                        Delete Account
                    </DialogTitle>
                    <DialogDescription>
                        This action will permanently suspend the account and schedule it for deletion.
                    </DialogDescription>
                </DialogHeader>

                <div className="space-y-4">
                    {/* Account Information */}
                    <div className="bg-muted p-4 rounded-lg">
                        <h4 className="font-medium mb-2">Account Details</h4>
                        <div className="space-y-1 text-sm">
                            <p><span className="font-medium">Account:</span> {account.account_number}</p>
                            <p><span className="font-medium">Type:</span> {account.account_type}</p>
                            <p><span className="font-medium">Member:</span> {account.member.name} ({account.member.member_number})</p>
                            <p><span className="font-medium">Balance:</span> KSh {account.balance.toLocaleString()}</p>
                        </div>
                    </div>

                    {/* Warning Messages */}
                    {hasActiveDebts && (
                        <Alert variant="destructive">
                            <AlertTriangle className="h-4 w-4" />
                            <AlertDescription>
                                <strong>Cannot delete account with outstanding balance.</strong>
                                <br />
                                The account has a balance of KSh {account.balance.toLocaleString()}. 
                                Please ensure all funds are withdrawn before proceeding with account deletion.
                            </AlertDescription>
                        </Alert>
                    )}

                    {canDelete && (
                        <Alert>
                            <Clock className="h-4 w-4" />
                            <AlertDescription>
                                <strong>Account Suspension Process:</strong>
                                <br />
                                • Account will be immediately suspended
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
                            Reason for Account Deletion <span className="text-destructive">*</span>
                        </Label>
                        <Textarea
                            id="reason"
                            placeholder="Please provide a reason for deleting this account..."
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
                            I understand that this action will suspend the account and schedule it for permanent deletion.
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
