import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { FileText, Download, Calendar, BarChart3, CreditCard, Users } from 'lucide-react';
import { type BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Reports',
        href: '#',
    },
];

export default function ReportsIndex() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Reports" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">Reports</h1>
                        <p className="text-muted-foreground">
                            Generate and view SACCO reports and analytics
                        </p>
                    </div>
                    <Button>
                        <Download className="mr-2 h-4 w-4" />
                        Export Report
                    </Button>
                </div>

                {/* Report Categories */}
                <div className="grid auto-rows-min gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <Link href="/reports/financial">
                        <Card className="cursor-pointer hover:shadow-md transition-shadow">
                            <CardHeader>
                                <CardTitle className="flex items-center">
                                    <BarChart3 className="mr-2 h-5 w-5" />
                                    Financial Reports
                                </CardTitle>
                                <CardDescription>
                                    Income statements, balance sheets, and cash flow reports
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <Button variant="outline" className="w-full">
                                    View Reports
                                </Button>
                            </CardContent>
                        </Card>
                    </Link>

                    <Link href="/reports/members">
                        <Card className="cursor-pointer hover:shadow-md transition-shadow">
                            <CardHeader>
                                <CardTitle className="flex items-center">
                                    <Users className="mr-2 h-5 w-5" />
                                    Member Reports
                                </CardTitle>
                                <CardDescription>
                                    Member statistics, growth, and activity reports
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <Button variant="outline" className="w-full">
                                    View Reports
                                </Button>
                            </CardContent>
                        </Card>
                    </Link>

                    <Link href="/reports/loans">
                        <Card className="cursor-pointer hover:shadow-md transition-shadow">
                            <CardHeader>
                                <CardTitle className="flex items-center">
                                    <CreditCard className="mr-2 h-5 w-5" />
                                    Loan Reports
                                </CardTitle>
                                <CardDescription>
                                    Loan performance, defaults, and portfolio analysis
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <Button variant="outline" className="w-full">
                                    View Reports
                                </Button>
                            </CardContent>
                        </Card>
                    </Link>
                </div>

                {/* Recent Reports */}
                <Card>
                    <CardHeader>
                        <CardTitle>Recent Reports</CardTitle>
                        <CardDescription>
                            Recently generated reports
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="text-center py-8 text-muted-foreground">
                            <FileText className="mx-auto h-12 w-12 mb-4" />
                            <p>Recent reports will be listed here</p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}

