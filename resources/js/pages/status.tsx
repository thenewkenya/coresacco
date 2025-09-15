import { Head } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Building2, CheckCircle, AlertCircle, Clock, ArrowRight } from 'lucide-react';

export default function Status() {
    return (
        <>
            <Head title="System Status - eSacco" />
            
            <div className="min-h-screen bg-background">
                {/* Navigation */}
                <nav className="border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
                    <div className="container mx-auto px-4 h-16 flex items-center justify-between">
                        <div className="flex items-center space-x-2">
                            <Building2 className="h-8 w-8 text-primary" />
                            <span className="text-xl font-bold">eSacco</span>
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
                            System
                            <span className="text-primary block">Status</span>
                        </h1>
                        <p className="text-xl text-muted-foreground mb-8 max-w-2xl mx-auto">
                            Real-time status monitoring coming soon.
                        </p>
                    </div>
                </section>

                {/* Coming Soon Section */}
                <section className="py-20 px-4">
                    <div className="container mx-auto max-w-4xl text-center">
                        <div className="bg-gradient-to-br from-primary/20 to-primary/5 rounded-2xl p-12">
                            <Clock className="h-16 w-16 text-primary mx-auto mb-6" />
                            <h2 className="text-3xl md:text-4xl font-bold mb-4">
                                Status Monitoring Coming Soon
                            </h2>
                            <p className="text-xl text-muted-foreground mb-8 max-w-2xl mx-auto">
                                We're working on a comprehensive system status page that will provide 
                                real-time monitoring of all eSacco services and infrastructure.
                            </p>
                            <div className="grid md:grid-cols-3 gap-6 mb-8">
                                <div className="bg-background/80 backdrop-blur rounded-lg p-4">
                                    <CheckCircle className="h-8 w-8 text-green-500 mx-auto mb-2" />
                                    <div className="font-semibold">Real-time Status</div>
                                    <div className="text-sm text-muted-foreground">Live service monitoring</div>
                                </div>
                                <div className="bg-background/80 backdrop-blur rounded-lg p-4">
                                    <AlertCircle className="h-8 w-8 text-orange-500 mx-auto mb-2" />
                                    <div className="font-semibold">Incident Tracking</div>
                                    <div className="text-sm text-muted-foreground">Issue history and updates</div>
                                </div>
                                <div className="bg-background/80 backdrop-blur rounded-lg p-4">
                                    <Clock className="h-8 w-8 text-blue-500 mx-auto mb-2" />
                                    <div className="font-semibold">Uptime Monitoring</div>
                                    <div className="text-sm text-muted-foreground">Service availability tracking</div>
                                </div>
                            </div>
                            <Button size="lg" asChild>
                                <a href="/contact">
                                    Get Notified When Available
                                    <ArrowRight className="ml-2 h-4 w-4" />
                                </a>
                            </Button>
                        </div>
                    </div>
                </section>

                {/* Contact Section */}
                <section className="py-20 px-4">
                    <div className="container mx-auto max-w-4xl text-center">
                        <h2 className="text-3xl font-bold mb-6">
                            Report an Issue
                        </h2>
                        <p className="text-xl text-muted-foreground mb-8 max-w-2xl mx-auto">
                            If you're experiencing issues not reflected here, please let us know.
                        </p>
                        <Button size="lg" asChild>
                            <a href="/contact">
                                Report Issue
                                <ArrowRight className="ml-2 h-4 w-4" />
                            </a>
                        </Button>
                    </div>
                </section>
            </div>
        </>
    );
}
