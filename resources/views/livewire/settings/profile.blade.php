<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;
    public string $name = '';
    public string $email = '';
    public string $phone_number = '';
    public string $id_number = '';
    public string $address = '';
    public string $city = '';
    public string $postal_code = '';
    public $profile_photo = null;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
        $this->phone_number = (string) (Auth::user()->phone_number ?? '');
        $this->id_number = (string) (Auth::user()->id_number ?? '');
        $this->address = (string) (Auth::user()->address ?? '');
        $this->city = (string) (Auth::user()->city ?? '');
        $this->postal_code = (string) (Auth::user()->postal_code ?? '');
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'lowercase', 'email', 'max:255',
                Rule::unique(User::class)->ignore($user->id)
            ],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'id_number' => ['nullable', 'string', 'max:20', Rule::unique(User::class, 'id_number')->ignore($user->id)],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:120'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'profile_photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Note: Storing profile photo is optional and depends on schema
        // If you have a column like profile_photo_path, you can uncomment this block
        // if ($this->profile_photo) {
        //     $path = $this->profile_photo->store('profile-photos', 'public');
        //     $user->profile_photo_path = $path;
        // }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Profile')" :subheading="__('Update your name and email address')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-8">
            <!-- Basic Information -->
            <div class="space-y-6">
                <flux:heading size="base">{{ __('Basic Information') }}</flux:heading>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Profile Photo -->
                    <div class="md:col-span-2">
                        <flux:label>{{ __('Profile Photo') }}</flux:label>
                        <div class="flex items-center gap-4">
                            <div class="h-12 w-12 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center overflow-hidden">
                                @if(method_exists(auth()->user(), 'profile_photo_url') && auth()->user()->profile_photo_url)
                                    <img src="{{ auth()->user()->profile_photo_url }}" alt="Avatar" class="h-12 w-12 object-cover">
                                @else
                                    <span class="text-base font-bold text-white">{{ auth()->user()->initials() }}</span>
                                @endif
                            </div>
                            <flux:input wire:model="profile_photo" type="file" accept="image/*" class="flex-1" />
                        </div>
                        <flux:error name="profile_photo" />
                    </div>
                    <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name" />
                    <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />
                    <flux:input wire:model="phone_number" :label="__('Phone Number')" type="text" autocomplete="tel" />
                    <flux:input wire:model="id_number" :label="__('ID Number')" type="text" />
                </div>

                @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail &&! auth()->user()->hasVerifiedEmail())
                    <div>
                        <flux:text class="mt-4">
                            {{ __('Your email address is unverified.') }}

                            <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                                {{ __('Click here to re-send the verification email.') }}
                            </flux:link>
                        </flux:text>

                        @if (session('status') === 'verification-link-sent')
                            <flux:text class="mt-2 font-medium !dark:text-green-400 !text-green-600">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </flux:text>
                        @endif
                    </div>
                @endif
                <!-- Membership Snapshot -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <flux:label>{{ __('Membership Status') }}</flux:label>
                        <div class="mt-1">
                            <flux:badge variant="{{ (auth()->user()->membership_status ?? 'active') === 'active' ? 'lime' : ((auth()->user()->membership_status ?? '') === 'suspended' ? 'red' : 'amber') }}">
                                {{ ucfirst(auth()->user()->membership_status ?? 'active') }}
                            </flux:badge>
                        </div>
                    </div>
                    <div>
                        <flux:label>{{ __('Member Since') }}</flux:label>
                        <div class="text-sm text-zinc-900 dark:text-zinc-100 mt-1">{{ optional(auth()->user()->joining_date ?? auth()->user()->created_at)->format('M Y') }}</div>
                    </div>
                    <div>
                        <flux:label>{{ __('Member Number') }}</flux:label>
                        <div class="font-mono text-sm text-zinc-900 dark:text-zinc-100 mt-1">{{ auth()->user()->member_number ?? str_pad(auth()->id(), 6, '0', STR_PAD_LEFT) }}</div>
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            <div class="space-y-6">
                <flux:heading size="base">{{ __('Address Information') }}</flux:heading>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:input wire:model="address" :label="__('Physical Address')" type="text" class="md:col-span-2" />
                    <flux:input wire:model="city" :label="__('City')" type="text" />
                    <flux:input wire:model="postal_code" :label="__('Postal Code')" type="text" />
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ __('Save') }}</flux:button>
                </div>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>

        <livewire:settings.delete-user-form />
    </x-settings.layout>
</section>
