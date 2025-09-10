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
    public $viewMode = 'list'; // 'list' or 'grid'
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
        'inactive' => 'Inactive (Pending)',
        'suspended' => 'Suspended',
    ];

    public function with()
    {
        $query = User::query()
            ->when($this->search, fn($q) => 
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('member_number', 'like', '%' . $this->search . '%')
            )
            ->when($this->statusFilter, fn($q) => 
                $this->statusFilter === 'inactive' 
                    ? $q->where(function($query) {
                        $query->where('membership_status', 'inactive')
                              ->orWhereNull('membership_status');
                    })
                    : $q->where('membership_status', $this->statusFilter)
            )
            ->when($this->branchFilter, fn($q) => 
                $q->where('branch_id', $this->branchFilter)
            )
            ->with(['branch', 'accounts', 'loans']);

        $members = $query->latest()->paginate(15);
        $branches = Branch::where('status', 'active')->get();

        // Stats for dashboard
        $totalMembers = User::count();
        $activeMembers = User::where('membership_status', 'active')->count();
        $pendingMembers = User::where(function($q) {
            $q->where('membership_status', 'inactive')
              ->orWhereNull('membership_status');
        })->count();
        $suspendedMembers = User::where('membership_status', 'suspended')->count();

        return [
            'members' => $members,
            'branches' => $branches,
            'totalMembers' => $totalMembers,
            'activeMembers' => $activeMembers,
            'pendingMembers' => $pendingMembers,
            'suspendedMembers' => $suspendedMembers,
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

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
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
        do {
            $memberNumber = 'MEM' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (User::where('member_number', $memberNumber)->exists());

        return $memberNumber;
    }

    private function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->phone_number = '';
        $this->id_number = '';
        $this->address = '';
        $this->branch_id = '';
        $this->membership_status = 'active';
        $this->member_number = '';
        $this->selectedMember = null;
    }

    public function closeModals()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showViewModal = false;
        $this->resetForm();
    }

    public function verifyUser($userId, $status = 'active')
    {
        $user = User::findOrFail($userId);
        $this->authorize('update', $user);
        
        $user->update(['membership_status' => $status]);

        session()->flash('success', "User '{$user->name}' has been {$status}.");
    }

    public function suspendUser($userId)
    {
        $this->verifyUser($userId, 'suspended');
    }
}; ?>

<div>
    <!-- Header -->
    <div class="mb-8">
        <flux:heading size="xl">Members</flux:heading>
        <flux:subheading>Manage SACCO members and their accounts</flux:subheading>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">Total Members</flux:subheading>
                    <flux:heading size="lg" class="!text-zinc-900 dark:!text-zinc-100">{{ number_format($totalMembers) }}</flux:heading>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                    <flux:icon.users class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">Active</flux:subheading>
                    <flux:heading size="lg" class="!text-green-600 dark:!text-green-400">{{ number_format($activeMembers) }}</flux:heading>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900/20 rounded-lg">
                    <flux:icon.check-circle class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">Pending</flux:subheading>
                    <flux:heading size="lg" class="!text-amber-600 dark:!text-amber-400">{{ number_format($pendingMembers) }}</flux:heading>
                </div>
                <div class="p-3 bg-amber-100 dark:bg-amber-900/20 rounded-lg">
                    <flux:icon.clock class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <flux:subheading class="!text-zinc-600 dark:!text-zinc-400">Suspended</flux:subheading>
                    <flux:heading size="lg" class="!text-red-600 dark:!text-red-400">{{ number_format($suspendedMembers) }}</flux:heading>
                </div>
                <div class="p-3 bg-red-100 dark:bg-red-900/20 rounded-lg">
                    <flux:icon.x-circle class="w-6 h-6 text-red-600 dark:text-red-400" />
                </div>
            </div>
        </div>
    </div>

    <!-- Controls -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <!-- Search and Filters -->
            <div class="flex flex-col sm:flex-row gap-4 flex-1">
                <div class="flex-1">
                    <flux:input 
                        wire:model.live="search" 
                        placeholder="Search members..." 
                        icon="magnifying-glass"
                    />
                </div>
                
                <flux:select wire:model.live="statusFilter" placeholder="All Status">
                    <option value="">All Status</option>
                    @foreach($memberStatuses as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </flux:select>

                <flux:select wire:model.live="branchFilter" placeholder="All Branches">
                    <option value="">All Branches</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </flux:select>
            </div>

            <!-- View Toggle and Add Button -->
            <div class="flex items-center gap-3">
                <!-- View Mode Toggle -->
                <div class="flex rounded-lg border border-zinc-200 dark:border-zinc-600 p-1">
                    <flux:button 
                        variant="{{ $viewMode === 'list' ? 'primary' : 'ghost' }}" 
                        size="sm"
                        wire:click="setViewMode('list')"
                        icon="list-bullet"
                    />
                    <flux:button 
                        variant="{{ $viewMode === 'grid' ? 'primary' : 'ghost' }}" 
                        size="sm"
                        wire:click="setViewMode('grid')"
                        icon="squares-2x2"
                    />
                </div>

                <flux:button variant="primary" wire:click="openCreateModal" icon="plus">
                    Add Member
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Members Display -->
    @if($members->count())
        @if($viewMode === 'list')
            <!-- List View -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-zinc-50 dark:bg-zinc-700 border-b border-zinc-200 dark:border-zinc-600">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-medium text-zinc-900 dark:text-zinc-100">Member</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-zinc-900 dark:text-zinc-100">Contact</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-zinc-900 dark:text-zinc-100">Branch</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-zinc-900 dark:text-zinc-100">Status</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-zinc-900 dark:text-zinc-100">Accounts</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-zinc-900 dark:text-zinc-100">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-600">
                        @foreach($members as $member)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                            {{ substr($member->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $member->name }}</div>
                                            <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $member->member_number }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-zinc-900 dark:text-zinc-100">{{ $member->email }}</div>
                                    <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $member->phone_number }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-zinc-900 dark:text-zinc-100">{{ $member->branch?->name ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <flux:badge 
                                        variant="{{ $member->membership_status === 'active' ? 'lime' : ($member->membership_status === 'suspended' ? 'red' : 'amber') }}"
                                    >
                                        {{ ucfirst($member->membership_status ?? 'inactive') }}
                                    </flux:badge>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-zinc-900 dark:text-zinc-100">{{ $member->accounts->count() }} account(s)</div>
                                    <div class="text-sm text-zinc-500 dark:text-zinc-400">KES {{ number_format($member->accounts->sum('balance'), 2) }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <flux:dropdown align="end">
                                        <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                        
                                        <flux:menu>
                                            <flux:menu.item wire:click="openViewModal({{ $member->id }})" icon="eye">
                                                View Details
                                            </flux:menu.item>
                                            <flux:menu.item wire:click="openEditModal({{ $member->id }})" icon="pencil">
                                                Edit Member
                                            </flux:menu.item>
                                            @if($member->membership_status === 'inactive' || $member->membership_status === null)
                                                <flux:menu.item wire:click="verifyUser({{ $member->id }})" icon="check-circle">
                                                    Verify
                                                </flux:menu.item>
                                            @endif
                                            @if($member->membership_status === 'active')
                                                <flux:menu.item wire:click="suspendUser({{ $member->id }})" icon="x-circle" variant="danger">
                                                    Suspend
                                                </flux:menu.item>
                                            @endif
                                            @if($member->membership_status === 'suspended')
                                                <flux:menu.item wire:click="verifyUser({{ $member->id }})" icon="arrow-path">
                                                    Reactivate
                                                </flux:menu.item>
                                            @endif
                                            <flux:menu.separator />
                                            <flux:menu.item 
                                                wire:click="deleteMember({{ $member->id }})"
                                                wire:confirm="Are you sure you want to delete this member?"
                                                icon="trash"
                                                variant="danger"
                                            >
                                                Delete
                                            </flux:menu.item>
                                        </flux:menu>
                                    </flux:dropdown>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <!-- Grid View -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($members as $member)
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 hover:shadow-lg dark:hover:shadow-zinc-900/25 transition-shadow">
                        <!-- Member Header -->
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                                {{ substr($member->name, 0, 2) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-zinc-900 dark:text-zinc-100 truncate">{{ $member->name }}</div>
                                <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $member->member_number }}</div>
                            </div>
                        </div>

                        <!-- Member Details -->
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-300">
                                <flux:icon.envelope class="w-4 h-4" />
                                <span class="truncate">{{ $member->email }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-300">
                                <flux:icon.phone class="w-4 h-4" />
                                <span>{{ $member->phone_number }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-300">
                                <flux:icon.building-office class="w-4 h-4" />
                                <span>{{ $member->branch?->name ?? 'N/A' }}</span>
                            </div>
                        </div>

                        <!-- Status and Stats -->
                        <div class="flex items-center justify-between mb-4">
                            <flux:badge 
                                variant="{{ $member->membership_status === 'active' ? 'lime' : ($member->membership_status === 'suspended' ? 'red' : 'amber') }}"
                            >
                                {{ ucfirst($member->membership_status ?? 'inactive') }}
                            </flux:badge>
                            <div class="text-right">
                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $member->accounts->count() }} accounts</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">KES {{ number_format($member->accounts->sum('balance'), 2) }}</div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-2">
                            <flux:button variant="outline" size="sm" wire:click="openViewModal({{ $member->id }})" class="flex-1">
                                View
                            </flux:button>
                            <flux:button variant="outline" size="sm" wire:click="openEditModal({{ $member->id }})" class="flex-1">
                                Edit
                            </flux:button>
                            @if($member->membership_status === 'inactive' || $member->membership_status === null)
                                <flux:button variant="primary" size="sm" wire:click="verifyUser({{ $member->id }})" class="flex-1">
                                    Verify
                                </flux:button>
                            @elseif($member->membership_status === 'active')
                                <flux:button variant="danger" size="sm" wire:click="suspendUser({{ $member->id }})" class="flex-1">
                                    Suspend
                                </flux:button>
                            @elseif($member->membership_status === 'suspended')
                                <flux:button variant="primary" size="sm" wire:click="verifyUser({{ $member->id }})" class="flex-1">
                                    Reactivate
                                </flux:button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Pagination -->
        <div class="mt-6">
            {{ $members->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-12 text-center">
            <div class="w-12 h-12 bg-zinc-100 dark:bg-zinc-700 rounded-xl flex items-center justify-center mx-auto mb-4">
                <flux:icon.users class="w-6 h-6 text-zinc-400 dark:text-zinc-500" />
            </div>
            <flux:heading size="lg" class="mb-2 dark:text-zinc-100">No members found</flux:heading>
            <flux:subheading class="mb-6 dark:text-zinc-400">
                @if($search || $statusFilter || $branchFilter)
                    No members match your current filters.
                @else
                    Get started by adding your first member.
                @endif
            </flux:subheading>
            <flux:button variant="primary" wire:click="openCreateModal" icon="plus">
                Add Your First Member
            </flux:button>
        </div>
    @endif

    <!-- Create Member Modal -->
    <flux:modal wire:model="showCreateModal" class="md:w-2xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg" class="dark:text-zinc-100">Add New Member</flux:heading>
                <flux:subheading class="dark:text-zinc-400">Create a new SACCO member account</flux:subheading>
            </div>

            <form wire:submit="createMember" class="space-y-6">
                <!-- Personal Information -->
                <div class="space-y-4">
                    <flux:heading size="base" class="dark:text-zinc-100">Personal Information</flux:heading>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>Full Name</flux:label>
                            <flux:input wire:model="name" placeholder="Enter full name" />
                            <flux:error name="name" />
                        </flux:field>

                        <flux:field>
                            <flux:label>ID Number</flux:label>
                            <flux:input wire:model="id_number" placeholder="National ID number" />
                            <flux:error name="id_number" />
                        </flux:field>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="space-y-4">
                    <flux:heading size="base" class="dark:text-zinc-100">Contact Information</flux:heading>
                    
                    <flux:field>
                        <flux:label>Email Address</flux:label>
                        <flux:input wire:model="email" type="email" placeholder="member@example.com" />
                        <flux:error name="email" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Phone Number</flux:label>
                        <flux:input wire:model="phone_number" placeholder="+254 700 000 000" />
                        <flux:error name="phone_number" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Address</flux:label>
                        <flux:textarea wire:model="address" placeholder="Physical address" rows="3" />
                        <flux:error name="address" />
                    </flux:field>
                </div>

                <!-- Account Setup -->
                <div class="space-y-4">
                    <flux:heading size="base" class="dark:text-zinc-100">Account Setup</flux:heading>
                    
                    <flux:field>
                        <flux:label>Branch</flux:label>
                        <flux:select wire:model="branch_id" placeholder="Select a branch">
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </flux:select>
                        <flux:error name="branch_id" />
                    </flux:field>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>Password</flux:label>
                            <flux:input wire:model="password" type="password" placeholder="Secure password" />
                            <flux:error name="password" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Confirm Password</flux:label>
                            <flux:input wire:model="password_confirmation" type="password" placeholder="Confirm password" />
                            <flux:error name="password_confirmation" />
                        </flux:field>
                    </div>
                </div>

                @error('general')
                    <flux:error>{{ $message }}</flux:error>
                @enderror

                <div class="flex justify-end gap-3">
                    <flux:button type="button" variant="ghost" wire:click="closeModals">Cancel</flux:button>
                    <flux:button type="submit" variant="primary">Create Member</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Edit Member Modal -->
    <flux:modal wire:model="showEditModal" class="md:w-2xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg" class="dark:text-zinc-100">Edit Member</flux:heading>
                <flux:subheading class="dark:text-zinc-400">Update member information</flux:subheading>
            </div>

            <form wire:submit="updateMember" class="space-y-6">
                <!-- Personal Information -->
                <div class="space-y-4">
                    <flux:heading size="base" class="dark:text-zinc-100">Personal Information</flux:heading>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>Full Name</flux:label>
                            <flux:input wire:model="name" />
                            <flux:error name="name" />
                        </flux:field>

                        <flux:field>
                            <flux:label>ID Number</flux:label>
                            <flux:input wire:model="id_number" />
                            <flux:error name="id_number" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Member Number</flux:label>
                            <flux:input wire:model="member_number" readonly />
                        </flux:field>

                        <flux:field>
                            <flux:label>Status</flux:label>
                            <flux:select wire:model="membership_status">
                                @foreach($memberStatuses as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="membership_status" />
                        </flux:field>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="space-y-4">
                    <flux:heading size="base" class="dark:text-zinc-100">Contact Information</flux:heading>
                    
                    <flux:field>
                        <flux:label>Email Address</flux:label>
                        <flux:input wire:model="email" type="email" />
                        <flux:error name="email" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Phone Number</flux:label>
                        <flux:input wire:model="phone_number" />
                        <flux:error name="phone_number" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Address</flux:label>
                        <flux:textarea wire:model="address" rows="3" />
                        <flux:error name="address" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Branch</flux:label>
                        <flux:select wire:model="branch_id">
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </flux:select>
                        <flux:error name="branch_id" />
                    </flux:field>
                </div>

                @error('general')
                    <flux:error>{{ $message }}</flux:error>
                @enderror

                <div class="flex justify-end gap-3">
                    <flux:button type="button" variant="ghost" wire:click="closeModals">Cancel</flux:button>
                    <flux:button type="submit" variant="primary">Update Member</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- View Member Modal -->
    @if($selectedMember)
        <flux:modal wire:model="showViewModal" class="md:w-4xl">
            <div class="space-y-6">
                <!-- Header -->
                <div class="flex items-center gap-4 p-6 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg text-white">
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center text-2xl font-bold">
                        {{ substr($selectedMember->name, 0, 2) }}
                    </div>
                    <div>
                        <flux:heading size="xl" class="!text-white">{{ $selectedMember->name }}</flux:heading>
                        <div class="text-blue-100">{{ $selectedMember->member_number }}</div>
                        <div class="mt-2">
                            <flux:badge 
                                variant="{{ $selectedMember->membership_status === 'active' ? 'lime' : ($selectedMember->membership_status === 'suspended' ? 'red' : 'amber') }}"
                            >
                                {{ ucfirst($selectedMember->membership_status ?? 'inactive') }}
                            </flux:badge>
                        </div>
                    </div>
                </div>

                <!-- Details Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Personal Information -->
                    <div class="space-y-4">
                        <flux:heading size="base" class="dark:text-zinc-100">Personal Information</flux:heading>
                        <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 space-y-3">
                            <div class="flex justify-between">
                                <span class="text-zinc-600 dark:text-zinc-400">ID Number:</span>
                                <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $selectedMember->id_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-zinc-600 dark:text-zinc-400">Joining Date:</span>
                                <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $selectedMember->joining_date?->format('M d, Y') ?? $selectedMember->created_at->format('M d, Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-zinc-600 dark:text-zinc-400">Branch:</span>
                                <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $selectedMember->branch?->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="space-y-4">
                        <flux:heading size="base" class="dark:text-zinc-100">Contact Information</flux:heading>
                        <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 space-y-3">
                            <div class="flex justify-between">
                                <span class="text-zinc-600 dark:text-zinc-400">Email:</span>
                                <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $selectedMember->email }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-zinc-600 dark:text-zinc-400">Phone:</span>
                                <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $selectedMember->phone_number ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-zinc-600 dark:text-zinc-400">Address:</span>
                                <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $selectedMember->address ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Accounts -->
                @if($selectedMember->accounts && $selectedMember->accounts->count())
                    <div class="space-y-4">
                        <flux:heading size="base" class="dark:text-zinc-100">Accounts</flux:heading>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @foreach($selectedMember->accounts as $account)
                                <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-4">
                                    <div class="text-sm text-zinc-600 dark:text-zinc-400">{{ ucfirst($account->account_type) }} Account</div>
                                    <div class="text-xl font-semibold text-zinc-900 dark:text-zinc-100">KES {{ number_format($account->balance, 2) }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $account->account_number }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Actions -->
                <div class="flex justify-end gap-3">
                    <flux:button variant="ghost" wire:click="closeModals">Close</flux:button>
                    <flux:button variant="primary" wire:click="openEditModal({{ $selectedMember->id }})">
                        Edit Member
                    </flux:button>
                </div>
            </div>
        </flux:modal>
    @endif
</div> 