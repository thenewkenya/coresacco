<?php

namespace App\Http\Controllers;

use App\Models\InsuranceProduct;
use App\Models\InsurancePolicy;
use App\Models\InsuranceClaim;
use App\Models\InsurancePremium;
use App\Services\InsuranceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class InsuranceController extends Controller
{
    protected $insuranceService;

    public function __construct(InsuranceService $insuranceService)
    {
        $this->insuranceService = $insuranceService;
    }

    /**
     * Display insurance dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $policySummary = $this->insuranceService->getPolicySummary($user);
        $availableProducts = InsuranceProduct::where('status', 'active')
            ->orderBy('insurance_type')
            ->orderBy('min_coverage_amount')
            ->get();

        return view('insurance.index', compact('policySummary', 'availableProducts'));
    }

    /**
     * Show insurance products
     */
    public function products()
    {
        $products = InsuranceProduct::where('status', 'active')
            ->orderBy('insurance_type')
            ->orderBy('coverage_type')
            ->get()
            ->groupBy('insurance_type');

        return view('insurance.products', compact('products'));
    }

    /**
     * Show insurance product details
     */
    public function showProduct(InsuranceProduct $product)
    {
        $product->load('policies');
        
        return view('insurance.product-details', compact('product'));
    }

    /**
     * Show insurance application form
     */
    public function apply(InsuranceProduct $product)
    {
        $user = Auth::user();
        
        return view('insurance.apply', compact('product', 'user'));
    }

    /**
     * Process insurance application
     */
    public function storeApplication(Request $request, InsuranceProduct $product)
    {
        $request->validate([
            'coverage_amount' => 'required|numeric|min:' . $product->min_coverage_amount . '|max:' . $product->max_coverage_amount,
            'beneficiaries' => 'required|array|min:1',
            'beneficiaries.*.name' => 'required|string|max:255',
            'beneficiaries.*.relationship' => 'required|string|max:100',
            'beneficiaries.*.percentage' => 'required|numeric|min:1|max:100',
            'medical_declarations' => 'nullable|array',
            'property_details' => 'nullable|array',
        ]);

        // Validate beneficiary percentages add up to 100%
        $totalPercentage = collect($request->beneficiaries)->sum('percentage');
        if ($totalPercentage !== 100) {
            return back()->withErrors(['beneficiaries' => 'Beneficiary percentages must add up to 100%'])->withInput();
        }

        try {
            $user = Auth::user();
            $metadata = [
                'application_source' => 'web',
                'medical_declarations' => $request->medical_declarations,
                'property_details' => $request->property_details,
                'agent_id' => $request->agent_id,
            ];

            $riskFactors = $this->calculateRiskFactors($request, $product);

            $policy = $this->insuranceService->createPolicy(
                $user,
                $product,
                $request->coverage_amount,
                $request->beneficiaries,
                $riskFactors,
                $metadata
            );

            return redirect()->route('insurance.policy', $policy)
                ->with('success', 'Insurance application submitted successfully! Policy Number: ' . $policy->policy_number);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Show insurance policy details
     */
    public function policy(InsurancePolicy $policy)
    {
        $this->authorize('view', $policy);
        
        $policy->load(['product', 'member', 'claims', 'premiums']);
        $cashValue = $this->insuranceService->calculateCashValue($policy);

        return view('insurance.policy', compact('policy', 'cashValue'));
    }

    /**
     * Show my insurance policies
     */
    public function myPolicies()
    {
        $user = Auth::user();
        $policySummary = $this->insuranceService->getPolicySummary($user);
        $policies = InsurancePolicy::where('member_id', $user->id)
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('insurance.my-policies', compact('policySummary', 'policies'));
    }

    /**
     * Show claims dashboard
     */
    public function claims()
    {
        $user = Auth::user();
        $claims = InsuranceClaim::where('member_id', $user->id)
            ->with(['policy.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('insurance.claims', compact('claims'));
    }

    /**
     * Show claim form
     */
    public function createClaim(InsurancePolicy $policy)
    {
        $this->authorize('update', $policy);
        
        if (!$policy->isEligibleForClaims()) {
            return back()->withErrors(['error' => 'This policy is not eligible for claims at this time.']);
        }

        $claimTypes = InsuranceClaim::getClaimTypes();
        
        return view('insurance.create-claim', compact('policy', 'claimTypes'));
    }

    /**
     * Store insurance claim
     */
    public function storeClaim(Request $request, InsurancePolicy $policy)
    {
        $this->authorize('update', $policy);
        
        $request->validate([
            'claim_type' => 'required|string',
            'incident_date' => 'required|date|before_or_equal:today',
            'claimed_amount' => 'required|numeric|min:1|max:' . $policy->coverage_amount,
            'incident_description' => 'required|string|min:50',
            'supporting_documents' => 'nullable|array',
            'witness_contact' => 'nullable|string',
            'police_report_number' => 'nullable|string',
        ]);

        try {
            $supportingDocuments = [
                'witness_contact' => $request->witness_contact,
                'police_report_number' => $request->police_report_number,
                'uploaded_files' => $request->supporting_documents ?? []
            ];

            $claim = $this->insuranceService->createClaim(
                $policy,
                $request->claim_type,
                Carbon::parse($request->incident_date),
                $request->claimed_amount,
                $request->incident_description,
                $supportingDocuments
            );

            return redirect()->route('insurance.claim-details', $claim)
                ->with('success', 'Claim submitted successfully! Claim Number: ' . $claim->claim_number);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Show claim details
     */
    public function claimDetails(InsuranceClaim $claim)
    {
        $this->authorize('view', $claim);
        
        $claim->load(['policy.product', 'member']);

        return view('insurance.claim-details', compact('claim'));
    }

    /**
     * Show premium payment form
     */
    public function payPremium(InsurancePremium $premium)
    {
        $this->authorize('update', $premium->policy);
        
        if ($premium->isPaid()) {
            return back()->withErrors(['error' => 'This premium has already been paid.']);
        }

        $premium->load('policy.product');
        $paymentMethods = InsurancePremium::getPaymentMethods();

        return view('insurance.pay-premium', compact('premium', 'paymentMethods'));
    }

    /**
     * Process premium payment
     */
    public function processPremiumPayment(Request $request, InsurancePremium $premium)
    {
        $this->authorize('update', $premium->policy);
        
        $request->validate([
            'payment_amount' => 'required|numeric|min:' . $premium->getTotalAmountDue(),
            'payment_method' => 'required|string',
            'payment_reference' => 'nullable|string',
        ]);

        try {
            $this->insuranceService->processPremiumPayment(
                $premium,
                $request->payment_amount,
                $request->payment_method,
                $request->payment_reference ?? 'WEB-' . time(),
                Auth::user()
            );

            return redirect()->route('insurance.policy', $premium->policy)
                ->with('success', 'Premium payment processed successfully!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Admin: Manage insurance products
     */
    public function manageProducts()
    {
        $this->authorize('manage', InsuranceProduct::class);
        
        $products = InsuranceProduct::orderBy('created_at', 'desc')
            ->get()
            ->groupBy('insurance_type');

        return view('admin.insurance.products', compact('products'));
    }

    /**
     * Admin: Create insurance product
     */
    public function createProduct()
    {
        $this->authorize('create', InsuranceProduct::class);
        
        $insuranceTypes = InsuranceProduct::getInsuranceTypes();
        $coverageTypes = InsuranceProduct::getCoverageTypes();
        $premiumFrequencies = InsuranceProduct::getPremiumFrequencies();

        return view('admin.insurance.create-product', compact('insuranceTypes', 'coverageTypes', 'premiumFrequencies'));
    }

    /**
     * Admin: Store insurance product
     */
    public function storeProduct(Request $request)
    {
        $this->authorize('create', InsuranceProduct::class);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'insurance_type' => 'required|in:life,health,property,crop,micro,travel,business',
            'coverage_type' => 'required|string',
            'min_coverage_amount' => 'required|numeric|min:1',
            'max_coverage_amount' => 'required|numeric|gt:min_coverage_amount',
            'min_age' => 'required|integer|min:0|max:100',
            'max_age' => 'required|integer|gt:min_age|max:100',
            'base_premium_rate' => 'required|numeric|min:0|max:100',
            'premium_frequency' => 'required|in:monthly,quarterly,semi_annually,annually',
            'grace_period_days' => 'required|integer|min:0|max:365',
            'waiting_period_days' => 'required|integer|min:0|max:365',
            'claim_settlement_days' => 'required|integer|min:1|max:365',
            'commission_rate' => 'nullable|numeric|min:0|max:50',
        ]);

        try {
            $product = InsuranceProduct::create($request->all());

            return redirect()->route('admin.insurance.products')
                ->with('success', 'Insurance product created successfully!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Admin: Manage claims
     */
    public function manageClaims()
    {
        $this->authorize('viewAny', InsuranceClaim::class);
        
        $claims = InsuranceClaim::with(['member', 'policy.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.insurance.claims', compact('claims'));
    }

    /**
     * Admin: Process claim
     */
    public function processClaimAdmin(Request $request, InsuranceClaim $claim)
    {
        $this->authorize('manage', $claim);
        
        $request->validate([
            'decision' => 'required|in:approved,rejected',
            'approved_amount' => 'required_if:decision,approved|nullable|numeric|min:1',
            'notes' => 'required_if:decision,rejected|nullable|string',
        ]);

        try {
            $this->insuranceService->processClaim(
                $claim,
                $request->decision,
                $request->approved_amount,
                $request->notes,
                Auth::user()
            );

            return back()->with('success', 'Claim ' . $request->decision . ' successfully!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Admin: Pay approved claim
     */
    public function payClaimAdmin(Request $request, InsuranceClaim $claim)
    {
        $this->authorize('manage', $claim);
        
        $request->validate([
            'payment_method' => 'required|string',
            'payment_reference' => 'required|string',
        ]);

        try {
            $this->insuranceService->payClaim(
                $claim,
                $request->payment_method,
                $request->payment_reference,
                Auth::user()
            );

            return back()->with('success', 'Claim payment processed successfully!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Admin: Insurance analytics
     */
    public function analytics()
    {
        $this->authorize('viewAny', InsurancePolicy::class);
        
        $analytics = [
            'total_policies' => InsurancePolicy::count(),
            'active_policies' => InsurancePolicy::where('status', 'active')->count(),
            'total_coverage' => InsurancePolicy::sum('coverage_amount'),
            'total_premiums_collected' => InsurancePolicy::sum('total_premiums_paid'),
            'total_claims_paid' => InsurancePolicy::sum('total_claims_paid'),
            'pending_claims' => InsuranceClaim::where('status', 'pending')->count(),
            'overdue_premiums' => InsurancePremium::where('status', 'overdue')->count(),
            'policies_by_type' => InsurancePolicy::join('insurance_products', 'insurance_policies.product_id', '=', 'insurance_products.id')
                ->selectRaw('insurance_products.insurance_type, COUNT(*) as count')
                ->groupBy('insurance_products.insurance_type')
                ->pluck('count', 'insurance_type'),
            'recent_claims' => InsuranceClaim::with(['member', 'policy.product'])
                ->latest()
                ->limit(10)
                ->get(),
        ];

        return view('admin.insurance.analytics', compact('analytics'));
    }

    /**
     * Private helper methods
     */
    private function calculateRiskFactors(Request $request, InsuranceProduct $product): array
    {
        $riskFactors = [];
        
        // Add age-based risk factor
        $user = Auth::user();
        $age = Carbon::parse($user->date_of_birth)->age ?? 25;
        $riskFactors['age'] = $age;
        
        // Add medical history risk factors
        if ($request->has('medical_declarations')) {
            $riskFactors['medical_history'] = $request->medical_declarations;
        }
        
        // Add property-specific risk factors
        if ($product->insurance_type === 'property' && $request->has('property_details')) {
            $riskFactors['property_details'] = $request->property_details;
        }
        
        return $riskFactors;
    }
} 