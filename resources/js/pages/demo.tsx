import { Head } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Building2, Calendar, Clock, ArrowRight } from 'lucide-react';

export default function Demo() {
    return (
        <>
            <Head title="Schedule Demo - CoreSacco" />
            
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
                            See CoreSacco
                            <span className="text-primary block">In Action</span>
                        </h1>
                        <p className="text-xl text-muted-foreground mb-8 max-w-2xl mx-auto">
                            Schedule a personalized demo and discover how CoreSacco can transform your SACCO operations.
                        </p>
                    </div>
                </section>

                {/* Demo Section */}
                <section className="py-20 px-4">
                    <div className="container mx-auto max-w-4xl">
                        <div className="grid lg:grid-cols-2 gap-12 items-center">
                            <div>
                                <h2 className="text-3xl md:text-4xl font-bold mb-6">
                                    Book Your Demo
                                </h2>
                                <p className="text-lg text-muted-foreground mb-6">
                                    During this 30-minute session, we'll show you:
                                </p>
                                <ul className="space-y-4 mb-8">
                                    <li className="flex items-start">
                                        <div className="w-2 h-2 bg-primary rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                        <span>Complete member management system</span>
                                    </li>
                                    <li className="flex items-start">
                                        <div className="w-2 h-2 bg-primary rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                        <span>Loan application and approval workflow</span>
                                    </li>
                                    <li className="flex items-start">
                                        <div className="w-2 h-2 bg-primary rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                        <span>Real-time transaction processing</span>
                                    </li>
                                    <li className="flex items-start">
                                        <div className="w-2 h-2 bg-primary rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                        <span>Comprehensive reporting and analytics</span>
                                    </li>
                                    <li className="flex items-start">
                                        <div className="w-2 h-2 bg-primary rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                        <span>Mobile money integration (M-Pesa)</span>
                                    </li>
                                </ul>
                                <Button size="lg" asChild>
                                    <a href="/contact">
                                        Schedule Demo
                                        <ArrowRight className="ml-2 h-4 w-4" />
                                    </a>
                                </Button>
                            </div>
                            <div className="relative">
                                <div className="bg-gradient-to-br from-primary/20 to-primary/5 rounded-2xl p-8">
                                    <div className="space-y-6">
                                        <div className="bg-background/80 backdrop-blur rounded-lg p-4 text-center">
                                            <Calendar className="h-8 w-8 text-blue-500 mx-auto mb-2" />
                                            <div className="text-2xl font-bold">30 Minutes</div>
                                            <div className="text-sm text-muted-foreground">Personalized Demo</div>
                                        </div>
                                        <div className="bg-background/80 backdrop-blur rounded-lg p-4 text-center">
                                            <Clock className="h-8 w-8 text-green-500 mx-auto mb-2" />
                                            <div className="text-2xl font-bold">Flexible</div>
                                            <div className="text-sm text-muted-foreground">Scheduling</div>
                                        </div>
                                        <div className="bg-background/80 backdrop-blur rounded-lg p-4 text-center">
                                            <Building2 className="h-8 w-8 text-purple-500 mx-auto mb-2" />
                                            <div className="text-2xl font-bold">Live</div>
                                            <div className="text-sm text-muted-foreground">Q&A Session</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {/* Beta Program Section */}
                <section className="py-20 px-4 bg-muted/50">
                    <div className="container mx-auto max-w-4xl text-center">
                        <h2 className="text-3xl md:text-4xl font-bold mb-6">
                            Join Our Beta Program
                        </h2>
                        <p className="text-xl text-muted-foreground mb-8 max-w-2xl mx-auto">
                            Ready to go beyond a demo? Join our beta program and get hands-on experience 
                            with CoreSacco while helping us improve the platform.
                        </p>
                        <div className="grid md:grid-cols-2 gap-8 mb-8">
                            <Card>
                                <CardHeader>
                                    <CardTitle>What You Get</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <ul className="space-y-2 text-left">
                                        <li>• Full access to the platform</li>
                                        <li>• Direct feedback channel</li>
                                        <li>• Priority support</li>
                                        <li>• Influence on new features</li>
                                        <li>• Free during beta period</li>
                                    </ul>
                                </CardContent>
                            </Card>
                            <Card>
                                <CardHeader>
                                    <CardTitle>What We Ask</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <ul className="space-y-2 text-left">
                                        <li>• Regular feedback on features</li>
                                        <li>• Bug reports and suggestions</li>
                                        <li>• Monthly check-in calls</li>
                                        <li>• Test new features early</li>
                                        <li>• Share your success stories</li>
                                    </ul>
                                </CardContent>
                            </Card>
                        </div>
                        <Button size="lg" asChild>
                            <a href="/register">
                                Join Beta Program
                                <ArrowRight className="ml-2 h-4 w-4" />
                            </a>
                        </Button>
                    </div>
                </section>
            </div>
        </>
    );
}
