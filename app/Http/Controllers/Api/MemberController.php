<?php

namespace App\Http\Controllers\Api;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\MemberRequest;
use Illuminate\Support\Facades\DB;

class MemberController extends ApiController
{
    public function index(): JsonResponse
    {
        $members = Member::with(['branch'])->paginate(10);
        return $this->successResponse($members);
    }

    public function store(MemberRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $member = Member::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'member_number' => 'M' . str_pad(Member::count() + 1, 6, '0', STR_PAD_LEFT),
                'id_number' => $request->id_number,
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'membership_status' => 'active',
                'joining_date' => now(),
                'branch_id' => $request->branch_id,
            ]);

            // Create default savings account for the member
            $member->accounts()->create([
                'account_number' => 'SA' . str_pad($member->id, 8, '0', STR_PAD_LEFT),
                'account_type' => 'savings',
                'status' => 'active',
            ]);

            DB::commit();
            return $this->createdResponse($member, 'Member registered successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to register member: ' . $e->getMessage());
        }
    }

    public function show(Member $member): JsonResponse
    {
        $member->load(['accounts', 'loans', 'insurancePolicies']);
        return $this->successResponse($member);
    }

    public function update(MemberRequest $request, Member $member): JsonResponse
    {
        try {
            $member->update($request->validated());
            return $this->successResponse($member, 'Member updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update member: ' . $e->getMessage());
        }
    }

    public function destroy(Member $member): JsonResponse
    {
        try {
            $member->delete();
            return $this->noContentResponse();
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete member: ' . $e->getMessage());
        }
    }

    public function accounts(Member $member): JsonResponse
    {
        $accounts = $member->accounts()->with(['transactions'])->get();
        return $this->successResponse($accounts);
    }

    public function loans(Member $member): JsonResponse
    {
        $loans = $member->loans()->with(['loanType'])->get();
        return $this->successResponse($loans);
    }

    public function transactions(Member $member): JsonResponse
    {
        $transactions = $member->transactions()
            ->with(['account', 'loan'])
            ->latest()
            ->paginate(15);
        return $this->successResponse($transactions);
    }
} 