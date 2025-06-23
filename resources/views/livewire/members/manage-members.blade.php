<?php

use App\Models\User;
use App\Models\Branch;
use App\Models\Role;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

new #[Layout('components.layouts.app')] class extends Component {
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $branchFilter = '';
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showViewModal = false;
    public $selectedMember = null;


    // Form fields
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $phone_number = '';
    public $id_number = '';
    public $address = '';
    public $branch_id = '';
    public $membership_status = 'active';
    public $member_number = '';

    public $memberStatuses = [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'suspended' => 'Suspended',
    ];



    public function with()
    {
        $members = User::query()
            ->when($this->search, fn($query) => 
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('member_number', 'like', '%' . $this->search . '%')
            )
            ->when($this->statusFilter, fn($query) => 
                $query->where('membership_status', $this->statusFilter)
            )
            ->when($this->branchFilter, fn($query) => 
                $query->where('branch_id', $this->branchFilter)
            )
            ->with(['branch', 'accounts', 'loans'])
            ->latest()
            ->paginate(10);

        $branches = Branch::where('status', 'active')->get();

        return [
            'members' => $members,
            'branches' => $branches,
            'canCreate' => true, // Simplified for now - can be enhanced with proper permission checks
            'canEdit' => true,
            'canDelete' => true,
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedBranchFilter()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function openEditModal($memberId)
    {
        $member = User::findOrFail($memberId);
        $this->selectedMember = $member;
        
        $this->name = $member->name;
        $this->email = $member->email;
        $this->phone_number = $member->phone_number;
        $this->id_number = $member->id_number;
        $this->address = $member->address;
        $this->branch_id = $member->branch_id;
        $this->membership_status = $member->membership_status ?? 'active';
        $this->member_number = $member->member_number;
        
        $this->showEditModal = true;
    }

    public function openViewModal($memberId)
    {
        $this->selectedMember = User::with(['branch', 'accounts', 'loans', 'transactions'])->findOrFail($memberId);
        $this->showViewModal = true;
    }

    public function createMember()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone_number' => 'required|string|max:20',
            'id_number' => 'required|string|max:20|unique:users,id_number',
            'address' => 'required|string|max:500',
            'branch_id' => 'required|exists:branches,id',
        ]);

        try {
            $member = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'phone_number' => $this->phone_number,
                'id_number' => $this->id_number,
                'address' => $this->address,
                'branch_id' => $this->branch_id,
                'membership_status' => 'active',
                'joining_date' => now(),
                'member_number' => $this->generateMemberNumber(),
            ]);

            // Member created successfully

            session()->flash('success', 'Member created successfully!');
            $this->resetForm();
            $this->showCreateModal = false;

        } catch (\Exception $e) {
            $this->addError('general', 'Failed to create member. Please try again.');
        }
    }

    public function updateMember()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->selectedMember->id)],
            'phone_number' => 'required|string|max:20',
            'id_number' => ['required', 'string', 'max:20', Rule::unique('users')->ignore($this->selectedMember->id)],
            'address' => 'required|string|max:500',
            'branch_id' => 'required|exists:branches,id',
            'membership_status' => 'required|in:active,inactive,suspended',
        ]);

        try {
            $this->selectedMember->update([
                'name' => $this->name,
                'email' => $this->email,
                'phone_number' => $this->phone_number,
                'id_number' => $this->id_number,
                'address' => $this->address,
                'branch_id' => $this->branch_id,
                'membership_status' => $this->membership_status,
            ]);

            session()->flash('success', 'Member updated successfully!');
            $this->resetForm();
            $this->showEditModal = false;

        } catch (\Exception $e) {
            $this->addError('general', 'Failed to update member. Please try again.');
        }
    }

    public function deleteMember($memberId)
    {
        try {
            $member = User::findOrFail($memberId);
            
            // Check if member has active loans or accounts with balance
            if ($member->loans()->whereIn('status', ['active', 'disbursed'])->exists()) {
                $this->addError('general', 'Cannot delete member with active loans.');
                return;
            }

            if ($member->accounts()->where('balance', '>', 0)->exists()) {
                $this->addError('general', 'Cannot delete member with account balances.');
                return;
            }

            $member->delete();
            session()->flash('success', 'Member deleted successfully!');

        } catch (\Exception $e) {
            $this->addError('general', 'Failed to delete member. Please try again.');
        }
    }

    private function generateMemberNumber()
    {
        $lastMember = User::whereNotNull('member_number')
            ->orderBy('member_number', 'desc')
            ->first();

        if ($lastMember && $lastMember->member_number) {
            $lastNumber = intval(substr($lastMember->member_number, 1));
            return 'M' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
        }

        return 'M000001';
    }

    private function resetForm()
    {
        $this->reset([
            'name', 'email', 'password', 'password_confirmation',
            'phone_number', 'id_number', 'address', 'branch_id',
            'membership_status', 'member_number'
        ]);
        $this->selectedMember = null;
    }

    public function closeModals()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showViewModal = false;
        $this->resetForm();
    }
}; ?>

<div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
    <!-- Header -->
    <div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                        {{ __('Member Management') }}
                    </h1>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                        {{ __('Manage SACCO members and their information') }}
                    </p>
                </div>
                
                @if($canCreate)
                    <flux:button wire:click="openCreateModal" variant="primary">
                        <flux:icon.plus class="w-4 h-4 mr-1" />
                        {{ __('Add Member') }}
                    </flux:button>
                @endif
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="px-4 sm:px-6 lg:px-8 py-4">
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <flux:input 
                    wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('Search members...') }}"
                    icon="magnifying-glass" />
                
                <flux:select wire:model.live="statusFilter">
                    <option value="">{{ __('All Statuses') }}</option>
                    @foreach($memberStatuses as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </flux:select>

                <flux:select wire:model.live="branchFilter">
                    <option value="">{{ __('All Branches') }}</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </flux:select>

                <flux:button wire:click="$set('search', '')" variant="outline">
                    {{ __('Clear Filters') }}
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Members Table -->
    <div class="px-4 sm:px-6 lg:px-8 pb-8">
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                {{ __('Member') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                {{ __('Contact') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                {{ __('Branch') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                {{ __('Status') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                {{ __('Accounts') }}
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                {{ __('Actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse($members as $member)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-medium">
                                                {{ substr($member->name, 0, 2) }}
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                                {{ $member->name }}
                                            </div>
                                            <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                                {{ $member->member_number ?? 'No member number' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-zinc-900 dark:text-zinc-100">{{ $member->email }}</div>
                                    <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $member->phone_number }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-100">
                                    {{ $member->branch->name ?? 'No branch' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ ($member->membership_status ?? 'active') === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                        {{ ($member->membership_status ?? 'active') === 'inactive' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' : '' }}
                                        {{ ($member->membership_status ?? 'active') === 'suspended' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}">
                                        {{ ucfirst($member->membership_status ?? 'active') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ $member->accounts->count() }} accounts
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <flux:button 
                                            wire:click="openViewModal({{ $member->id }})"
                                            variant="ghost" 
                                            size="sm">
                                            {{ __('View') }}
                                        </flux:button>
                                        
                                        @if($canEdit)
                                            <flux:button 
                                                wire:click="openEditModal({{ $member->id }})"
                                                variant="outline" 
                                                size="sm">
                                                {{ __('Edit') }}
                                            </flux:button>
                                        @endif

                                        @if($canDelete)
                                            <flux:button 
                                                wire:click="deleteMember({{ $member->id }})"
                                                wire:confirm="Are you sure you want to delete this member?"
                                                variant="danger" 
                                                size="sm">
                                                {{ __('Delete') }}
                                            </flux:button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-zinc-500 dark:text-zinc-400">
                                    {{ __('No members found.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
                {{ $members->links() }}
            </div>
        </div>
    </div>

    <!-- Create Member Modal -->
    <flux:modal name="create-member" :show="$showCreateModal">
        <div class="space-y-6">
            <div class="border-b border-zinc-200 dark:border-zinc-700 pb-4">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ __('Create New Member') }}</h3>
            </div>

            <form wire:submit="createMember" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>{{ __('Full Name') }}</flux:label>
                        <flux:input wire:model="name" required />
                        <flux:error name="name" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Email Address') }}</flux:label>
                        <flux:input wire:model="email" type="email" required />
                        <flux:error name="email" />
                    </flux:field>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>{{ __('Password') }}</flux:label>
                        <flux:input wire:model="password" type="password" required />
                        <flux:error name="password" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Confirm Password') }}</flux:label>
                        <flux:input wire:model="password_confirmation" type="password" required />
                    </flux:field>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>{{ __('Phone Number') }}</flux:label>
                        <flux:input wire:model="phone_number" required />
                        <flux:error name="phone_number" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('ID Number') }}</flux:label>
                        <flux:input wire:model="id_number" required />
                        <flux:error name="id_number" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>{{ __('Address') }}</flux:label>
                    <flux:textarea wire:model="address" rows="3" required />
                    <flux:error name="address" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Branch') }}</flux:label>
                    <flux:select wire:model="branch_id" required>
                        <option value="">{{ __('Select branch...') }}</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </flux:select>
                    <flux:error name="branch_id" />
                </flux:field>

                <div class="flex justify-end space-x-3 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:button wire:click="closeModals" variant="outline">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ __('Create Member') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Edit Member Modal -->
    <flux:modal name="edit-member" :show="$showEditModal">
        <div class="space-y-6">
            <div class="border-b border-zinc-200 dark:border-zinc-700 pb-4">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ __('Edit Member') }}</h3>
            </div>

            <form wire:submit="updateMember" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>{{ __('Full Name') }}</flux:label>
                        <flux:input wire:model="name" required />
                        <flux:error name="name" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Email Address') }}</flux:label>
                        <flux:input wire:model="email" type="email" required />
                        <flux:error name="email" />
                    </flux:field>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>{{ __('Phone Number') }}</flux:label>
                        <flux:input wire:model="phone_number" required />
                        <flux:error name="phone_number" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('ID Number') }}</flux:label>
                        <flux:input wire:model="id_number" required />
                        <flux:error name="id_number" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>{{ __('Address') }}</flux:label>
                    <flux:textarea wire:model="address" rows="3" required />
                    <flux:error name="address" />
                </flux:field>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>{{ __('Branch') }}</flux:label>
                        <flux:select wire:model="branch_id" required>
                            <option value="">{{ __('Select branch...') }}</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </flux:select>
                        <flux:error name="branch_id" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Status') }}</flux:label>
                        <flux:select wire:model="membership_status" required>
                            @foreach($memberStatuses as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:button wire:click="closeModals" variant="outline">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ __('Update Member') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- View Member Modal -->
    @if($selectedMember && $showViewModal)
        <flux:modal name="view-member" :show="$showViewModal">
            <div class="space-y-6">
                <div class="border-b border-zinc-200 dark:border-zinc-700 pb-4">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">
                        {{ __('Member Details') }} - {{ $selectedMember->name }}
                    </h3>
                </div>
                <div class="space-y-6">
                    <!-- Personal Information -->
                    <div>
                        <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-3">
                            {{ __('Personal Information') }}
                        </h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-zinc-500 dark:text-zinc-400">Name:</span>
                                <div class="font-medium">{{ $selectedMember->name }}</div>
                            </div>
                            <div>
                                <span class="text-zinc-500 dark:text-zinc-400">Member Number:</span>
                                <div class="font-medium">{{ $selectedMember->member_number ?? 'Not assigned' }}</div>
                            </div>
                            <div>
                                <span class="text-zinc-500 dark:text-zinc-400">Email:</span>
                                <div class="font-medium">{{ $selectedMember->email }}</div>
                            </div>
                            <div>
                                <span class="text-zinc-500 dark:text-zinc-400">Phone:</span>
                                <div class="font-medium">{{ $selectedMember->phone_number }}</div>
                            </div>
                            <div class="col-span-2">
                                <span class="text-zinc-500 dark:text-zinc-400">Address:</span>
                                <div class="font-medium">{{ $selectedMember->address }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Account Summary -->
                    @if($selectedMember->accounts->count() > 0)
                        <div>
                            <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-3">
                                {{ __('Account Summary') }}
                            </h3>
                            <div class="grid grid-cols-3 gap-4">
                                @foreach($selectedMember->accounts as $account)
                                    <div class="bg-zinc-50 dark:bg-zinc-700 p-3 rounded-lg">
                                        <div class="text-sm font-medium">{{ ucfirst($account->account_type) }}</div>
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $account->account_number }}</div>
                                        <div class="text-lg font-bold text-green-600 dark:text-green-400">
                                            KES {{ number_format($account->balance) }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Loan Summary -->
                    @if($selectedMember->loans->count() > 0)
                        <div>
                            <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-3">
                                {{ __('Active Loans') }}
                            </h3>
                            <div class="space-y-2">
                                @foreach($selectedMember->loans->where('status', 'active') as $loan)
                                    <div class="bg-zinc-50 dark:bg-zinc-700 p-3 rounded-lg flex justify-between items-center">
                                        <div>
                                            <div class="font-medium">KES {{ number_format($loan->amount) }}</div>
                                            <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $loan->loanType->name ?? 'Unknown Type' }}</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm">Paid: KES {{ number_format($loan->amount_paid) }}</div>
                                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $loan->status }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div class="flex justify-end pt-4 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:button wire:click="closeModals" variant="outline">
                        {{ __('Close') }}
                    </flux:button>
                </div>
            </div>
        </flux:modal>
    @endif


</div> 