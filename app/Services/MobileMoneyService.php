<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Account;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MobileMoneyService
{
    private $mpesaConfig;

    public function __construct()
    {
        $this->mpesaConfig = [
            'consumer_key' => env('MPESA_CONSUMER_KEY'),
            'consumer_secret' => env('MPESA_CONSUMER_SECRET'),
            'base_url' => env('MPESA_BASE_URL', 'https://sandbox.safaricom.co.ke'),
            'shortcode' => env('MPESA_SHORTCODE'),
            'passkey' => env('MPESA_PASSKEY'),
        ];
    }

    /**
     * Initiate M-Pesa STK Push payment
     */
    public function initiateMpesaPayment(Account $account, float $amount, string $phoneNumber): array
    {
        try {
            $accessToken = $this->getMpesaAccessToken();
            
            $timestamp = Carbon::now()->format('YmdHis');
            $password = base64_encode($this->mpesaConfig['shortcode'] . $this->mpesaConfig['passkey'] . $timestamp);

            $response = Http::withToken($accessToken)
                ->post($this->mpesaConfig['base_url'] . '/mpesa/stkpush/v1/processrequest', [
                    'BusinessShortCode' => $this->mpesaConfig['shortcode'],
                    'Password' => $password,
                    'Timestamp' => $timestamp,
                    'TransactionType' => 'CustomerPayBillOnline',
                    'Amount' => $amount,
                    'PartyA' => $this->formatPhoneNumber($phoneNumber),
                    'PartyB' => $this->mpesaConfig['shortcode'],
                    'PhoneNumber' => $this->formatPhoneNumber($phoneNumber),
                    'CallBackURL' => env('MPESA_CALLBACK_URL', route('webhooks.mpesa.callback')),
                    'AccountReference' => $account->account_number,
                    'TransactionDesc' => 'SACCO Deposit - ' . $account->account_number,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Store pending transaction
                $transaction = Transaction::create([
                    'account_id' => $account->id,
                    'member_id' => $account->member_id,
                    'type' => Transaction::TYPE_DEPOSIT,
                    'amount' => $amount,
                    'description' => 'M-Pesa deposit - pending confirmation',
                    'reference_number' => $this->generateReferenceNumber(),
                    'status' => Transaction::STATUS_PENDING,
                    'balance_before' => $account->balance,
                    'balance_after' => $account->balance,
                    'payment_method' => 'mpesa',
                    'phone_number' => $phoneNumber,
                    'metadata' => [
                        'checkout_request_id' => $data['CheckoutRequestID'],
                        'merchant_request_id' => $data['MerchantRequestID'],
                    ],
                ]);

                return [
                    'success' => true,
                    'message' => 'Payment initiated. Please complete on your phone.',
                    'transaction_id' => $transaction->id,
                    'checkout_request_id' => $data['CheckoutRequestID'],
                ];
            } else {
                throw new \Exception('M-Pesa API Error: ' . $response->body());
            }

        } catch (\Exception $e) {
            Log::error('M-Pesa payment initiation failed', [
                'error' => $e->getMessage(),
                'account_id' => $account->id,
                'amount' => $amount,
            ]);

            return [
                'success' => false,
                'message' => 'Payment initiation failed: ' . $e->getMessage(),
            ];
        }
    }


    /**
     * Process payment confirmation webhook
     */
    public function processPaymentConfirmation(string $provider, array $webhookData): bool
    {
        try {
            if ($provider === 'mpesa') {
                return $this->processMpesaConfirmation($webhookData);
            }
            
            throw new \Exception('Unknown payment provider: ' . $provider);
        } catch (\Exception $e) {
            Log::error('Payment confirmation processing failed', [
                'provider' => $provider,
                'error' => $e->getMessage(),
                'webhook_data' => $webhookData,
            ]);
            return false;
        }
    }

    /**
     * Get M-Pesa access token
     */
    private function getMpesaAccessToken(): string
    {
        $credentials = base64_encode($this->mpesaConfig['consumer_key'] . ':' . $this->mpesaConfig['consumer_secret']);
        
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $credentials,
        ])->get($this->mpesaConfig['base_url'] . '/oauth/v1/generate?grant_type=client_credentials');

        if ($response->successful()) {
            return $response->json()['access_token'];
        }

        throw new \Exception('Failed to get M-Pesa access token');
    }

    /**
     * Query M-Pesa STK Push status and update the transaction accordingly
     */
    public function queryMpesaStatus(Transaction $transaction): array
    {
        try {
            $checkoutRequestId = $transaction->metadata['checkout_request_id'] ?? null;
            if (!$checkoutRequestId) {
                return ['success' => false, 'message' => 'Missing CheckoutRequestID'];
            }

            $accessToken = $this->getMpesaAccessToken();
            $timestamp = Carbon::now()->format('YmdHis');
            $password = base64_encode($this->mpesaConfig['shortcode'] . $this->mpesaConfig['passkey'] . $timestamp);

            $response = Http::withToken($accessToken)
                ->post($this->mpesaConfig['base_url'] . '/mpesa/stkpushquery/v1/query', [
                    'BusinessShortCode' => $this->mpesaConfig['shortcode'],
                    'Password' => $password,
                    'Timestamp' => $timestamp,
                    'CheckoutRequestID' => $checkoutRequestId,
                ]);

            if (!$response->successful()) {
                throw new \Exception('Query API Error: ' . $response->body());
            }

            $data = $response->json();
            $resultCode = (int) ($data['ResultCode'] ?? 1);
            $resultDesc = $data['ResultDesc'] ?? null;

            if ($resultCode === 0) {
                // Considered successful; if transaction still pending, finalize it
                if ($transaction->status === Transaction::STATUS_PENDING) {
                    $account = $transaction->account;
                    $account->balance += $transaction->amount;
                    $account->save();

                    $transaction->update([
                        'status' => Transaction::STATUS_COMPLETED,
                        'balance_after' => $account->balance,
                        'metadata' => array_merge($transaction->metadata ?? [], [
                            'stk_query_result' => $data,
                            'confirmed_at' => now()->toISOString(),
                        ]),
                    ]);
                }
                return ['success' => true, 'status' => 'completed'];
            }

            // Handle different result codes
            if ($resultCode === 4999) {
                // Transaction is still under processing - keep as pending
                $transaction->update([
                    'metadata' => array_merge($transaction->metadata ?? [], [
                        'stk_query_result' => $data,
                        'result_code' => $resultCode,
                        'result_desc' => $resultDesc,
                        'last_checked_at' => now()->toISOString(),
                    ]),
                ]);
                return ['success' => true, 'status' => 'pending', 'message' => $resultDesc];
            }

            // Other non-zero codes: failed/cancelled/timeout
            if ($transaction->status === Transaction::STATUS_PENDING) {
                $transaction->update([
                    'status' => Transaction::STATUS_FAILED,
                    'metadata' => array_merge($transaction->metadata ?? [], [
                        'stk_query_result' => $data,
                        'result_code' => $resultCode,
                        'result_desc' => $resultDesc,
                        'failed_at' => now()->toISOString(),
                    ]),
                ]);
            }

            return ['success' => true, 'status' => 'failed', 'message' => $resultDesc];

        } catch (\Exception $e) {
            Log::error('M-Pesa STK query failed', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
            ]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }


    /**
     * Format phone number for M-Pesa
     */
    private function formatPhoneNumber(string $phoneNumber): string
    {
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Convert to international format
        if (str_starts_with($phone, '0')) {
            $phone = '254' . substr($phone, 1);
        } elseif (str_starts_with($phone, '7') || str_starts_with($phone, '1')) {
            $phone = '254' . $phone;
        }

        return $phone;
    }

    /**
     * Generate unique reference number
     */
    private function generateReferenceNumber(): string
    {
        return 'MM' . date('Ymd') . strtoupper(substr(uniqid(), -6));
    }

    /**
     * Process M-Pesa confirmation
     */
    private function processMpesaConfirmation(array $webhookData): bool
    {
        $stk = $webhookData['Body']['stkCallback'] ?? [];
        $resultCode = $stk['ResultCode'] ?? null;
        $resultDesc = $stk['ResultDesc'] ?? null;
        $checkoutRequestId = $stk['CheckoutRequestID'] ?? null;

        if (!$checkoutRequestId) {
            return false;
        }

        $transaction = Transaction::where('metadata->checkout_request_id', $checkoutRequestId)->first();
        if (!$transaction) {
            return false;
        }

        // Helper to extract a value from CallbackMetadata by Name
        $extractFromItems = function (array $items, string $name) {
            foreach ($items as $item) {
                if (($item['Name'] ?? null) === $name) {
                    return $item['Value'] ?? null;
                }
            }
            return null;
        };

        if ((int) $resultCode === 0) {
            $items = $stk['CallbackMetadata']['Item'] ?? [];
            $receipt = $extractFromItems($items, 'MpesaReceiptNumber')
                ?? $extractFromItems($items, 'ReceiptNumber')
                ?? null;
            $amountPaid = $extractFromItems($items, 'Amount') ?? $transaction->amount;

            // Credit account
            $account = $transaction->account;
            $account->balance += $transaction->amount;
            $account->save();

            $transaction->update([
                'status' => Transaction::STATUS_COMPLETED,
                'description' => 'M-Pesa deposit - confirmed',
                'balance_after' => $account->balance,
                'metadata' => array_merge($transaction->metadata ?? [], [
                    'mpesa_receipt_number' => $receipt,
                    'paid_amount' => $amountPaid,
                    'result_desc' => $resultDesc,
                    'confirmed_at' => now()->toISOString(),
                ]),
            ]);

            return true;
        }

        // Handle failures/cancellations: mark transaction failed
        $transaction->update([
            'status' => Transaction::STATUS_FAILED,
            'description' => 'M-Pesa deposit - failed',
            'metadata' => array_merge($transaction->metadata ?? [], [
                'result_code' => $resultCode,
                'result_desc' => $resultDesc,
                'failed_at' => now()->toISOString(),
            ]),
        ]);

        return false;
    }

} 