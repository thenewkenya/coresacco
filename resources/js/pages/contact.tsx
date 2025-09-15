import { Head } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Building2, Mail, Phone, MapPin, Send } from 'lucide-react';

export default function Contact() {
    return (
        <>
            <Head title="Contact Us - CoreSacco" />
            
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
                            Get in
                            <span className="text-primary block">Touch</span>
                        </h1>
                        <p className="text-xl text-muted-foreground mb-8 max-w-2xl mx-auto">
                            Have questions about CoreSacco? We'd love to hear from you. Send us a message and we'll respond as soon as possible.
                        </p>
                    </div>
                </section>

                {/* Contact Section */}
                <section className="py-20 px-4">
                    <div className="container mx-auto max-w-6xl">
                        <div className="grid lg:grid-cols-2 gap-12">
                            {/* Contact Form */}
                            <Card>
                                <CardHeader>
                                    <CardTitle>Send us a message</CardTitle>
                                    <CardDescription>
                                        Fill out the form below and we'll get back to you within 24 hours.
                                    </CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <form className="space-y-6">
                                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label htmlFor="firstName" className="text-sm font-medium mb-2 block">
                                                    First Name
                                                </label>
                                                <Input id="firstName" placeholder="John" />
                                            </div>
                                            <div>
                                                <label htmlFor="lastName" className="text-sm font-medium mb-2 block">
                                                    Last Name
                                                </label>
                                                <Input id="lastName" placeholder="Doe" />
                                            </div>
                                        </div>
                                        <div>
                                            <label htmlFor="email" className="text-sm font-medium mb-2 block">
                                                Email
                                            </label>
                                            <Input id="email" type="email" placeholder="john@example.com" />
                                        </div>
                                        <div>
                                            <label htmlFor="sacco" className="text-sm font-medium mb-2 block">
                                                SACCO Name
                                            </label>
                                            <Input id="sacco" placeholder="Your SACCO Name" />
                                        </div>
                                        <div>
                                            <label htmlFor="subject" className="text-sm font-medium mb-2 block">
                                                Subject
                                            </label>
                                            <Input id="subject" placeholder="How can we help?" />
                                        </div>
                                        <div>
                                            <label htmlFor="message" className="text-sm font-medium mb-2 block">
                                                Message
                                            </label>
                                            <Textarea 
                                                id="message" 
                                                placeholder="Tell us about your SACCO and how we can help..."
                                                className="min-h-[120px]"
                                            />
                                        </div>
                                        <Button className="w-full">
                                            <Send className="mr-2 h-4 w-4" />
                                            Send Message
                                        </Button>
                                    </form>
                                </CardContent>
                            </Card>

                            {/* Contact Information */}
                            <div className="space-y-8">
                                <Card>
                                    <CardHeader>
                                        <CardTitle>Contact Information</CardTitle>
                                        <CardDescription>
                                            Reach out to us through any of these channels.
                                        </CardDescription>
                                    </CardHeader>
                                    <CardContent className="space-y-4">
                                        <div className="flex items-center space-x-3">
                                            <Mail className="h-5 w-5 text-primary" />
                                            <div>
                                                <p className="font-medium">Email</p>
                                                <p className="text-sm text-muted-foreground">hello@coresacco.com</p>
                                            </div>
                                        </div>
                                        <div className="flex items-center space-x-3">
                                            <Phone className="h-5 w-5 text-primary" />
                                            <div>
                                                <p className="font-medium">Phone</p>
                                                <p className="text-sm text-muted-foreground">+254 700 000 000</p>
                                            </div>
                                        </div>
                                        <div className="flex items-center space-x-3">
                                            <MapPin className="h-5 w-5 text-primary" />
                                            <div>
                                                <p className="font-medium">Address</p>
                                                <p className="text-sm text-muted-foreground">
                                                    Nairobi, Kenya
                                                </p>
                                            </div>
                                        </div>
                                    </CardContent>
                                </Card>

                                <Card>
                                    <CardHeader>
                                        <CardTitle>Beta Program</CardTitle>
                                        <CardDescription>
                                            Join our beta program and help shape the future of SACCO management.
                                        </CardDescription>
                                    </CardHeader>
                                    <CardContent>
                                        <p className="text-sm text-muted-foreground mb-4">
                                            As a beta participant, you'll get early access to new features, 
                                            direct communication with our development team, and the opportunity 
                                            to influence the product roadmap.
                                        </p>
                                        <Button variant="outline" className="w-full" asChild>
                                            <a href="/register">Join Beta Program</a>
                                        </Button>
                                    </CardContent>
                                </Card>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </>
    );
}
