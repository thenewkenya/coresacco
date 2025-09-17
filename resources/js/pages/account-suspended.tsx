import AppLayout from '@/layouts/app-layout';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { AlertTriangle, Clock, Mail, Phone } from 'lucide-react';
import { Head, Link } from '@inertiajs/react';
import { Alert, AlertDescription } from '@/components/ui/alert';

export default function AccountSuspended() {
    return (
        <AppLayout>
            <Head title="Account Suspended" />
            
            <div className="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
                <div className="max-w-md w-full space-y-8">
                    <div className="text-center">
                        <AlertTriangle className="mx-auto h-16 w-16 text-red-500 mb-4" />
                        <h2 className="mt-6 text-3xl font-extrabold text-gray-900">
                            Account Suspended
                        </h2>
                        <p className="mt-2 text-sm text-gray-600">
                            Your account has been suspended and is scheduled for deletion.
                        </p>
                    </div>

                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <Clock className="h-5 w-5" />
                                Account Status
                            </CardTitle>
                            <CardDescription>
                                Your account is currently suspended
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <Alert variant="destructive">
                                <AlertTriangle className="h-4 w-4" />
                                <AlertDescription>
                                    <strong>Account Suspended</strong>
                                    <br />
                                    Your account has been suspended and is scheduled for permanent deletion in 3 months.
                                </AlertDescription>
                            </Alert>

                            <div className="space-y-3">
                                <h4 className="font-medium">What happens next?</h4>
                                <ul className="text-sm text-gray-600 space-y-2">
                                    <li>• Your account is currently suspended</li>
                                    <li>• It will be permanently deleted after 3 months of inactivity</li>
                                    <li>• If you log in during this period, the deletion timer resets</li>
                                    <li>• You cannot access your account until it's unsuspended</li>
                                </ul>
                            </div>

                            <div className="space-y-3">
                                <h4 className="font-medium">Need help?</h4>
                                <p className="text-sm text-gray-600">
                                    If you believe this suspension is in error or need assistance, 
                                    please contact our support team.
                                </p>
                                
                                <div className="space-y-2">
                                    <div className="flex items-center gap-2 text-sm">
                                        <Mail className="h-4 w-4" />
                                        <span>support@saccocore.co.ke</span>
                                    </div>
                                    <div className="flex items-center gap-2 text-sm">
                                        <Phone className="h-4 w-4" />
                                        <span>+254 20 2345 678</span>
                                    </div>
                                </div>
                            </div>

                            <div className="pt-4">
                                <Button asChild className="w-full">
                                    <Link href="/logout">
                                        Sign Out
                                    </Link>
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}
