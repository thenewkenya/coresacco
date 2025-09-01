<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Member;
use App\Models\Guarantor;
use App\Models\LoanType;
use App\Services\LoanApplicationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class LoanApplicationController extends Controller
{
    protected $loanApplicationService;

    public function __construct(LoanApplicationService $loanApplicationService)
    {
        $this->loanApplicationService = $loanApplicationService;
    }

    /**
     * Display a listing of loan applications
     */
    public function index()
    {
        $loans = Loan::with(['member', 'loanType', 'guarantors'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $loans
            ]);
        }

        return view('loans.applications-index', compact('loans'));
    }

    /**
     * Show the form for creating a new loan application
     */
    public function create()
    {
        $user = auth()->user();
        
        // For staff/admin, show all members. For members, only show themselves
        if ($user->hasAnyRole(['admin', 'manager', 'staff'])) {
            $members = Member::orderBy('name')->get();
        } else {
            $members = collect([$user]);
        }
        
        $loanTypes = LoanType::orderBy('name')->get();
        
        return view('loans.application', compact('members', 'loanTypes'));
    }

    /**
     * Store a newly created loan application
     */
    public function store(Request $request)
    {
        try {
            // Debug: Log the request data
            \Log::info('Loan application request data:', $request->all());
            
            $validator = Validator::make($request->all(), [
            'member_id' => 'required|exists:users,id',
            'loan_type_id' => 'required|exists:loan_types,id',
            'amount' => 'required|numeric|min:1000',
            'term_period' => 'required|integer|min:1|max:60',
            'interest_rate' => 'nullable|numeric|min:0|max:100',
            'required_savings_multiplier' => 'nullable|numeric|min:1|max:10',
            'minimum_savings_balance' => 'nullable|numeric|min:0',
            'minimum_membership_months' => 'nullable|integer|min:0',
            'required_guarantors' => 'nullable|integer|min:1|max:5',
            'required_guarantee_amount' => 'nullable|numeric|min:0',
            'guarantors' => 'nullable|array',
            'guarantors.*.full_name' => 'required_with:guarantors|string|max:255',
            'guarantors.*.id_number' => 'required_with:guarantors|string|max:50',
            'guarantors.*.phone_number' => 'required_with:guarantors|string|max:20',
            'guarantors.*.address' => 'required_with:guarantors|string',
            'guarantors.*.employment_status' => 'nullable|in:employed,self_employed,unemployed,retired',
            'guarantors.*.monthly_income' => 'nullable|numeric|min:0',
            'guarantors.*.relationship_to_borrower' => 'required_with:guarantors|string|max:100',
            'guarantors.*.guarantee_amount' => 'required_with:guarantors|numeric|min:0',
            'guarantors.*.max_guarantee_amount' => 'nullable|numeric|min:0',
        ]);
        
        // Debug: Log validation results
        \Log::info('Validation passed: ' . ($validator->passes() ? 'true' : 'false'));
        if ($validator->fails()) {
            \Log::info('Validation errors:', $validator->errors()->toArray());
        }

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->loanApplicationService->processLoanApplication($request->all());
        
        // Debug: Log the result
        \Log::info('Loan application service result:', $result);

        // Handle JSON requests (API)
        if ($request->expectsJson()) {
            if ($result['success']) {
                return response()->json($result, 201);
            } else {
                return response()->json($result, 400);
            }
        }

        if ($result['success']) {
            // For members, redirect to their loans page. For staff, show the application details
            if (auth()->user()->hasRole('member')) {
                \Log::info('Redirecting member to loans.my');
                return redirect()->route('loans.my')
                               ->with('success', 'Loan application submitted successfully');
            } else {
                \Log::info('Redirecting staff to loan application details');
                return redirect()->route('loan-applications.show', $result['loan'])
                               ->with('success', 'Loan application submitted successfully');
            }
        } else {
            \Log::info('Loan application failed, returning back with errors: ' . $result['message']);
            return back()->withInput()->withErrors(['error' => $result['message']]);
        }
        
        } catch (\Exception $e) {
            \Log::error('Loan application controller error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->withInput()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified loan application
     */
    public function show(Loan $loan)
    {
        $loan->load(['member', 'loanType', 'guarantors.member']);
        
        $report = $this->loanApplicationService->getLoanEligibilityReport($loan);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $report
            ]);
        }

        return view('loans.application-details', compact('loan'));
    }

    /**
     * Add guarantors to a loan application
     */
    public function addGuarantors(Request $request, Loan $loan): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'guarantors' => 'required|array|min:1',
            'guarantors.*.full_name' => 'required|string|max:255',
            'guarantors.*.id_number' => 'required|string|max:50',
            'guarantors.*.phone_number' => 'required|string|max:20',
            'guarantors.*.address' => 'required|string',
            'guarantors.*.employment_status' => 'nullable|in:employed,self_employed,unemployed,retired',
            'guarantors.*.monthly_income' => 'nullable|numeric|min:0',
            'guarantors.*.relationship_to_borrower' => 'required|string|max:100',
            'guarantors.*.guarantee_amount' => 'required|numeric|min:0',
            'guarantors.*.max_guarantee_amount' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->loanApplicationService->addGuarantorsToLoan($loan, $request->guarantors);

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Approve a guarantor for a loan
     */
    public function approveGuarantor(Request $request, Loan $loan, Guarantor $guarantor): JsonResponse
    {
        $result = $this->loanApplicationService->approveGuarantor($loan, $guarantor);

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Reject a guarantor for a loan
     */
    public function rejectGuarantor(Request $request, Loan $loan, Guarantor $guarantor): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->loanApplicationService->rejectGuarantor($loan, $guarantor, $request->reason);

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Approve a loan application
     */
    public function approveLoan(Request $request, Loan $loan): JsonResponse
    {
        $result = $this->loanApplicationService->approveLoan($loan);

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Reject a loan application
     */
    public function rejectLoan(Request $request, Loan $loan): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->loanApplicationService->rejectLoan($loan, $request->reason);

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Get loan eligibility report
     */
    public function getEligibilityReport(Loan $loan): JsonResponse
    {
        $report = $this->loanApplicationService->getLoanEligibilityReport($loan);

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Check member's borrowing eligibility
     */
    public function checkMemberEligibility(Member $member): JsonResponse
    {
        $savingsBalance = $member->getTotalSavingsBalance();
        $sharesBalance = $member->getTotalSharesBalance();
        $totalBalance = $member->getTotalBalance();
        $monthsInSacco = $member->getMonthsInSacco();

        // Default criteria
        $multiplier = 3.0;
        $minimumBalance = 1000; // 1000 KES
        $minimumMonths = 6;

        $maxLoanAmount = $savingsBalance * $multiplier;
        $meetsSavingsCriteria = $savingsBalance >= $minimumBalance;
        $meetsMembershipCriteria = $monthsInSacco >= $minimumMonths;

        return response()->json([
            'success' => true,
            'data' => [
                'member' => $member,
                'savings_balance' => $savingsBalance,
                'shares_balance' => $sharesBalance,
                'total_balance' => $totalBalance,
                'months_in_sacco' => $monthsInSacco,
                'max_loan_amount' => $maxLoanAmount,
                'meets_savings_criteria' => $meetsSavingsCriteria,
                'meets_membership_criteria' => $meetsMembershipCriteria,
                'overall_eligible' => $meetsSavingsCriteria && $meetsMembershipCriteria,
                'criteria' => [
                    'multiplier' => $multiplier,
                    'minimum_balance' => $minimumBalance,
                    'minimum_months' => $minimumMonths
                ]
            ]
        ]);
    }

    /**
     * Get available guarantors for a member
     */
    public function getAvailableGuarantors(Member $member): JsonResponse
    {
        $guarantors = Guarantor::where('status', Guarantor::STATUS_ACTIVE)
                              ->where('employment_status', '!=', Guarantor::EMPLOYMENT_UNEMPLOYED)
                              ->get()
                              ->filter(function ($guarantor) {
                                  return $guarantor->getAvailableGuaranteeAmount() > 0;
                              });

        return response()->json([
            'success' => true,
            'data' => $guarantors
        ]);
    }
}
