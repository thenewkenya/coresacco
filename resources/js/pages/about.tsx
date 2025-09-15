import { Head } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Building2, Users, Target, ArrowRight } from 'lucide-react';

export default function About() {
    return (
        <>
            <Head title="About Us - CoreSacco" />
            
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
                            About
                            <span className="text-primary block">CoreSacco</span>
                        </h1>
                        <p className="text-xl text-muted-foreground mb-8 max-w-2xl mx-auto">
                            We're building the future of SACCO management, one digital innovation at a time.
                        </p>
                    </div>
                </section>

                {/* Mission Section */}
                <section className="py-20 px-4">
                    <div className="container mx-auto max-w-6xl">
                        <div className="grid lg:grid-cols-2 gap-12 items-center">
                            <div>
                                <h2 className="text-3xl md:text-4xl font-bold mb-6">
                                    Our Mission
                                </h2>
                                <p className="text-lg text-muted-foreground mb-6">
                                    To revolutionize SACCO management through modern technology, making it easier 
                                    for cooperatives to serve their members and grow their impact in communities 
                                    across East Africa and beyond.
                                </p>
                                <p className="text-lg text-muted-foreground mb-8">
                                    We believe that every SACCO deserves access to world-class digital tools 
                                    that can help them operate more efficiently, serve their members better, 
                                    and make a greater impact in their communities.
                                </p>
                                <Button size="lg" asChild>
                                    <a href="/register">
                                        Join Our Mission
                                        <ArrowRight className="ml-2 h-4 w-4" />
                                    </a>
                                </Button>
                            </div>
                            <div className="relative">
                                <div className="bg-gradient-to-br from-primary/20 to-primary/5 rounded-2xl p-8">
                                    <div className="grid grid-cols-2 gap-6">
                                        <div className="bg-background/80 backdrop-blur rounded-lg p-4 text-center">
                                            <Users className="h-8 w-8 text-blue-500 mx-auto mb-2" />
                                            <div className="text-2xl font-bold">Beta</div>
                                            <div className="text-sm text-muted-foreground">Program Active</div>
                                        </div>
                                        <div className="bg-background/80 backdrop-blur rounded-lg p-4 text-center">
                                            <Target className="h-8 w-8 text-green-500 mx-auto mb-2" />
                                            <div className="text-2xl font-bold">0</div>
                                            <div className="text-sm text-muted-foreground">SACCOs Served</div>
                                        </div>
                                        <div className="bg-background/80 backdrop-blur rounded-lg p-4 text-center">
                                            <Building2 className="h-8 w-8 text-purple-500 mx-auto mb-2" />
                                            <div className="text-2xl font-bold">1</div>
                                            <div className="text-sm text-muted-foreground">Country</div>
                                        </div>
                                        <div className="bg-background/80 backdrop-blur rounded-lg p-4 text-center">
                                            <Target className="h-8 w-8 text-orange-500 mx-auto mb-2" />
                                            <div className="text-2xl font-bold">Beta</div>
                                            <div className="text-sm text-muted-foreground">Support</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {/* Values Section */}
                <section className="py-20 px-4 bg-muted/50">
                    <div className="container mx-auto max-w-6xl">
                        <div className="text-center mb-16">
                            <h2 className="text-3xl md:text-4xl font-bold mb-4">
                                Our Values
                            </h2>
                            <p className="text-xl text-muted-foreground max-w-2xl mx-auto">
                                The principles that guide everything we do.
                            </p>
                        </div>

                        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                            <Card>
                                <CardHeader>
                                    <Users className="h-10 w-10 text-primary mb-2" />
                                    <CardTitle>Member-Centric</CardTitle>
                                    <CardDescription>
                                        Every feature we build is designed with SACCO members in mind, 
                                        making their financial journey simpler and more accessible.
                                    </CardDescription>
                                </CardHeader>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <Building2 className="h-10 w-10 text-primary mb-2" />
                                    <CardTitle>Innovation</CardTitle>
                                    <CardDescription>
                                        We embrace cutting-edge technology to solve real-world problems 
                                        in the cooperative sector.
                                    </CardDescription>
                                </CardHeader>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <Target className="h-10 w-10 text-primary mb-2" />
                                    <CardTitle>Transparency</CardTitle>
                                    <CardDescription>
                                        We believe in open communication, honest pricing, and building 
                                        trust through transparency.
                                    </CardDescription>
                                </CardHeader>
                            </Card>
                        </div>
                    </div>
                </section>


                {/* CTA Section */}
                <section className="py-20 px-4 bg-primary text-primary-foreground">
                    <div className="container mx-auto text-center">
                        <h2 className="text-3xl md:text-4xl font-bold mb-4">
                            Ready to Join Our Journey?
                        </h2>
                        <p className="text-xl mb-8 max-w-2xl mx-auto opacity-90">
                            Be part of the future of SACCO management. Join our beta program and help us 
                            build the perfect solution for your cooperative.
                        </p>
                        <div className="flex flex-col sm:flex-row gap-4 justify-center">
                            <Button size="lg" variant="secondary" asChild>
                                <a href="/register">
                                    Join Beta Program
                                    <ArrowRight className="ml-2 h-4 w-4" />
                                </a>
                            </Button>
                            <Button size="lg" variant="outline" className="border-primary-foreground text-primary-foreground hover:bg-primary-foreground hover:text-primary" asChild>
                                <a href="/contact">Contact Us</a>
                            </Button>
                        </div>
                    </div>
                </section>
            </div>
        </>
    );
}
