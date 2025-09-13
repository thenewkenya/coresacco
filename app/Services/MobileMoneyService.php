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
    private $airtelConfig;
    private $tkashConfig;

    public function __construct()
    {
        $this->mpesaConfig = [
            'consumer_key' => env('MPESA_CONSUMER_KEY'),
            'consumer_secret' => env('MPESA_CONSUMER_SECRET'),
            'base_url' => env('MPESA_BASE_URL', 'https://sandbox.safaricom.co.ke'),
            'shortcode' => env('MPESA_SHORTCODE'),
            'passkey' => env('MPESA_PASSKEY'),
        ];

        $this->airtelConfig = [
            'client_id' => setting('airtel_client_id'),
            'client_secret' => setting('airtel_client_secret'),
            'base_url' => setting('airtel_base_url', 'https://openapi.airtel.africa'),
        ];

        $this->tkashConfig = [
            'merchant_code' => setting('tkash_merchant_code'),
            'api_key' => setting('tkash_api_key'),
            'base_url' => setting('tkash_base_url'),
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
                    'metadata' => [
                        'payment_method' => 'mpesa',
                        'phone_number' => $phoneNumber,
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
     * Initiate Airtel Money payment
     */
    public function initiateAirtelPayment(Account $account, float $amount, string $phoneNumber): array
    {
        try {
            $accessToken = $this->getAirtelAccessToken();
            
            $transactionId = 'AIRTEL_' . time() . '_' . rand(1000, 9999);

            $response = Http::withToken($accessToken)
                ->post($this->airtelConfig['base_url'] . '/merchant/v1/payments/', [
                    'reference' => $transactionId,
                    'subscriber' => [
                        'country' => 'KE',
                        'currency' => 'KES',
                        'msisdn' => $this->formatPhoneNumber($phoneNumber, 'airtel'),
                    ],
                    'transaction' => [
                        'amount' => $amount,
                        'country' => 'KE',
                        'currency' => 'KES',
                        'id' => $transactionId,
                    ],
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Store pending transaction
                $transaction = Transaction::create([
                    'account_id' => $account->id,
                    'member_id' => $account->member_id,
                    'type' => Transaction::TYPE_DEPOSIT,
                    'amount' => $amount,
                    'description' => 'Airtel Money deposit - pending confirmation',
                    'reference_number' => $this->generateReferenceNumber(),
                    'status' => Transaction::STATUS_PENDING,
                    'balance_before' => $account->balance,
                    'balance_after' => $account->balance,
                    'metadata' => [
                        'payment_method' => 'airtel',
                        'phone_number' => $phoneNumber,
                        'airtel_transaction_id' => $transactionId,
                        'airtel_reference' => $data['data']['transaction']['id'] ?? null,
                    ],
                ]);

                return [
                    'success' => true,
                    'message' => 'Payment initiated. Please complete on your phone.',
                    'transaction_id' => $transaction->id,
                    'airtel_transaction_id' => $transactionId,
                ];
            } else {
                throw new \Exception('Airtel Money API Error: ' . $response->body());
            }

        } catch (\Exception $e) {
            Log::error('Airtel Money payment initiation failed', [
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
     * Initiate T-Kash payment
     */
    public function initiateTkashPayment(Account $account, float $amount, string $phoneNumber): array
    {
        try {
            $transactionId = 'TKASH_' . time() . '_' . rand(1000, 9999);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->tkashConfig['api_key'],
                'Content-Type' => 'application/json',
            ])->post($this->tkashConfig['base_url'] . '/api/v1/payments/request', [
                'merchant_code' => $this->tkashConfig['merchant_code'],
                'transaction_id' => $transactionId,
                'amount' => $amount,
                'currency' => 'KES',
                'phone_number' => $this->formatPhoneNumber($phoneNumber, 'tkash'),
                'description' => 'SACCO Deposit - ' . $account->account_number,
                'callback_url' => route('webhooks.tkash.callback'),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Store pending transaction
                $transaction = Transaction::create([
                    'account_id' => $account->id,
                    'member_id' => $account->member_id,
                    'type' => Transaction::TYPE_DEPOSIT,
                    'amount' => $amount,
                    'description' => 'T-Kash deposit - pending confirmation',
                    'reference_number' => $this->generateReferenceNumber(),
                    'status' => Transaction::STATUS_PENDING,
                    'balance_before' => $account->balance,
                    'balance_after' => $account->balance,
                    'metadata' => [
                        'payment_method' => 'tkash',
                        'phone_number' => $phoneNumber,
                        'tkash_transaction_id' => $transactionId,
                        'tkash_reference' => $data['reference'] ?? null,
                    ],
                ]);

                return [
                    'success' => true,
                    'message' => 'Payment initiated. Please complete on your phone.',
                    'transaction_id' => $transaction->id,
                    'tkash_transaction_id' => $transactionId,
                ];
            } else {
                throw new \Exception('T-Kash API Error: ' . $response->body());
            }

        } catch (\Exception $e) {
            Log::error('T-Kash payment initiation failed', [
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
            switch ($provider) {
                case 'mpesa':
                    return $this->processMpesaConfirmation($webhookData);
                case 'airtel':
                    return $this->processAirtelConfirmation($webhookData);
                case 'tkash':
                    return $this->processTkashConfirmation($webhookData);
                default:
                    throw new \Exception('Unknown payment provider: ' . $provider);
            }
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

            // Non-zero: failed/cancelled/timeout
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
     * Get Airtel access token
     */
    private function getAirtelAccessToken(): string
    {
        $response = Http::post($this->airtelConfig['base_url'] . '/auth/oauth2/token', [
            'client_id' => $this->airtelConfig['client_id'],
            'client_secret' => $this->airtelConfig['client_secret'],
            'grant_type' => 'client_credentials',
        ]);

        if ($response->successful()) {
            return $response->json()['access_token'];
        }

        throw new \Exception('Failed to get Airtel access token');
    }

    /**
     * Format phone number for different providers
     */
    private function formatPhoneNumber(string $phoneNumber, string $provider = 'mpesa'): string
    {
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Convert to international format
        if (str_starts_with($phone, '0')) {
            $phone = '254' . substr($phone, 1);
        } elseif (str_starts_with($phone, '7') || str_starts_with($phone, '1')) {
            $phone = '254' . $phone;
        }

        // Provider-specific formatting
        switch ($provider) {
            case 'airtel':
            case 'tkash':
                return '+' . $phone;
            default: // mpesa
                return $phone;
        }
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

    /**
     * Process Airtel confirmation
     */
    private function processAirtelConfirmation(array $webhookData): bool
    {
        // Implementation for Airtel webhook processing
        // Similar to M-Pesa but with Airtel-specific fields
        return true;
    }

    /**
     * Process T-Kash confirmation
     */
    private function processTkashConfirmation(array $webhookData): bool
    {
        // Implementation for T-Kash webhook processing
        // Similar to M-Pesa but with T-Kash-specific fields
        return true;
    }
} 