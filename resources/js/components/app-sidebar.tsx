import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { 
    LayoutGrid, 
    Users, 
    CreditCard, 
    ArrowLeftRight, 
    DollarSign, 
    PiggyBank, 
    FileText, 
    Building2, 
    Bell,
    Settings,
    HelpCircle
} from 'lucide-react';
import AppLogo from './app-logo';

// Define all possible navigation items with their required roles
const allMainNavItems: (NavItem & { roles?: string[] })[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
        roles: ['admin', 'staff', 'manager', 'member'], // Everyone can see dashboard
    },
    {
        title: 'Members',
        href: '/members',
        icon: Users,
        roles: ['admin', 'staff', 'manager'], // Only admin/staff/manager can manage members
    },
    {
        title: 'Accounts',
        href: '/accounts',
        icon: CreditCard,
        roles: ['admin', 'staff', 'manager', 'member'], // Members can view their own accounts
    },
    {
        title: 'Transactions',
        href: '/transactions',
        icon: ArrowLeftRight,
        roles: ['admin', 'staff', 'manager', 'member'], // Members can view their transactions
    },
    {
        title: 'Loans',
        href: '/loans',
        icon: DollarSign,
        roles: ['admin', 'staff', 'manager', 'member'], // Members can apply for loans
    },
    {
        title: 'Savings',
        href: '/savings',
        icon: PiggyBank,
        roles: ['admin', 'staff', 'manager', 'member'], // Members can manage savings
    },
    {
        title: 'Reports',
        href: '/reports',
        icon: FileText,
        roles: ['admin', 'staff', 'manager'], // Only admin/staff/manager can view reports
    },
    {
        title: 'Branches',
        href: '/branches',
        icon: Building2,
        roles: ['admin', 'staff', 'manager'], // Only admin/staff/manager can manage branches
    },
];

const allFooterNavItems: (NavItem & { roles?: string[] })[] = [
    {
        title: 'Help & Support',
        href: '/help',
        icon: HelpCircle,
        roles: ['admin', 'staff', 'manager', 'member'], // Everyone can access help
    },
];

// Helper function to check if user has required role
const hasRole = (userRoles: string[], requiredRoles: string[]): boolean => {
    return requiredRoles.some(role => userRoles.includes(role));
};

// Helper function to filter navigation items based on user roles
const filterNavItems = (items: (NavItem & { roles?: string[] })[], userRoles: string[]): NavItem[] => {
    return items
        .filter(item => !item.roles || hasRole(userRoles, item.roles))
        .map(item => ({
            title: item.title,
            href: item.href,
            icon: item.icon,
        }));
};

export function AppSidebar() {
    const { auth } = usePage().props as { auth: { user: any } };
    const user = auth.user || {};
    
    // Get user roles - handle both role field and roles relationship
    const userRoles = user ? [
        ...(user.roles?.map((role: any) => role.slug) || []),
        ...(user.role ? [user.role] : [])
    ].filter((role, index, array) => array.indexOf(role) === index) : []; // Remove duplicates
    
    // Filter navigation items based on user roles
    const mainNavItems = filterNavItems(allMainNavItems, userRoles);
    const footerNavItems = filterNavItems(allFooterNavItems, userRoles);

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboard()} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
