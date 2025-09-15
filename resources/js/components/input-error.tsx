import { cn } from '@/lib/utils';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { type HTMLAttributes } from 'react';

export default function InputError({ message, className = '', ...props }: HTMLAttributes<HTMLDivElement> & { message?: string }) {
    return message ? (
        <Alert variant="destructive" className={cn("mt-1", className)} {...props}>
            <AlertDescription className="text-sm">
                {message}
            </AlertDescription>
        </Alert>
    ) : null;
}
