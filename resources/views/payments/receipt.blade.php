<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Receipt - {{ $payment->reference_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            line-height: 1.6;
            color: #333;
        }
        .receipt-header {
            text-align: center;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
        }
        .receipt-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .receipt-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .info-section {
            flex: 1;
        }
        .info-section h3 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #666;
            text-transform: uppercase;
        }
        .info-item {
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }
        .payment-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .amount {
            font-size: 32px;
            font-weight: bold;
            text-align: center;
            color: #059669;
            margin: 20px 0;
        }
        .status {
            text-align: center;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .status.completed {
            background: #d1fae5;
            color: #065f46;
        }
        .status.pending {
            background: #fef3c7;
            color: #92400e;
        }
        .status.failed {
            background: #fee2e2;
            color: #991b1b;
        }
        .footer {
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 20px;
            margin-top: 30px;
            font-size: 12px;
            color: #666;
        }
        .print-only {
            display: none;
        }
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
            .no-print {
                display: none;
            }
            .print-only {
                display: block;
            }
        }
        .actions {
            text-align: center;
            margin-bottom: 20px;
        }
        .btn {
            background: #2563eb;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 0 5px;
            text-decoration: none;
            display: inline-block;
        }
        .btn:hover {
            background: #1d4ed8;
        }
        .reference-number {
            font-family: 'Courier New', monospace;
            font-size: 18px;
            font-weight: bold;
            background: #f3f4f6;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="actions no-print">
        <button class="btn" onclick="window.print()">Print Receipt</button>
        <a href="{{ route('payments.show', $payment) }}" class="btn">Back to Payment</a>
    </div>

    <div class="receipt-header">
        <div class="logo">{{ config('app.name', 'SACCO System') }}</div>
        <div>Savings and Credit Cooperative Society</div>
        <div style="font-size: 12px; color: #666; margin-top: 5px;">
            P.O. Box 12345, Nairobi, Kenya | Tel: +254 700 000 000
        </div>
    </div>

    <div class="receipt-title">PAYMENT RECEIPT</div>

    <div class="reference-number">
        REF: {{ $payment->reference_number }}
    </div>

    <div class="status {{ $payment->status }}">
        STATUS: {{ strtoupper($payment->status) }}
    </div>

    <div class="amount">
        KES {{ number_format($payment->amount, 2) }}
    </div>

    <div class="receipt-info">
        <div class="info-section">
            <h3>Payment Information</h3>
            <div class="info-item">
                <span class="info-label">Type:</span>
                {{ ucwords(str_replace('_', ' ', $payment->type)) }}
            </div>
            <div class="info-item">
                <span class="info-label">Method:</span>
                {{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}
            </div>
            <div class="info-item">
                <span class="info-label">Date:</span>
                {{ $payment->created_at->format('M d, Y') }}
            </div>
            <div class="info-item">
                <span class="info-label">Time:</span>
                {{ $payment->created_at->format('g:i A') }}
            </div>
            @if($payment->external_reference)
            <div class="info-item">
                <span class="info-label">External Ref:</span>
                {{ $payment->external_reference }}
            </div>
            @endif
        </div>

        <div class="info-section">
            <h3>Member Details</h3>
            <div class="info-item">
                <span class="info-label">Name:</span>
                {{ $payment->member->name }}
            </div>
            <div class="info-item">
                <span class="info-label">Email:</span>
                {{ $payment->member->email }}
            </div>
            <div class="info-item">
                <span class="info-label">Phone:</span>
                {{ $payment->member->phone ?? 'N/A' }}
            </div>
            <div class="info-item">
                <span class="info-label">Member ID:</span>
                {{ $payment->member->member_number ?? $payment->member->id }}
            </div>
        </div>
    </div>

    @if($payment->account_id || $payment->loan_id)
    <div class="payment-details">
        <h3 style="margin-top: 0;">Transaction Details</h3>
        
        @if($payment->account)
        <div style="margin-bottom: 15px;">
            <strong>Savings Account:</strong><br>
            Account Number: {{ $payment->account->account_number }}<br>
            Account Type: {{ ucwords($payment->account->account_type) }}<br>
            Balance After Transaction: KES {{ number_format($payment->account->balance, 2) }}
        </div>
        @endif

        @if($payment->loan)
        <div style="margin-bottom: 15px;">
            <strong>Loan Repayment:</strong><br>
            Loan Type: {{ $payment->loan->loanType->name }}<br>
            Original Amount: KES {{ number_format($payment->loan->amount, 2) }}<br>
            Outstanding Balance: KES {{ number_format($payment->loan->outstanding_balance ?? $payment->loan->amount, 2) }}
        </div>
        @endif

        @if($payment->description)
        <div>
            <strong>Description:</strong><br>
            {{ $payment->description }}
        </div>
        @endif
    </div>
    @endif

    <div class="payment-details">
        <h3 style="margin-top: 0;">Payment Breakdown</h3>
        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <span>Payment Amount:</span>
            <span><strong>KES {{ number_format($payment->amount, 2) }}</strong></span>
        </div>
        @if($payment->fees > 0)
        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <span>Processing Fee:</span>
            <span>KES {{ number_format($payment->fees, 2) }}</span>
        </div>
        <div style="display: flex; justify-content: space-between; border-top: 1px solid #ddd; padding-top: 10px; margin-top: 10px;">
            <span><strong>Total Paid:</strong></span>
            <span><strong>KES {{ number_format($payment->amount + $payment->fees, 2) }}</strong></span>
        </div>
        @endif
    </div>

    <div class="footer">
        <div style="margin-bottom: 10px;">
            <strong>Thank you for your payment!</strong>
        </div>
        <div>
            This is a computer-generated receipt. No signature required.<br>
            For any queries, please contact us at info@sacco.co.ke or call +254 700 000 000
        </div>
        <div style="margin-top: 15px; font-size: 10px;">
            Receipt generated on {{ now()->format('M d, Y \a\t g:i A') }}
        </div>
    </div>

    <div class="print-only" style="page-break-before: always;">
        <!-- Duplicate for member copy -->
        <div style="text-align: center; margin-bottom: 20px; font-weight: bold; border-bottom: 1px dashed #ccc; padding-bottom: 10px;">
            MEMBER COPY
        </div>
        
        <div class="receipt-header">
            <div class="logo">{{ config('app.name', 'SACCO System') }}</div>
            <div>Savings and Credit Cooperative Society</div>
        </div>

        <div class="receipt-title">PAYMENT RECEIPT</div>
        
        <div class="reference-number">
            REF: {{ $payment->reference_number }}
        </div>

        <div class="amount">
            KES {{ number_format($payment->amount, 2) }}
        </div>

        <div style="text-align: center; margin: 20px 0;">
            <div><strong>{{ $payment->member->name }}</strong></div>
            <div>{{ ucwords(str_replace('_', ' ', $payment->type)) }}</div>
            <div>{{ $payment->created_at->format('M d, Y g:i A') }}</div>
            <div class="status {{ $payment->status }}" style="display: inline-block; margin-top: 10px;">
                {{ strtoupper($payment->status) }}
            </div>
        </div>
    </div>
</body>
</html>
