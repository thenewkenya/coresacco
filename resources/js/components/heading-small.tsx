import { cn } from '@/lib/utils';

interface HeadingSmallProps {
    title: string;
    description?: string;
    className?: string;
}

export default function HeadingSmall({ title, description, className }: HeadingSmallProps) {
    return (
        <header className={cn("space-y-1", className)}>
            <h3 className="text-lg font-semibold leading-none tracking-tight">{title}</h3>
            {description && <p className="text-sm text-muted-foreground">{description}</p>}
        </header>
    );
}
