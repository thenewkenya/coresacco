import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/react';
import { ComponentProps } from 'react';

type LinkProps = ComponentProps<typeof Link>;

export default function TextLink({ className = '', children, ...props }: LinkProps) {
    return (
        <Button asChild variant="link" className={className}>
            <Link {...props}>
                {children}
            </Link>
        </Button>
    );
}
