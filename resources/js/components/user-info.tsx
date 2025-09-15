import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { useInitials } from '@/hooks/use-initials';
import { type User } from '@/types';

export function UserInfo({ user, showEmail = false, notificationCount = 0 }: { user: User; showEmail?: boolean; notificationCount?: number }) {
    const getInitials = useInitials();

    return (
        <>
            <div className="relative">
                <Avatar className="h-8 w-8 overflow-hidden rounded-full">
                    <AvatarImage src={user?.avatar} alt={user?.name || 'User'} />
                    <AvatarFallback className="rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                        {getInitials(user?.name || 'User')}
                    </AvatarFallback>
                </Avatar>
                {notificationCount > 0 && (
                    <Badge 
                        variant="destructive" 
                        className="absolute -top-1 -right-1 h-5 w-5 rounded-full p-0 text-xs flex items-center justify-center"
                    >
                        {notificationCount > 99 ? '99+' : notificationCount}
                    </Badge>
                )}
            </div>
            <div className="grid flex-1 text-left text-sm leading-tight">
                <span className="truncate font-medium">{user?.name || 'User'}</span>
                {showEmail && <span className="truncate text-xs text-muted-foreground">{user?.email || ''}</span>}
            </div>
        </>
    );
}
