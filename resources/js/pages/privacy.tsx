import { Head } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Building2 } from 'lucide-react';

export default function Privacy() {
    return (
        <>
            <Head title="Privacy Policy - eSacco" />
            
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

                {/* Content */}
                <section className="py-20 px-4">
                    <div className="container mx-auto max-w-4xl">
                        <h1 className="text-4xl font-bold mb-8">Privacy Policy</h1>
                        <p className="text-muted-foreground mb-8">Last updated: January 2025</p>
                        
                        <div className="prose prose-lg max-w-none">
                            <h2>1. Information We Collect</h2>
                            <p>
                                We collect information you provide directly to us, such as when you create an account, 
                                use our services, or contact us for support. This may include your name, email address, 
                                phone number, and SACCO information.
                            </p>

                            <h2>2. How We Use Your Information</h2>
                            <p>
                                We use the information we collect to provide, maintain, and improve our services, 
                                process transactions, communicate with you, and ensure the security of our platform.
                            </p>

                            <h2>3. Information Sharing</h2>
                            <p>
                                We do not sell, trade, or otherwise transfer your personal information to third parties 
                                without your consent, except as described in this privacy policy or as required by law.
                            </p>

                            <h2>4. Data Security</h2>
                            <p>
                                We implement appropriate security measures to protect your personal information against 
                                unauthorized access, alteration, disclosure, or destruction.
                            </p>

                            <h2>5. Your Rights</h2>
                            <p>
                                You have the right to access, update, or delete your personal information. You may also 
                                opt out of certain communications from us.
                            </p>

                            <h2>6. Contact Us</h2>
                            <p>
                                If you have any questions about this privacy policy, please contact us at 
                                privacy@esacco.com.
                            </p>
                        </div>
                    </div>
                </section>
            </div>
        </>
    );
}
