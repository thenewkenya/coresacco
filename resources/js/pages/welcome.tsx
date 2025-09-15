import { Head } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import AppearanceToggleDropdown from '@/components/appearance-dropdown';
import { 
    ArrowRight, 
    Shield, 
    Users, 
    TrendingUp, 
    Smartphone, 
    Clock, 
    CheckCircle,
    Star,
    Building2,
    PiggyBank,
    CreditCard,
    BarChart3,
    Globe,
    Zap
} from 'lucide-react';

export default function Welcome() {
    return (
        <>
            <Head title="eSacco - Digital SACCO Management System" />
            
            <div className="min-h-screen bg-background">
                {/* Navigation */}
                <nav className="border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
                    <div className="container mx-auto px-4 h-16 flex items-center justify-between">
                        <div className="flex items-center space-x-2">
                            <Building2 className="h-8 w-8 text-primary" />
                            <span className="text-xl font-bold">eSacco</span>
                        </div>
                               <div className="flex items-center space-x-4">
                                   <AppearanceToggleDropdown />
                                   <Button variant="ghost" asChild>
                                       <a href="/login">Sign In</a>
                                   </Button>
                                   <Button asChild>
                                       <a href="/register">Get Started</a>
                                   </Button>
                               </div>
                    </div>
                </nav>

                {/* Hero Section */}
                <section className="py-20 px-4">
                    <div className="container mx-auto text-center max-w-4xl">
                        <Badge variant="secondary" className="mb-4">
                            <Zap className="w-3 h-3 mr-1" />
                            Beta - Modern SACCO Management
                        </Badge>
                        <h1 className="text-4xl md:text-6xl font-bold tracking-tight mb-6">
                            Digital SACCO Management
                            <span className="text-primary block">Made Simple</span>
                        </h1>
                        <p className="text-xl text-muted-foreground mb-8 max-w-2xl mx-auto">
                            Experience the future of SACCO management with our innovative digital platform. 
                            Currently in beta - help us build the perfect solution for your cooperative.
                        </p>
                        <div className="flex flex-col sm:flex-row gap-4 justify-center">
                            <Button size="lg" asChild>
                                <a href="/register">
                                    Join Beta Program
                                    <ArrowRight className="ml-2 h-4 w-4" />
                                </a>
                            </Button>
                            <Button variant="outline" size="lg" asChild>
                                <a href="#features">Learn More</a>
                            </Button>
                        </div>
                    </div>
                </section>

                {/* Features Grid */}
                <section id="features" className="py-20 px-4 bg-muted/50">
                    <div className="container mx-auto">
                        <div className="text-center mb-16">
                            <h2 className="text-3xl md:text-4xl font-bold mb-4">
                                Everything You Need to Run Your SACCO
                            </h2>
                            <p className="text-xl text-muted-foreground max-w-2xl mx-auto">
                                Comprehensive tools designed specifically for SACCO operations and member management.
                            </p>
                        </div>

                        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                            <Card>
                                <CardHeader>
                                    <Users className="h-10 w-10 text-primary mb-2" />
                                    <CardTitle>Member Management</CardTitle>
                                    <CardDescription>
                                        Complete member lifecycle management with digital onboarding, 
                                        profile management, and membership tracking.
                                    </CardDescription>
                                </CardHeader>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <PiggyBank className="h-10 w-10 text-primary mb-2" />
                                    <CardTitle>Savings & Accounts</CardTitle>
                                    <CardDescription>
                                        Multiple account types including savings, shares, deposits, 
                                        emergency funds, and retirement accounts.
                                    </CardDescription>
                                </CardHeader>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <CreditCard className="h-10 w-10 text-primary mb-2" />
                                    <CardTitle>Loan Management</CardTitle>
                                    <CardDescription>
                                        End-to-end loan processing from application to disbursement 
                                        with automated calculations and tracking.
                                    </CardDescription>
                                </CardHeader>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <BarChart3 className="h-10 w-10 text-primary mb-2" />
                                    <CardTitle>Financial Reports</CardTitle>
                                    <CardDescription>
                                        Comprehensive reporting and analytics with real-time 
                                        insights into SACCO performance and member activities.
                                    </CardDescription>
                                </CardHeader>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <Smartphone className="h-10 w-10 text-primary mb-2" />
                                    <CardTitle>Mobile Payments</CardTitle>
                                    <CardDescription>
                                        Integrated M-Pesa payments for seamless transactions 
                                        with real-time status updates and notifications.
                                    </CardDescription>
                                </CardHeader>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <Shield className="h-10 w-10 text-primary mb-2" />
                                    <CardTitle>Security & Compliance</CardTitle>
                                    <CardDescription>
                                        Bank-level security with role-based access control, 
                                        audit trails, and regulatory compliance features.
                                    </CardDescription>
                                </CardHeader>
                            </Card>
                        </div>
                    </div>
                </section>

                {/* Benefits Section */}
                <section className="py-20 px-4">
                    <div className="container mx-auto">
                        <div className="grid lg:grid-cols-2 gap-12 items-center">
                            <div>
                                <h2 className="text-3xl md:text-4xl font-bold mb-6">
                                    Why Choose eSacco?
                                </h2>
                                <div className="space-y-6">
                                    <div className="flex items-start space-x-4">
                                        <CheckCircle className="h-6 w-6 text-green-500 mt-1 flex-shrink-0" />
                                        <div>
                                            <h3 className="font-semibold mb-2">Modern Technology</h3>
                                            <p className="text-muted-foreground">
                                                Built with the latest web technologies for speed, 
                                                reliability, and user experience.
                                            </p>
                                        </div>
                                    </div>
                                    <div className="flex items-start space-x-4">
                                        <CheckCircle className="h-6 w-6 text-green-500 mt-1 flex-shrink-0" />
                                        <div>
                                            <h3 className="font-semibold mb-2">Easy to Use</h3>
                                            <p className="text-muted-foreground">
                                                Intuitive interface designed for both staff and 
                                                members with minimal training required.
                                            </p>
                                        </div>
                                    </div>
                                    <div className="flex items-start space-x-4">
                                        <CheckCircle className="h-6 w-6 text-green-500 mt-1 flex-shrink-0" />
                                        <div>
                                            <h3 className="font-semibold mb-2">Scalable Solution</h3>
                                            <p className="text-muted-foreground">
                                                Grows with your SACCO from small cooperatives 
                                                to large financial institutions.
                                            </p>
                                        </div>
                                    </div>
                                    <div className="flex items-start space-x-4">
                                        <CheckCircle className="h-6 w-6 text-green-500 mt-1 flex-shrink-0" />
                                        <div>
                                            <h3 className="font-semibold mb-2">24/7 Support</h3>
                                            <p className="text-muted-foreground">
                                                Dedicated support team to help you succeed 
                                                with implementation and ongoing operations.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div className="relative">
                                <div className="bg-gradient-to-br from-primary/20 to-primary/5 rounded-2xl p-8">
                                    <div className="grid grid-cols-2 gap-4">
                                        <div className="bg-background/80 backdrop-blur rounded-lg p-4 text-center">
                                            <TrendingUp className="h-8 w-8 text-green-500 mx-auto mb-2" />
                                            <div className="text-2xl font-bold">Modern</div>
                                            <div className="text-sm text-muted-foreground">Technology Stack</div>
                                        </div>
                                        <div className="bg-background/80 backdrop-blur rounded-lg p-4 text-center">
                                            <Clock className="h-8 w-8 text-blue-500 mx-auto mb-2" />
                                            <div className="text-2xl font-bold">24/7</div>
                                            <div className="text-sm text-muted-foreground">Access</div>
                                        </div>
                                        <div className="bg-background/80 backdrop-blur rounded-lg p-4 text-center">
                                            <Globe className="h-8 w-8 text-purple-500 mx-auto mb-2" />
                                            <div className="text-2xl font-bold">Cloud</div>
                                            <div className="text-sm text-muted-foreground">Based</div>
                                        </div>
                                        <div className="bg-background/80 backdrop-blur rounded-lg p-4 text-center">
                                            <Star className="h-8 w-8 text-yellow-500 mx-auto mb-2" />
                                            <div className="text-2xl font-bold">Beta</div>
                                            <div className="text-sm text-muted-foreground">Early Access</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {/* CTA Section */}
                <section className="py-20 px-4 bg-primary text-primary-foreground">
                    <div className="container mx-auto text-center">
                        <h2 className="text-3xl md:text-4xl font-bold mb-4">
                            Ready to Transform Your SACCO?
                        </h2>
                        <p className="text-xl mb-8 max-w-2xl mx-auto opacity-90">
                            Be among the first to experience the future of SACCO management. 
                            Join our beta program and help shape the next generation of digital cooperatives.
                        </p>
                        <div className="flex flex-col sm:flex-row gap-4 justify-center">
                            <Button size="lg" variant="secondary" asChild>
                                <a href="/register">
                                    Join Beta Program
                                    <ArrowRight className="ml-2 h-4 w-4" />
                                </a>
                            </Button>
                            <Button size="lg" variant="outline" className="border-primary-foreground text-primary-foreground hover:bg-primary-foreground hover:text-primary bg-white/10 backdrop-blur" asChild>
                                <a href="/contact">Get Early Access</a>
                            </Button>
                        </div>
                    </div>
                </section>

                {/* Footer */}
                <footer className="border-t bg-background">
                    <div className="container mx-auto px-4 py-12">
                        <div className="grid md:grid-cols-4 gap-8">
                            <div>
                                <div className="flex items-center space-x-2 mb-4">
                                    <Building2 className="h-6 w-6 text-primary" />
                                    <span className="text-lg font-bold">eSacco</span>
                                </div>
                                <p className="text-muted-foreground">
                                    Modern SACCO management platform designed for the digital age.
                                </p>
                            </div>
                            <div>
                                <h3 className="font-semibold mb-4">Product</h3>
                                <ul className="space-y-2 text-muted-foreground">
                                    <li><a href="#features" className="hover:text-foreground">Features</a></li>
                                    <li><a href="/pricing" className="hover:text-foreground">Pricing</a></li>
                                    <li><a href="/demo" className="hover:text-foreground">Demo</a></li>
                                </ul>
                            </div>
                            <div>
                                <h3 className="font-semibold mb-4">Support</h3>
                                <ul className="space-y-2 text-muted-foreground">
                                    <li><a href="/help" className="hover:text-foreground">Help Center</a></li>
                                    <li><a href="/contact" className="hover:text-foreground">Contact Us</a></li>
                                    <li><a href="/status" className="hover:text-foreground">System Status</a></li>
                                </ul>
                            </div>
                            <div>
                                <h3 className="font-semibold mb-4">Company</h3>
                                <ul className="space-y-2 text-muted-foreground">
                                    <li><a href="/about" className="hover:text-foreground">About</a></li>
                                    <li><a href="/privacy" className="hover:text-foreground">Privacy</a></li>
                                    <li><a href="/terms" className="hover:text-foreground">Terms</a></li>
                                </ul>
                            </div>
                        </div>
                        <div className="border-t mt-8 pt-8 text-center text-muted-foreground">
                            <p>&copy; 2025 eSacco. All rights reserved.</p>
                        </div>
                    </div>
                </footer>
            </div>
        </>
    );
}