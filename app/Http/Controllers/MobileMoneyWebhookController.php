<?php

namespace App\Http\Controllers;

use App\Services\MobileMoneyService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class MobileMoneyWebhookController extends Controller
{
    private MobileMoneyService $mobileMoneyService;

    public function __construct(MobileMoneyService $mobileMoneyService)
    {
        $this->mobileMoneyService = $mobileMoneyService;
    }

    /**
     * Handle M-Pesa STK Push callback
     */
    public function mpesaCallback(Request $request): JsonResponse
    {
        try {
            $webhookData = $request->all();
            
            Log::info('M-Pesa callback received', ['data' => $webhookData]);

            $success = $this->mobileMoneyService->processPaymentConfirmation('mpesa', $webhookData);

            return response()->json([
                'ResultCode' => $success ? 0 : 1,
                'ResultDesc' => $success ? 'Success' : 'Failed to process payment'
            ]);

        } catch (\Exception $e) {
            Log::error('M-Pesa callback error', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'ResultCode' => 1,
                'ResultDesc' => 'Internal server error'
            ]);
        }
    }

    /**
     * Handle Airtel Money callback
     */
    public function airtelCallback(Request $request): JsonResponse
    {
        try {
            $webhookData = $request->all();
            
            Log::info('Airtel callback received', ['data' => $webhookData]);

            $success = $this->mobileMoneyService->processPaymentConfirmation('airtel', $webhookData);

            return response()->json([
                'status' => $success ? 'success' : 'error',
                'message' => $success ? 'Payment processed successfully' : 'Failed to process payment'
            ]);

        } catch (\Exception $e) {
            Log::error('Airtel callback error', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Internal server error'
            ]);
        }
    }

    /**
     * Handle T-Kash callback
     */
    public function tkashCallback(Request $request): JsonResponse
    {
        try {
            $webhookData = $request->all();
            
            Log::info('T-Kash callback received', ['data' => $webhookData]);

            $success = $this->mobileMoneyService->processPaymentConfirmation('tkash', $webhookData);

            return response()->json([
                'success' => $success,
                'message' => $success ? 'Payment processed successfully' : 'Failed to process payment'
            ]);

        } catch (\Exception $e) {
            Log::error('T-Kash callback error', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ]);
        }
    }
} 