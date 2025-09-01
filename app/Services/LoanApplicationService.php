<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\Member;
use App\Models\Guarantor;
use App\Models\LoanType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LoanApplicationService
{
    /**
     * Process a new loan application with full criteria evaluation
     */
    public function processLoanApplication(array $data): array
    {
        try {
            DB::beginTransaction();

            $member = Member::findOrFail($data['member_id']);
            $loanType = LoanType::findOrFail($data['loan_type_id']);

            // Create the loan
            $loan = Loan::create([
                'member_id' => $member->id,
                'loan_type_id' => $loanType->id,
                'amount' => $data['amount'],
                'interest_rate' => $data['interest_rate'] ?? $loanType->interest_rate,
                'term_period' => $data['term_period'],
                'required_savings_multiplier' => $data['required_savings_multiplier'] ?? 3.0,
                'minimum_savings_balance' => $data['minimum_savings_balance'] ?? 0,
                'minimum_membership_months' => $data['minimum_membership_months'] ?? 6,
                'required_guarantors' => $data['required_guarantors'] ?? 2,
                'required_guarantee_amount' => $data['required_guarantee_amount'] ?? ($data['amount'] * 0.5),
                'status' => Loan::STATUS_PENDING,
            ]);

            // Evaluate borrowing criteria
            $evaluation = $loan->evaluateBorrowingCriteria();

            // Add guarantors if provided
            if (isset($data['guarantors']) && is_array($data['guarantors'])) {
                $this->addGuarantorsToLoan($loan, $data['guarantors']);
            }

            // Re-evaluate guarantor criteria after adding guarantors
            $loan->evaluateGuarantorCriteria();

            DB::commit();

            return [
                'success' => true,
                'loan' => $loan,
                'evaluation' => $evaluation,
                'message' => 'Loan application processed successfully'
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Loan application failed: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Loan application failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Add guarantors to a loan
     */
    public function addGuarantorsToLoan(Loan $loan, array $guarantors): void
    {
        foreach ($guarantors as $guarantorData) {
            // Check if guarantor exists
            $guarantor = Guarantor::where('id_number', $guarantorData['id_number'])->first();
            
            if (!$guarantor) {
                // Create new guarantor
                $guarantor = Guarantor::create([
                    'member_id' => $guarantorData['member_id'] ?? null,
                    'full_name' => $guarantorData['full_name'],
                    'id_number' => $guarantorData['id_number'],
                    'phone_number' => $guarantorData['phone_number'],
                    'address' => $guarantorData['address'],
                    'employment_status' => $guarantorData['employment_status'] ?? Guarantor::EMPLOYMENT_EMPLOYED,
                    'monthly_income' => $guarantorData['monthly_income'] ?? 0,
                    'relationship_to_borrower' => $guarantorData['relationship_to_borrower'],
                    'max_guarantee_amount' => $guarantorData['max_guarantee_amount'] ?? ($guarantorData['monthly_income'] * 6),
                ]);
            }

            // Attach guarantor to loan
            $loan->guarantors()->attach($guarantor->id, [
                'guarantee_amount' => $guarantorData['guarantee_amount'],
                'status' => 'pending'
            ]);
        }
    }

    /**
     * Approve a guarantor for a loan
     */
    public function approveGuarantor(Loan $loan, int $guarantorId): array
    {
        try {
            $pivot = $loan->guarantors()->where('guarantor_id', $guarantorId)->first()->pivot;
            
            if (!$pivot) {
                return ['success' => false, 'message' => 'Guarantor not found for this loan'];
            }

            $guarantor = Guarantor::find($guarantorId);
            
            if (!$guarantor->isEligible()) {
                return ['success' => false, 'message' => 'Guarantor is not eligible'];
            }

            if (!$guarantor->canGuarantee($pivot->guarantee_amount)) {
                return ['success' => false, 'message' => 'Guarantor cannot guarantee this amount'];
            }

            // Update pivot
            $loan->guarantors()->updateExistingPivot($guarantorId, [
                'status' => 'approved',
                'approved_at' => now()
            ]);

            // Update guarantor obligations
            $guarantor->updateGuaranteeObligations($pivot->guarantee_amount);

            // Re-evaluate loan criteria
            $loan->evaluateGuarantorCriteria();

            return [
                'success' => true,
                'message' => 'Guarantor approved successfully',
                'evaluation' => $loan->evaluateBorrowingCriteria()
            ];

        } catch (\Exception $e) {
            Log::error('Guarantor approval failed: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Guarantor approval failed: ' . $e->getMessage()];
        }
    }

    /**
     * Reject a guarantor for a loan
     */
    public function rejectGuarantor(Loan $loan, int $guarantorId, string $reason = null): array
    {
        try {
            $loan->guarantors()->updateExistingPivot($guarantorId, [
                'status' => 'rejected',
                'rejection_reason' => $reason
            ]);

            // Re-evaluate loan criteria
            $loan->evaluateGuarantorCriteria();

            return [
                'success' => true,
                'message' => 'Guarantor rejected successfully',
                'evaluation' => $loan->evaluateBorrowingCriteria()
            ];

        } catch (\Exception $e) {
            Log::error('Guarantor rejection failed: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Guarantor rejection failed: ' . $e->getMessage()];
        }
    }

    /**
     * Approve a loan application
     */
    public function approveLoan(Loan $loan): array
    {
        try {
            $evaluation = $loan->evaluateBorrowingCriteria();
            
            if (!$evaluation['overall_eligible']) {
                return [
                    'success' => false,
                    'message' => 'Loan does not meet all criteria',
                    'evaluation' => $evaluation
                ];
            }

            $loan->update(['status' => Loan::STATUS_APPROVED]);

            return [
                'success' => true,
                'message' => 'Loan approved successfully',
                'evaluation' => $evaluation
            ];

        } catch (\Exception $e) {
            Log::error('Loan approval failed: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Loan approval failed: ' . $e->getMessage()];
        }
    }

    /**
     * Reject a loan application
     */
    public function rejectLoan(Loan $loan, string $reason = null): array
    {
        try {
            $loan->update([
                'status' => Loan::STATUS_REJECTED,
                'criteria_evaluation_notes' => $reason
            ]);

            return [
                'success' => true,
                'message' => 'Loan rejected successfully'
            ];

        } catch (\Exception $e) {
            Log::error('Loan rejection failed: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Loan rejection failed: ' . $e->getMessage()];
        }
    }

    /**
     * Get loan eligibility report
     */
    public function getLoanEligibilityReport(Loan $loan): array
    {
        return [
            'loan' => $loan,
            'evaluation' => $loan->evaluateBorrowingCriteria(),
            'guarantors' => $loan->guarantors()->with('member')->get(),
            'member' => $loan->member
        ];
    }
}
