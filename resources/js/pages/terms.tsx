import { Head } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Building2 } from 'lucide-react';

export default function Terms() {
    return (
        <>
            <Head title="Terms of Service - CoreSacco" />
            
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

                {/* Content */}
                <section className="py-20 px-4">
                    <div className="container mx-auto max-w-4xl">
                        <h1 className="text-4xl font-bold mb-8">Terms of Service</h1>
                        <p className="text-muted-foreground mb-8">Last updated: January 2025</p>
                        
                        <div className="prose prose-lg max-w-none">
                            <h2>1. Acceptance of Terms</h2>
                            <p>
                                By accessing and using CoreSacco, you accept and agree to be bound by the terms and 
                                provision of this agreement.
                            </p>

                            <h2>2. Beta Program</h2>
                            <p>
                                CoreSacco is currently in beta. By participating in our beta program, you understand 
                                that the service may contain bugs, errors, and other issues. We appreciate your 
                                feedback and patience as we improve the platform.
                            </p>

                            <h2>3. Use License</h2>
                            <p>
                                Permission is granted to temporarily use CoreSacco for personal, non-commercial 
                                transitory viewing only. This is the grant of a license, not a transfer of title.
                            </p>

                            <h2>4. User Responsibilities</h2>
                            <p>
                                You are responsible for maintaining the confidentiality of your account and password. 
                                You agree to accept responsibility for all activities that occur under your account.
                            </p>

                            <h2>5. Prohibited Uses</h2>
                            <p>
                                You may not use our service for any unlawful purpose or to solicit others to perform 
                                unlawful acts. You may not violate any international, federal, provincial, or state 
                                regulations, rules, laws, or local ordinances.
                            </p>

                            <h2>6. Service Availability</h2>
                            <p>
                                We strive to maintain high service availability, but we do not guarantee uninterrupted 
                                access to our services. We may experience downtime for maintenance, updates, or other reasons.
                            </p>

                            <h2>7. Limitation of Liability</h2>
                            <p>
                                In no event shall CoreSacco, nor its directors, employees, partners, agents, suppliers, 
                                or affiliates, be liable for any indirect, incidental, special, consequential, or 
                                punitive damages.
                            </p>

                            <h2>8. Contact Information</h2>
                            <p>
                                If you have any questions about these Terms of Service, please contact us at 
                                legal@coresacco.com.
                            </p>
                        </div>
                    </div>
                </section>
            </div>
        </>
    );
}
