<?php

namespace App\Http\Controllers\Api;

use App\Models\Loan;
use App\Models\LoanType;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\LoanRequest;
use App\Http\Requests\LoanRepaymentRequest;
use Illuminate\Support\Facades\DB;

class LoanController extends ApiController
{
    public function index(): JsonResponse
    {
        $loans = Loan::with(['member', 'loanType'])->paginate(10);
        return $this->successResponse($loans);
    }

    public function store(LoanRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $loanType = LoanType::findOrFail($request->loan_type_id);
            
            // Validate loan amount against loan type limits
            if (!$loanType->isEligibleAmount($request->amount)) {
                return $this->errorResponse('Loan amount is outside the allowed range for this loan type');
            }

            // Calculate processing fee
            $processingFee = $loanType->calculateProcessingFee($request->amount);
            
            // Calculate total payable amount
            $totalPayable = $request->amount + ($request->amount * ($loanType->interest_rate / 100));

            $loan = Loan::create([
                'member_id' => $request->member_id,
                'loan_type_id' => $request->loan_type_id,
                'amount' => $request->amount,
                'interest_rate' => $loanType->interest_rate,
                'term_period' => $request->term_period,
                'processing_fee' => $processingFee,
                'total_payable' => $totalPayable,
                'collateral_details' => $request->collateral_details,
                'status' => 'pending',
            ]);

            DB::commit();
            return $this->createdResponse($loan, 'Loan application submitted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to submit loan application: ' . $e->getMessage());
        }
    }

    public function show(Loan $loan): JsonResponse
    {
        $loan->load(['member', 'loanType', 'transactions']);
        return $this->successResponse($loan);
    }

    public function approve(Loan $loan): JsonResponse
    {
        try {
            if ($loan->status !== 'pending') {
                return $this->errorResponse('Loan is not in pending status');
            }

            $loan->update(['status' => 'approved']);
            return $this->successResponse($loan, 'Loan approved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to approve loan: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, Loan $loan): JsonResponse
    {
        try {
            if ($loan->status !== 'pending') {
                return $this->errorResponse('Loan is not in pending status');
            }

            $loan->update([
                'status' => 'rejected',
                'rejection_reason' => $request->reason
            ]);
            return $this->successResponse($loan, 'Loan rejected successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to reject loan: ' . $e->getMessage());
        }
    }

    public function disburse(Loan $loan): JsonResponse
    {
        try {
            if ($loan->status !== 'approved') {
                return $this->errorResponse('Loan is not approved');
            }

            DB::beginTransaction();

            // Create disbursement transaction
            $transaction = Transaction::create([
                'account_id' => $loan->member->accounts()->where('account_type', 'savings')->first()->id,
                'member_id' => $loan->member_id,
                'loan_id' => $loan->id,
                'type' => Transaction::TYPE_LOAN_DISBURSEMENT,
                'amount' => $loan->amount,
                'description' => 'Loan disbursement',
                'reference_number' => 'LDS' . time() . rand(1000, 9999),
                'status' => 'completed',
            ]);

            $loan->update([
                'status' => 'disbursed',
                'disbursement_date' => now(),
                'due_date' => now()->addMonths($loan->term_period)
            ]);

            DB::commit();
            return $this->successResponse($loan, 'Loan disbursed successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to disburse loan: ' . $e->getMessage());
        }
    }

    public function repay(LoanRepaymentRequest $request, Loan $loan): JsonResponse
    {
        try {
            if (!in_array($loan->status, ['disbursed', 'active'])) {
                return $this->errorResponse('Loan is not active');
            }

            DB::beginTransaction();

            // Create repayment transaction
            $transaction = Transaction::create([
                'account_id' => $loan->member->accounts()->where('account_type', 'savings')->first()->id,
                'member_id' => $loan->member_id,
                'loan_id' => $loan->id,
                'type' => Transaction::TYPE_LOAN_REPAYMENT,
                'amount' => $request->amount,
                'description' => 'Loan repayment',
                'reference_number' => 'LRP' . time() . rand(1000, 9999),
                'status' => 'completed',
            ]);

            // Update loan amount paid
            $loan->amount_paid += $request->amount;
            
            // Check if loan is fully paid
            if ($loan->amount_paid >= $loan->total_payable) {
                $loan->status = 'completed';
            } else {
                $loan->status = 'active';
            }
            
            $loan->save();

            DB::commit();
            return $this->successResponse($transaction, 'Loan repayment processed successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to process loan repayment: ' . $e->getMessage());
        }
    }

    public function schedule(Loan $loan): JsonResponse
    {
        // Calculate repayment schedule
        $monthlyPayment = $loan->calculateMonthlyPayment();
        $schedule = [];
        
        for ($i = 1; $i <= $loan->term_period; $i++) {
            $schedule[] = [
                'installment_number' => $i,
                'due_date' => $loan->disbursement_date->copy()->addMonths($i),
                'amount' => $monthlyPayment,
                'status' => $loan->amount_paid >= ($monthlyPayment * $i) ? 'paid' : 'pending'
            ];
        }

        return $this->successResponse([
            'loan' => $loan,
            'schedule' => $schedule
        ]);
    }
} 