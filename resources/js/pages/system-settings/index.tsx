import { Head, useForm } from '@inertiajs/react';
import { useState } from 'react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Switch } from '@/components/ui/switch';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { 
    Save, 
    Settings, 
    Building2, 
    DollarSign, 
    CreditCard, 
    Users, 
    Shield, 
    Bell, 
    Database, 
    Plug, 
    Monitor,
    AlertCircle,
    CheckCircle
} from 'lucide-react';
import { type BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';

interface SystemSettings {
    general: {
        sacco_name: string;
        sacco_email: string;
        sacco_phone: string;
        sacco_address: string;
        timezone: string;
        currency: string;
        currency_symbol: string;
        date_format: string;
        time_format: string;
        language: string;
    };
    financial: {
        minimum_deposit: number;
        maximum_deposit: number;
        minimum_withdrawal: number;
        maximum_withdrawal: number;
        transaction_fee: number;
        monthly_maintenance_fee: number;
        interest_rate: number;
        penalty_rate: number;
        loan_processing_fee: number;
        loan_insurance_rate: number;
    };
    loans: {
        minimum_loan_amount: number;
        maximum_loan_amount: number;
        minimum_loan_period: number;
        maximum_loan_period: number;
        loan_interest_rate: number;
        loan_penalty_rate: number;
        loan_processing_fee_percentage: number;
        loan_insurance_required: boolean;
        loan_guarantor_required: boolean;
        loan_collateral_required: boolean;
        loan_approval_levels: number;
        loan_auto_approval_limit: number;
    };
    members: {
        minimum_age: number;
        maximum_age: number;
        minimum_share_capital: number;
        share_capital_increment: number;
        membership_fee: number;
        annual_subscription_fee: number;
        member_photo_required: boolean;
        member_id_required: boolean;
        member_address_required: boolean;
        member_employment_required: boolean;
    };
    security: {
        password_min_length: number;
        password_require_uppercase: boolean;
        password_require_lowercase: boolean;
        password_require_numbers: boolean;
        password_require_symbols: boolean;
        session_timeout: number;
        max_login_attempts: number;
        lockout_duration: number;
        two_factor_auth: boolean;
        ip_whitelist: string;
    };
    notifications: {
        email_notifications: boolean;
        sms_notifications: boolean;
        push_notifications: boolean;
        loan_reminder_days: number;
        payment_reminder_days: number;
        overdue_notification_days: number;
        monthly_statement_email: boolean;
        transaction_alerts: boolean;
        system_maintenance_notifications: boolean;
    };
    backup: {
        auto_backup: boolean;
        backup_frequency: string;
        backup_retention_days: number;
        backup_location: string;
        backup_encryption: boolean;
        backup_notification_email: string;
    };
    integrations: {
        mobile_money: {
            enabled: boolean;
            provider: string;
            api_key: string;
            api_secret: string;
            callback_url: string;
        };
        banking: {
            enabled: boolean;
            provider: string;
            api_endpoint: string;
            api_key: string;
            api_secret: string;
        };
        sms: {
            enabled: boolean;
            provider: string;
            api_key: string;
            username: string;
        };
        email: {
            enabled: boolean;
            provider: string;
            host: string;
            port: number;
            username: string;
            password: string;
            encryption: string;
        };
    };
    system: {
        maintenance_mode: boolean;
        debug_mode: boolean;
        log_level: string;
        cache_driver: string;
        queue_driver: string;
        session_driver: string;
        file_upload_max_size: string;
        image_upload_max_size: string;
        allowed_file_types: string;
        auto_logout_minutes: number;
    };
}

interface Props {
    settings: SystemSettings;
}

export default function SystemSettingsIndex({ settings }: Props) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: dashboard().url,
        },
        {
            title: 'System Settings',
            href: '#',
        },
    ];

    const { data, setData, put, processing, errors } = useForm(settings);
    const [activeTab, setActiveTab] = useState('general');

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put('/system-settings');
    };

    const formatCurrency = (amount: number) => {
        return `KSh ${amount.toLocaleString()}`;
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="System Settings" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">System Settings</h1>
                        <p className="text-muted-foreground">
                            Configure all aspects of your CoreSacco system
                        </p>
                    </div>
                    <div className="flex items-center space-x-2">
                        <Button 
                            onClick={handleSubmit} 
                            disabled={processing}
                            className="bg-primary hover:bg-primary/90"
                        >
                            <Save className="mr-2 h-4 w-4" />
                            {processing ? 'Saving...' : 'Save Settings'}
                        </Button>
                    </div>
                </div>

                {/* Success/Error Messages */}
                {errors && Object.keys(errors).length > 0 && (
                    <Alert variant="destructive">
                        <AlertCircle className="h-4 w-4" />
                        <AlertDescription>
                            Please fix the errors below before saving.
                        </AlertDescription>
                    </Alert>
                )}

                {/* Settings Tabs */}
                <Tabs value={activeTab} onValueChange={setActiveTab} className="w-full">
                    <TabsList className="grid w-full grid-cols-8">
                        <TabsTrigger value="general" className="flex items-center gap-2">
                            <Building2 className="h-4 w-4" />
                            General
                        </TabsTrigger>
                        <TabsTrigger value="financial" className="flex items-center gap-2">
                            <DollarSign className="h-4 w-4" />
                            Financial
                        </TabsTrigger>
                        <TabsTrigger value="loans" className="flex items-center gap-2">
                            <CreditCard className="h-4 w-4" />
                            Loans
                        </TabsTrigger>
                        <TabsTrigger value="members" className="flex items-center gap-2">
                            <Users className="h-4 w-4" />
                            Members
                        </TabsTrigger>
                        <TabsTrigger value="security" className="flex items-center gap-2">
                            <Shield className="h-4 w-4" />
                            Security
                        </TabsTrigger>
                        <TabsTrigger value="notifications" className="flex items-center gap-2">
                            <Bell className="h-4 w-4" />
                            Notifications
                        </TabsTrigger>
                        <TabsTrigger value="backup" className="flex items-center gap-2">
                            <Database className="h-4 w-4" />
                            Backup
                        </TabsTrigger>
                        <TabsTrigger value="integrations" className="flex items-center gap-2">
                            <Plug className="h-4 w-4" />
                            Integrations
                        </TabsTrigger>
                    </TabsList>

                    {/* General Settings */}
                    <TabsContent value="general" className="space-y-4">
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <Building2 className="h-5 w-5" />
                                    General Settings
                                </CardTitle>
                                <CardDescription>
                                    Basic information and configuration for your SACCO
                                </CardDescription>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div className="space-y-2">
                                        <Label htmlFor="sacco_name">SACCO Name</Label>
                                        <Input
                                            id="sacco_name"
                                            value={data.general.sacco_name}
                                            onChange={(e) => setData('general', { ...data.general, sacco_name: e.target.value })}
                                            placeholder="Enter SACCO name"
                                        />
                                        {errors['general.sacco_name'] && (
                                            <p className="text-sm text-red-600">{errors['general.sacco_name']}</p>
                                        )}
                                    </div>
                                    <div className="space-y-2">
                                        <Label htmlFor="sacco_email">SACCO Email</Label>
                                        <Input
                                            id="sacco_email"
                                            type="email"
                                            value={data.general.sacco_email}
                                            onChange={(e) => setData('general', { ...data.general, sacco_email: e.target.value })}
                                            placeholder="Enter SACCO email"
                                        />
                                        {errors['general.sacco_email'] && (
                                            <p className="text-sm text-red-600">{errors['general.sacco_email']}</p>
                                        )}
                                    </div>
                                    <div className="space-y-2">
                                        <Label htmlFor="sacco_phone">SACCO Phone</Label>
                                        <Input
                                            id="sacco_phone"
                                            value={data.general.sacco_phone}
                                            onChange={(e) => setData('general', { ...data.general, sacco_phone: e.target.value })}
                                            placeholder="Enter SACCO phone"
                                        />
                                        {errors['general.sacco_phone'] && (
                                            <p className="text-sm text-red-600">{errors['general.sacco_phone']}</p>
                                        )}
                                    </div>
                                    <div className="space-y-2">
                                        <Label htmlFor="timezone">Timezone</Label>
                                        <Select
                                            value={data.general.timezone}
                                            onValueChange={(value) => setData('general', { ...data.general, timezone: value })}
                                        >
                                            <SelectTrigger>
                                                <SelectValue placeholder="Select timezone" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="Africa/Nairobi">Africa/Nairobi</SelectItem>
                                                <SelectItem value="Africa/Kampala">Africa/Kampala</SelectItem>
                                                <SelectItem value="Africa/Dar_es_Salaam">Africa/Dar es Salaam</SelectItem>
                                                <SelectItem value="UTC">UTC</SelectItem>
                                            </SelectContent>
                                        </Select>
                                        {errors['general.timezone'] && (
                                            <p className="text-sm text-red-600">{errors['general.timezone']}</p>
                                        )}
                                    </div>
                                    <div className="space-y-2">
                                        <Label htmlFor="currency">Currency</Label>
                                        <Select
                                            value={data.general.currency}
                                            onValueChange={(value) => setData('general', { ...data.general, currency: value })}
                                        >
                                            <SelectTrigger>
                                                <SelectValue placeholder="Select currency" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="KES">Kenyan Shilling (KES)</SelectItem>
                                                <SelectItem value="UGX">Ugandan Shilling (UGX)</SelectItem>
                                                <SelectItem value="TZS">Tanzanian Shilling (TZS)</SelectItem>
                                                <SelectItem value="USD">US Dollar (USD)</SelectItem>
                                            </SelectContent>
                                        </Select>
                                        {errors['general.currency'] && (
                                            <p className="text-sm text-red-600">{errors['general.currency']}</p>
                                        )}
                                    </div>
                                    <div className="space-y-2">
                                        <Label htmlFor="currency_symbol">Currency Symbol</Label>
                                        <Input
                                            id="currency_symbol"
                                            value={data.general.currency_symbol}
                                            onChange={(e) => setData('general', { ...data.general, currency_symbol: e.target.value })}
                                            placeholder="Enter currency symbol"
                                        />
                                        {errors['general.currency_symbol'] && (
                                            <p className="text-sm text-red-600">{errors['general.currency_symbol']}</p>
                                        )}
                                    </div>
                                </div>
                                <div className="space-y-2">
                                    <Label htmlFor="sacco_address">SACCO Address</Label>
                                    <Textarea
                                        id="sacco_address"
                                        value={data.general.sacco_address}
                                        onChange={(e) => setData('general', { ...data.general, sacco_address: e.target.value })}
                                        placeholder="Enter SACCO address"
                                        rows={3}
                                    />
                                    {errors['general.sacco_address'] && (
                                        <p className="text-sm text-red-600">{errors['general.sacco_address']}</p>
                                    )}
                                </div>
                            </CardContent>
                        </Card>
                    </TabsContent>

                    {/* Financial Settings */}
                    <TabsContent value="financial" className="space-y-4">
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <DollarSign className="h-5 w-5" />
                                    Financial Settings
                                </CardTitle>
                                <CardDescription>
                                    Configure financial limits, fees, and rates
                                </CardDescription>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div className="space-y-2">
                                        <Label htmlFor="minimum_deposit">Minimum Deposit</Label>
                                        <Input
                                            id="minimum_deposit"
                                            type="number"
                                            value={data.financial.minimum_deposit}
                                            onChange={(e) => setData('financial', { ...data.financial, minimum_deposit: Number(e.target.value) })}
                                            placeholder="Enter minimum deposit"
                                        />
                                        {errors['financial.minimum_deposit'] && (
                                            <p className="text-sm text-red-600">{errors['financial.minimum_deposit']}</p>
                                        )}
                                    </div>
                                    <div className="space-y-2">
                                        <Label htmlFor="maximum_deposit">Maximum Deposit</Label>
                                        <Input
                                            id="maximum_deposit"
                                            type="number"
                                            value={data.financial.maximum_deposit}
                                            onChange={(e) => setData('financial', { ...data.financial, maximum_deposit: Number(e.target.value) })}
                                            placeholder="Enter maximum deposit"
                                        />
                                        {errors['financial.maximum_deposit'] && (
                                            <p className="text-sm text-red-600">{errors['financial.maximum_deposit']}</p>
                                        )}
                                    </div>
                                    <div className="space-y-2">
                                        <Label htmlFor="minimum_withdrawal">Minimum Withdrawal</Label>
                                        <Input
                                            id="minimum_withdrawal"
                                            type="number"
                                            value={data.financial.minimum_withdrawal}
                                            onChange={(e) => setData('financial', { ...data.financial, minimum_withdrawal: Number(e.target.value) })}
                                            placeholder="Enter minimum withdrawal"
                                        />
                                        {errors['financial.minimum_withdrawal'] && (
                                            <p className="text-sm text-red-600">{errors['financial.minimum_withdrawal']}</p>
                                        )}
                                    </div>
                                    <div className="space-y-2">
                                        <Label htmlFor="maximum_withdrawal">Maximum Withdrawal</Label>
                                        <Input
                                            id="maximum_withdrawal"
                                            type="number"
                                            value={data.financial.maximum_withdrawal}
                                            onChange={(e) => setData('financial', { ...data.financial, maximum_withdrawal: Number(e.target.value) })}
                                            placeholder="Enter maximum withdrawal"
                                        />
                                        {errors['financial.maximum_withdrawal'] && (
                                            <p className="text-sm text-red-600">{errors['financial.maximum_withdrawal']}</p>
                                        )}
                                    </div>
                                    <div className="space-y-2">
                                        <Label htmlFor="transaction_fee">Transaction Fee</Label>
                                        <Input
                                            id="transaction_fee"
                                            type="number"
                                            value={data.financial.transaction_fee}
                                            onChange={(e) => setData('financial', { ...data.financial, transaction_fee: Number(e.target.value) })}
                                            placeholder="Enter transaction fee"
                                        />
                                        {errors['financial.transaction_fee'] && (
                                            <p className="text-sm text-red-600">{errors['financial.transaction_fee']}</p>
                                        )}
                                    </div>
                                    <div className="space-y-2">
                                        <Label htmlFor="monthly_maintenance_fee">Monthly Maintenance Fee</Label>
                                        <Input
                                            id="monthly_maintenance_fee"
                                            type="number"
                                            value={data.financial.monthly_maintenance_fee}
                                            onChange={(e) => setData('financial', { ...data.financial, monthly_maintenance_fee: Number(e.target.value) })}
                                            placeholder="Enter monthly maintenance fee"
                                        />
                                        {errors['financial.monthly_maintenance_fee'] && (
                                            <p className="text-sm text-red-600">{errors['financial.monthly_maintenance_fee']}</p>
                                        )}
                                    </div>
                                    <div className="space-y-2">
                                        <Label htmlFor="interest_rate">Interest Rate (%)</Label>
                                        <Input
                                            id="interest_rate"
                                            type="number"
                                            step="0.1"
                                            value={data.financial.interest_rate}
                                            onChange={(e) => setData('financial', { ...data.financial, interest_rate: Number(e.target.value) })}
                                            placeholder="Enter interest rate"
                                        />
                                        {errors['financial.interest_rate'] && (
                                            <p className="text-sm text-red-600">{errors['financial.interest_rate']}</p>
                                        )}
                                    </div>
                                    <div className="space-y-2">
                                        <Label htmlFor="penalty_rate">Penalty Rate (%)</Label>
                                        <Input
                                            id="penalty_rate"
                                            type="number"
                                            step="0.1"
                                            value={data.financial.penalty_rate}
                                            onChange={(e) => setData('financial', { ...data.financial, penalty_rate: Number(e.target.value) })}
                                            placeholder="Enter penalty rate"
                                        />
                                        {errors['financial.penalty_rate'] && (
                                            <p className="text-sm text-red-600">{errors['financial.penalty_rate']}</p>
                                        )}
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </TabsContent>

                    {/* Continue with other tabs... */}
                    <TabsContent value="loans" className="space-y-4">
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <CreditCard className="h-5 w-5" />
                                    Loan Settings
                                </CardTitle>
                                <CardDescription>
                                    Configure loan policies, limits, and requirements
                                </CardDescription>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div className="space-y-2">
                                        <Label htmlFor="minimum_loan_amount">Minimum Loan Amount</Label>
                                        <Input
                                            id="minimum_loan_amount"
                                            type="number"
                                            value={data.loans.minimum_loan_amount}
                                            onChange={(e) => setData('loans', { ...data.loans, minimum_loan_amount: Number(e.target.value) })}
                                            placeholder="Enter minimum loan amount"
                                        />
                                        {errors['loans.minimum_loan_amount'] && (
                                            <p className="text-sm text-red-600">{errors['loans.minimum_loan_amount']}</p>
                                        )}
                                    </div>
                                    <div className="space-y-2">
                                        <Label htmlFor="maximum_loan_amount">Maximum Loan Amount</Label>
                                        <Input
                                            id="maximum_loan_amount"
                                            type="number"
                                            value={data.loans.maximum_loan_amount}
                                            onChange={(e) => setData('loans', { ...data.loans, maximum_loan_amount: Number(e.target.value) })}
                                            placeholder="Enter maximum loan amount"
                                        />
                                        {errors['loans.maximum_loan_amount'] && (
                                            <p className="text-sm text-red-600">{errors['loans.maximum_loan_amount']}</p>
                                        )}
                                    </div>
                                    <div className="space-y-2">
                                        <Label htmlFor="loan_interest_rate">Loan Interest Rate (%)</Label>
                                        <Input
                                            id="loan_interest_rate"
                                            type="number"
                                            step="0.1"
                                            value={data.loans.loan_interest_rate}
                                            onChange={(e) => setData('loans', { ...data.loans, loan_interest_rate: Number(e.target.value) })}
                                            placeholder="Enter loan interest rate"
                                        />
                                        {errors['loans.loan_interest_rate'] && (
                                            <p className="text-sm text-red-600">{errors['loans.loan_interest_rate']}</p>
                                        )}
                                    </div>
                                    <div className="space-y-2">
                                        <Label htmlFor="loan_approval_levels">Loan Approval Levels</Label>
                                        <Select
                                            value={data.loans.loan_approval_levels.toString()}
                                            onValueChange={(value) => setData('loans', { ...data.loans, loan_approval_levels: Number(value) })}
                                        >
                                            <SelectTrigger>
                                                <SelectValue placeholder="Select approval levels" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="1">1 Level</SelectItem>
                                                <SelectItem value="2">2 Levels</SelectItem>
                                                <SelectItem value="3">3 Levels</SelectItem>
                                                <SelectItem value="4">4 Levels</SelectItem>
                                                <SelectItem value="5">5 Levels</SelectItem>
                                            </SelectContent>
                                        </Select>
                                        {errors['loans.loan_approval_levels'] && (
                                            <p className="text-sm text-red-600">{errors['loans.loan_approval_levels']}</p>
                                        )}
                                    </div>
                                </div>
                                
                                {/* Loan Requirements */}
                                <div className="space-y-4">
                                    <h4 className="text-lg font-medium">Loan Requirements</h4>
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div className="flex items-center space-x-2">
                                            <Switch
                                                id="loan_insurance_required"
                                                checked={data.loans.loan_insurance_required}
                                                onCheckedChange={(checked) => setData('loans', { ...data.loans, loan_insurance_required: checked })}
                                            />
                                            <Label htmlFor="loan_insurance_required">Insurance Required</Label>
                                        </div>
                                        <div className="flex items-center space-x-2">
                                            <Switch
                                                id="loan_guarantor_required"
                                                checked={data.loans.loan_guarantor_required}
                                                onCheckedChange={(checked) => setData('loans', { ...data.loans, loan_guarantor_required: checked })}
                                            />
                                            <Label htmlFor="loan_guarantor_required">Guarantor Required</Label>
                                        </div>
                                        <div className="flex items-center space-x-2">
                                            <Switch
                                                id="loan_collateral_required"
                                                checked={data.loans.loan_collateral_required}
                                                onCheckedChange={(checked) => setData('loans', { ...data.loans, loan_collateral_required: checked })}
                                            />
                                            <Label htmlFor="loan_collateral_required">Collateral Required</Label>
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </TabsContent>

                    {/* Add more tabs as needed... */}
                    <TabsContent value="system" className="space-y-4">
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <Monitor className="h-5 w-5" />
                                    System Settings
                                </CardTitle>
                                <CardDescription>
                                    Advanced system configuration and maintenance
                                </CardDescription>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div className="space-y-4">
                                    <div className="flex items-center space-x-2">
                                        <Switch
                                            id="maintenance_mode"
                                            checked={data.system.maintenance_mode}
                                            onCheckedChange={(checked) => setData('system', { ...data.system, maintenance_mode: checked })}
                                        />
                                        <Label htmlFor="maintenance_mode">Maintenance Mode</Label>
                                    </div>
                                    <div className="flex items-center space-x-2">
                                        <Switch
                                            id="debug_mode"
                                            checked={data.system.debug_mode}
                                            onCheckedChange={(checked) => setData('system', { ...data.system, debug_mode: checked })}
                                        />
                                        <Label htmlFor="debug_mode">Debug Mode</Label>
                                    </div>
                                </div>
                                
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div className="space-y-2">
                                        <Label htmlFor="log_level">Log Level</Label>
                                        <Select
                                            value={data.system.log_level}
                                            onValueChange={(value) => setData('system', { ...data.system, log_level: value })}
                                        >
                                            <SelectTrigger>
                                                <SelectValue placeholder="Select log level" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="debug">Debug</SelectItem>
                                                <SelectItem value="info">Info</SelectItem>
                                                <SelectItem value="warning">Warning</SelectItem>
                                                <SelectItem value="error">Error</SelectItem>
                                            </SelectContent>
                                        </Select>
                                        {errors['system.log_level'] && (
                                            <p className="text-sm text-red-600">{errors['system.log_level']}</p>
                                        )}
                                    </div>
                                    <div className="space-y-2">
                                        <Label htmlFor="auto_logout_minutes">Auto Logout (minutes)</Label>
                                        <Input
                                            id="auto_logout_minutes"
                                            type="number"
                                            value={data.system.auto_logout_minutes}
                                            onChange={(e) => setData('system', { ...data.system, auto_logout_minutes: Number(e.target.value) })}
                                            placeholder="Enter auto logout minutes"
                                        />
                                        {errors['system.auto_logout_minutes'] && (
                                            <p className="text-sm text-red-600">{errors['system.auto_logout_minutes']}</p>
                                        )}
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </TabsContent>
                </Tabs>
            </div>
        </AppLayout>
    );
}
