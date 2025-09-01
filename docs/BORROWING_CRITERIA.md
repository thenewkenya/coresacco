# Borrowing Criteria and Guarantor System

This document outlines the implementation of borrowing criteria tied to member savings and the guarantor system for the eSacco application.

## Overview

The eSacco system now includes comprehensive borrowing criteria that ensure loans are only granted to eligible members based on their savings, membership duration, and guarantor requirements. This is essential for maintaining the financial stability of the SACCO.

## Borrowing Criteria

### 1. Savings Criteria

**Requirements:**
- Member must have a minimum savings balance (configurable, default: 1,000 KES)
- Loan amount must not exceed savings balance × multiplier (configurable, default: 3x)
- Both savings and shares accounts are considered

**Implementation:**
```php
// Check if member meets savings criteria
$savingsBalance = $member->getTotalSavingsBalance();
$maxLoanAmount = $savingsBalance * $multiplier;
$meetsCriteria = $savingsBalance >= $minimumBalance && $amount <= $maxLoanAmount;
```

### 2. Membership Criteria

**Requirements:**
- Member must have been in the SACCO for a minimum number of months (configurable, default: 6 months)
- Calculated from the member's joining date

**Implementation:**
```php
// Check membership duration
$monthsInSacco = $member->getMonthsInSacco();
$meetsCriteria = $monthsInSacco >= $minimumMonths;
```

### 3. Guarantor Criteria

**Requirements:**
- Minimum number of guarantors (configurable, default: 2)
- Minimum total guarantee amount (configurable, default: 50% of loan amount)
- All guarantors must be approved
- Guarantors must be eligible (active status, employed, sufficient capacity)

## Guarantor System

### Guarantor Model

The `Guarantor` model includes:

**Fields:**
- `member_id` - Optional link to existing SACCO member
- `full_name` - Full name of guarantor
- `id_number` - National ID number (unique)
- `phone_number` - Contact number
- `address` - Physical address
- `employment_status` - employed, self_employed, unemployed, retired
- `monthly_income` - Monthly income amount
- `relationship_to_borrower` - Relationship to loan applicant
- `status` - active, inactive, suspended, blacklisted
- `max_guarantee_amount` - Maximum amount they can guarantee
- `current_guarantee_obligations` - Current guarantee obligations

**Key Methods:**
```php
// Check if guarantor can guarantee an amount
$guarantor->canGuarantee($amount);

// Get available guarantee amount
$available = $guarantor->getAvailableGuaranteeAmount();

// Check if guarantor is eligible
$eligible = $guarantor->isEligible();
```

### Loan-Guarantor Relationship

The `loan_guarantors` pivot table manages the relationship between loans and guarantors:

**Fields:**
- `loan_id` - Reference to loan
- `guarantor_id` - Reference to guarantor
- `guarantee_amount` - Amount guaranteed by this guarantor
- `status` - pending, approved, rejected, active, completed
- `approved_at` - Timestamp when approved
- `rejection_reason` - Reason for rejection if applicable

## API Endpoints

### Loan Applications

**POST** `/api/loan-applications`
Create a new loan application with full criteria evaluation.

**Request Body:**
```json
{
    "member_id": 1,
    "loan_type_id": 1,
    "amount": 25000,
    "term_period": 12,
    "interest_rate": 12.0,
    "required_savings_multiplier": 3.0,
    "minimum_savings_balance": 1000,
    "minimum_membership_months": 6,
    "required_guarantors": 2,
    "required_guarantee_amount": 12500,
    "guarantors": [
        {
            "full_name": "John Doe",
            "id_number": "12345678",
            "phone_number": "+254700000000",
            "address": "Nairobi, Kenya",
            "employment_status": "employed",
            "monthly_income": 50000,
            "relationship_to_borrower": "Brother",
            "guarantee_amount": 6250
        }
    ]
}
```

**GET** `/api/loan-applications/{loan}`
Get loan application details with evaluation results.

**GET** `/api/loan-applications/{loan}/eligibility`
Get detailed eligibility report for a loan.

### Member Eligibility

**GET** `/api/members/{member}/eligibility`
Check if a member is eligible for borrowing.

**GET** `/api/members/{member}/available-guarantors`
Get list of available guarantors for a member.

### Guarantor Management

**POST** `/api/loan-applications/{loan}/guarantors/{guarantor}/approve`
Approve a guarantor for a loan.

**POST** `/api/loan-applications/{loan}/guarantors/{guarantor}/reject`
Reject a guarantor for a loan.

**Request Body:**
```json
{
    "reason": "Insufficient income"
}
```

### Loan Approval

**POST** `/api/loan-applications/{loan}/approve`
Approve a loan application (only if all criteria are met).

**POST** `/api/loan-applications/{loan}/reject`
Reject a loan application.

**Request Body:**
```json
{
    "reason": "Insufficient guarantors"
}
```

## Usage Examples

### Creating a Loan Application

```php
use App\Services\LoanApplicationService;

$service = new LoanApplicationService();

$data = [
    'member_id' => 1,
    'loan_type_id' => 1,
    'amount' => 25000,
    'term_period' => 12,
    'guarantors' => [
        [
            'full_name' => 'John Doe',
            'id_number' => '12345678',
            'phone_number' => '+254700000000',
            'address' => 'Nairobi, Kenya',
            'employment_status' => 'employed',
            'monthly_income' => 50000,
            'relationship_to_borrower' => 'Brother',
            'guarantee_amount' => 12500
        ]
    ]
];

$result = $service->processLoanApplication($data);
```

### Checking Loan Eligibility

```php
$loan = Loan::find(1);
$evaluation = $loan->evaluateBorrowingCriteria();

if ($evaluation['overall_eligible']) {
    echo "Loan is eligible for approval";
} else {
    echo "Loan does not meet criteria";
    print_r($evaluation);
}
```

### Approving a Guarantor

```php
$result = $service->approveGuarantor($loan, $guarantorId);
if ($result['success']) {
    echo "Guarantor approved successfully";
}
```

## Database Schema

### New Tables

1. **guarantors**
   - Stores guarantor information
   - Tracks guarantee capacity and obligations

2. **loan_guarantors**
   - Pivot table linking loans and guarantors
   - Tracks guarantee amounts and approval status

### Modified Tables

1. **loans**
   - Added borrowing criteria fields
   - Added guarantor requirement fields
   - Added evaluation result fields

## Testing

Run the borrowing criteria tests:

```bash
php artisan test tests/Feature/LoanBorrowingCriteriaTest.php
```

The tests cover:
- Savings criteria evaluation
- Membership criteria evaluation
- Guarantor criteria evaluation
- Overall loan eligibility
- Member borrowing eligibility checks

## Configuration

Default values can be configured in the loan application service:

- `required_savings_multiplier`: 3.0
- `minimum_savings_balance`: 1,000 KES
- `minimum_membership_months`: 6 months
- `required_guarantors`: 2 guarantors
- `required_guarantee_amount`: 50% of loan amount

These values can be customized per loan type or loan application.

## Security Considerations

1. **Guarantor Verification**: All guarantors must be verified before approval
2. **Income Validation**: Monthly income should be verified with supporting documents
3. **Relationship Validation**: Ensure guarantors are not related in ways that could create conflicts
4. **Capacity Monitoring**: Track guarantor obligations to prevent over-commitment
5. **Audit Trail**: All approvals and rejections are logged with timestamps and reasons

## Future Enhancements

1. **Credit Scoring**: Integrate with credit bureaus for additional risk assessment
2. **Collateral Management**: Add support for physical collateral
3. **Guarantor Scoring**: Implement guarantor risk scoring based on history
4. **Automated Approval**: Rules-based automated approval for low-risk loans
5. **Mobile App Integration**: Allow guarantors to approve loans via mobile app
