<?php

namespace App\Livewire;

use App\Models\Account;
use App\Services\MobileMoneyService;
use App\Traits\HasPermissions;
use App\Traits\WithLoadingStates;
use App\Traits\WithFormValidation;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class MobileMoneyPayment extends Component
{
    use HasPermissions, WithLoadingStates, WithFormValidation;

    public $accounts = [];
    public $selectedAccountId = '';
    public $amount = '';
    public $phoneNumber = '';
    public $provider = 'mpesa'; // mpesa, airtel, tkash
    public $paymentStatus = '';
    public $transactionId = '';
    public $checkoutRequestId = '';
    public $showSuccessModal = false;
    public $showErrorModal = false;
    public $errorMessage = '';
    public $successMessage = '';

    protected $listeners = [
        'paymentConfirmed' => 'handlePaymentConfirmation',
        'paymentFailed' => 'handlePaymentFailure',
    ];

    public function mount()
    {
        $this->loadUserAccounts();
        $this->phoneNumber = Auth::user()->phone_number ?? '';
    }

    protected function getRealTimeValidationRules(): array
    {
        return [
            'selectedAccountId' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:10|max:500000',
            'phoneNumber' => 'required|string|min:10|max:15',
            'provider' => 'required|in:mpesa,airtel,tkash',
        ];
    }

    public function updated($field)
    {
        $this->validateField($field);
    }

    public function loadUserAccounts()
    {
        $user = Auth::user();
        
        if ($user->hasRole('member')) {
            $this->accounts = $user->accounts()
                ->where('status', 'active')
                ->get()
                ->map(function ($account) {
                    return [
                        'id' => $account->id,
                        'label' => $account->getDisplayName() . ' - ' . $account->account_number,
                        'balance' => $account->balance,
                    ];
                });
        } else {
            // For staff, show dropdown to select member first
            $this->accounts = collect();
        }
    }

    public function initiatePayment()
    {
        $this->validate($this->getRealTimeValidationRules());

        return $this->handleLoadingState('payment', function () {
            $account = Account::findOrFail($this->selectedAccountId);
            
            // Check if user has permission to deposit to this account
            if (Auth::user()->hasRole('member') && $account->member_id !== Auth::id()) {
                throw new \Exception('You can only deposit to your own accounts.');
            }

            $mobileMoneyService = app(MobileMoneyService::class);
            
            $result = match ($this->provider) {
                'mpesa' => $mobileMoneyService->initiateMpesaPayment($account, $this->amount, $this->phoneNumber),
                'airtel' => $mobileMoneyService->initiateAirtelPayment($account, $this->amount, $this->phoneNumber),
                'tkash' => $mobileMoneyService->initiateTkashPayment($account, $this->amount, $this->phoneNumber),
                default => ['success' => false, 'message' => 'Invalid payment provider']
            };

            if ($result['success']) {
                $this->transactionId = $result['transaction_id'];
                $this->checkoutRequestId = $result['checkout_request_id'] ?? $result['airtel_transaction_id'] ?? $result['tkash_transaction_id'] ?? '';
                $this->paymentStatus = 'pending';
                $this->successMessage = $result['message'];
                
                // Start polling for payment status
                $this->dispatch('start-payment-polling', [
                    'transactionId' => $this->transactionId,
                    'provider' => $this->provider
                ]);
                
                $this->dispatch('payment-initiated', [
                    'message' => $result['message'],
                    'provider' => $this->getProviderName()
                ]);
            } else {
                $this->errorMessage = $result['message'];
                $this->showErrorModal = true;
            }
        });
    }

    public function getProviderName(): string
    {
        return match ($this->provider) {
            'mpesa' => 'M-Pesa',
            'airtel' => 'Airtel Money',
            'tkash' => 'T-Kash',
            default => 'Mobile Money'
        };
    }

    public function getProviderColor(): string
    {
        return match ($this->provider) {
            'mpesa' => 'green',
            'airtel' => 'red',
            'tkash' => 'orange',
            default => 'gray'
        };
    }

    public function getProviderIcon(): string
    {
        return match ($this->provider) {
            'mpesa' => '💚', // M-Pesa green
            'airtel' => '❤️', // Airtel red
            'tkash' => '🧡', // T-Kash orange
            default => '📱'
        };
    }

    public function handlePaymentConfirmation($data)
    {
        $this->paymentStatus = 'completed';
        $this->successMessage = 'Payment of KES ' . number_format($this->amount, 2) . ' completed successfully!';
        $this->showSuccessModal = true;
        
        // Clear form
        $this->reset(['amount', 'selectedAccountId', 'transactionId', 'checkoutRequestId']);
        
        // Refresh accounts to show updated balance
        $this->loadUserAccounts();
        
        $this->dispatch('payment-completed', [
            'message' => $this->successMessage,
            'amount' => $this->amount
        ]);
    }

    public function handlePaymentFailure($data)
    {
        $this->paymentStatus = 'failed';
        $this->errorMessage = $data['message'] ?? 'Payment failed. Please try again.';
        $this->showErrorModal = true;
        
        $this->dispatch('payment-failed', [
            'message' => $this->errorMessage
        ]);
    }

    public function resetPayment()
    {
        $this->reset([
            'paymentStatus', 'transactionId', 'checkoutRequestId', 
            'showSuccessModal', 'showErrorModal', 'errorMessage', 'successMessage'
        ]);
    }

    public function render()
    {
        return view('livewire.mobile-money-payment', [
            'isProcessing' => $this->paymentStatus === 'pending',
            'canProcess' => $this->can('process-transactions') || Auth::user()->hasRole('member'),
        ]);
    }
} 