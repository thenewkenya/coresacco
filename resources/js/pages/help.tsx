import { Head } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Building2, Search, BookOpen, MessageCircle, ArrowRight } from 'lucide-react';

export default function Help() {
    return (
        <>
            <Head title="Help Center - CoreSacco" />
            
            <div className="min-h-screen bg-background">
                {/* Navigation */}
                <nav className="border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
                    <div className="container mx-auto px-4 h-16 flex items-center justify-between">
                        <div className="flex items-center space-x-2">
                            <Building2 className="h-8 w-8 text-primary" />
                            <span className="text-xl font-bold">CoreSacco</span>
                        </div>
                        <div className="flex items-center space-x-4">
                            <Button variant="ghost" asChild>
                                <a href="/">Back to Home</a>
                            </Button>
                        </div>
                    </div>
                </nav>

                {/* Hero Section */}
                <section className="py-20 px-4">
                    <div className="container mx-auto text-center max-w-4xl">
                        <h1 className="text-4xl md:text-6xl font-bold tracking-tight mb-6">
                            How can we
                            <span className="text-primary block">help you?</span>
                        </h1>
                        <p className="text-xl text-muted-foreground mb-8 max-w-2xl mx-auto">
                            Find answers to common questions and get the support you need.
                        </p>
                        <div className="relative max-w-md mx-auto">
                            <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                            <input
                                type="text"
                                placeholder="Search help articles..."
                                className="w-full pl-10 pr-4 py-2 border border-input rounded-md bg-background"
                            />
                        </div>
                    </div>
                </section>

                {/* Help Categories */}
                <section className="py-20 px-4">
                    <div className="container mx-auto max-w-6xl">
                        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                            <Card>
                                <CardHeader>
                                    <BookOpen className="h-10 w-10 text-primary mb-2" />
                                    <CardTitle>Getting Started</CardTitle>
                                    <CardDescription>
                                        Learn the basics of using CoreSacco for your SACCO.
                                    </CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <ul className="space-y-2 text-sm">
                                        <li>• Setting up your SACCO</li>
                                        <li>• Adding members</li>
                                        <li>• Creating accounts</li>
                                        <li>• First transaction</li>
                                    </ul>
                                </CardContent>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <Building2 className="h-10 w-10 text-primary mb-2" />
                                    <CardTitle>Member Management</CardTitle>
                                    <CardDescription>
                                        Everything about managing your SACCO members.
                                    </CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <ul className="space-y-2 text-sm">
                                        <li>• Member registration</li>
                                        <li>• Profile management</li>
                                        <li>• Account types</li>
                                        <li>• Member reports</li>
                                    </ul>
                                </CardContent>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <MessageCircle className="h-10 w-10 text-primary mb-2" />
                                    <CardTitle>Transactions</CardTitle>
                                    <CardDescription>
                                        Processing payments and managing transactions.
                                    </CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <ul className="space-y-2 text-sm">
                                        <li>• Creating transactions</li>
                                        <li>• M-Pesa integration</li>
                                        <li>• Transaction approval</li>
                                        <li>• Receipts and reports</li>
                                    </ul>
                                </CardContent>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <BookOpen className="h-10 w-10 text-primary mb-2" />
                                    <CardTitle>Loans</CardTitle>
                                    <CardDescription>
                                        Managing loan applications and disbursements.
                                    </CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <ul className="space-y-2 text-sm">
                                        <li>• Loan applications</li>
                                        <li>• Approval process</li>
                                        <li>• Loan disbursement</li>
                                        <li>• Repayment tracking</li>
                                    </ul>
                                </CardContent>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <Building2 className="h-10 w-10 text-primary mb-2" />
                                    <CardTitle>Reports</CardTitle>
                                    <CardDescription>
                                        Generating and understanding reports.
                                    </CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <ul className="space-y-2 text-sm">
                                        <li>• Financial reports</li>
                                        <li>• Member reports</li>
                                        <li>• Transaction reports</li>
                                        <li>• Exporting data</li>
                                    </ul>
                                </CardContent>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <MessageCircle className="h-10 w-10 text-primary mb-2" />
                                    <CardTitle>Support</CardTitle>
                                    <CardDescription>
                                        Get help when you need it.
                                    </CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <ul className="space-y-2 text-sm">
                                        <li>• Contact support</li>
                                        <li>• Bug reporting</li>
                                        <li>• Feature requests</li>
                                        <li>• Beta feedback</li>
                                    </ul>
                                </CardContent>
                            </Card>
                        </div>
                    </div>
                </section>

                {/* Contact Support */}
                <section className="py-20 px-4 bg-muted/50">
                    <div className="container mx-auto max-w-4xl text-center">
                        <h2 className="text-3xl md:text-4xl font-bold mb-6">
                            Still need help?
                        </h2>
                        <p className="text-xl text-muted-foreground mb-8 max-w-2xl mx-auto">
                            Our support team is here to help you get the most out of CoreSacco.
                        </p>
                        <div className="flex flex-col sm:flex-row gap-4 justify-center">
                            <Button size="lg" asChild>
                                <a href="/contact">
                                    Contact Support
                                    <ArrowRight className="ml-2 h-4 w-4" />
                                </a>
                            </Button>
                            <Button size="lg" variant="outline" asChild>
                                <a href="/demo">Schedule Demo</a>
                            </Button>
                        </div>
                    </div>
                </section>
            </div>
        </>
    );
}
