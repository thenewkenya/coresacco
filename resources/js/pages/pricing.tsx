import { Head } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Check, Building2, ArrowRight } from 'lucide-react';

export default function Pricing() {
    return (
        <>
            <Head title="Pricing - CoreSacco" />
            
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
                        <Badge variant="secondary" className="mb-4">
                            Beta Pricing
                        </Badge>
                        <h1 className="text-4xl md:text-6xl font-bold tracking-tight mb-6">
                            Simple, Transparent
                            <span className="text-primary block">Pricing</span>
                        </h1>
                        <p className="text-xl text-muted-foreground mb-8 max-w-2xl mx-auto">
                            During our beta period, we're offering special pricing to help us build the perfect SACCO management solution.
                        </p>
                    </div>
                </section>

                {/* Pricing Cards */}
                <section className="py-20 px-4">
                    <div className="container mx-auto max-w-6xl">
                        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                            {/* Beta Plan */}
                            <Card className="relative">
                                <CardHeader>
                                    <div className="flex items-center justify-between">
                                        <CardTitle>Beta Access</CardTitle>
                                        <Badge variant="secondary">Free</Badge>
                                    </div>
                                    <CardDescription>
                                        Perfect for early adopters and feedback providers
                                    </CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <div className="text-3xl font-bold mb-4">KES 0<span className="text-lg text-muted-foreground">/month</span></div>
                                    <ul className="space-y-3 mb-6">
                                        <li className="flex items-center">
                                            <Check className="h-4 w-4 text-green-500 mr-2" />
                                            Full platform access
                                        </li>
                                        <li className="flex items-center">
                                            <Check className="h-4 w-4 text-green-500 mr-2" />
                                            Up to 100 members
                                        </li>
                                        <li className="flex items-center">
                                            <Check className="h-4 w-4 text-green-500 mr-2" />
                                            Basic reporting
                                        </li>
                                        <li className="flex items-center">
                                            <Check className="h-4 w-4 text-green-500 mr-2" />
                                            Email support
                                        </li>
                                        <li className="flex items-center">
                                            <Check className="h-4 w-4 text-green-500 mr-2" />
                                            Direct feedback channel
                                        </li>
                                    </ul>
                                    <Button className="w-full" asChild>
                                        <a href="/register">Join Beta Program</a>
                                    </Button>
                                </CardContent>
                            </Card>

                            {/* Future Standard Plan */}
                            <Card>
                                <CardHeader>
                                    <div className="flex items-center justify-between">
                                        <CardTitle>Standard</CardTitle>
                                        <Badge variant="outline">Coming Soon</Badge>
                                    </div>
                                    <CardDescription>
                                        For established SACCOs ready to scale
                                    </CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <div className="text-3xl font-bold mb-4">KES 15,000<span className="text-lg text-muted-foreground">/month</span></div>
                                    <ul className="space-y-3 mb-6">
                                        <li className="flex items-center">
                                            <Check className="h-4 w-4 text-green-500 mr-2" />
                                            Up to 1,000 members
                                        </li>
                                        <li className="flex items-center">
                                            <Check className="h-4 w-4 text-green-500 mr-2" />
                                            Advanced reporting
                                        </li>
                                        <li className="flex items-center">
                                            <Check className="h-4 w-4 text-green-500 mr-2" />
                                            Priority support
                                        </li>
                                        <li className="flex items-center">
                                            <Check className="h-4 w-4 text-green-500 mr-2" />
                                            Custom integrations
                                        </li>
                                        <li className="flex items-center">
                                            <Check className="h-4 w-4 text-green-500 mr-2" />
                                            Training sessions
                                        </li>
                                    </ul>
                                    <Button variant="outline" className="w-full" disabled>
                                        Coming Soon
                                    </Button>
                                </CardContent>
                            </Card>

                            {/* Future Enterprise Plan */}
                            <Card>
                                <CardHeader>
                                    <div className="flex items-center justify-between">
                                        <CardTitle>Enterprise</CardTitle>
                                        <Badge variant="outline">Coming Soon</Badge>
                                    </div>
                                    <CardDescription>
                                        For large cooperatives with complex needs
                                    </CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <div className="text-3xl font-bold mb-4">Custom<span className="text-lg text-muted-foreground"> pricing</span></div>
                                    <ul className="space-y-3 mb-6">
                                        <li className="flex items-center">
                                            <Check className="h-4 w-4 text-green-500 mr-2" />
                                            Unlimited members
                                        </li>
                                        <li className="flex items-center">
                                            <Check className="h-4 w-4 text-green-500 mr-2" />
                                            Custom features
                                        </li>
                                        <li className="flex items-center">
                                            <Check className="h-4 w-4 text-green-500 mr-2" />
                                            24/7 dedicated support
                                        </li>
                                        <li className="flex items-center">
                                            <Check className="h-4 w-4 text-green-500 mr-2" />
                                            On-premise deployment
                                        </li>
                                        <li className="flex items-center">
                                            <Check className="h-4 w-4 text-green-500 mr-2" />
                                            White-label options
                                        </li>
                                    </ul>
                                    <Button variant="outline" className="w-full" disabled>
                                        Contact Sales
                                    </Button>
                                </CardContent>
                            </Card>
                        </div>
                    </div>
                </section>

                {/* CTA Section */}
                <section className="py-20 px-4 bg-muted/50">
                    <div className="container mx-auto text-center">
                        <h2 className="text-3xl md:text-4xl font-bold mb-4">
                            Questions About Pricing?
                        </h2>
                        <p className="text-xl mb-8 max-w-2xl mx-auto text-muted-foreground">
                            We're here to help you understand our pricing and find the right plan for your SACCO.
                        </p>
                        <div className="flex flex-col sm:flex-row gap-4 justify-center">
                            <Button size="lg" asChild>
                                <a href="/contact">
                                    Contact Us
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
