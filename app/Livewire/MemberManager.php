<?php

namespace App\Livewire;

use App\Models\Member;
use App\Traits\HasPermissions;
use App\Traits\WithLoadingStates;
use App\Traits\WithFormValidation;
use Livewire\Component;
use Livewire\WithPagination;

class MemberManager extends Component
{
    use WithPagination, HasPermissions, WithLoadingStates, WithFormValidation;

    public $search = '';
    public $selectedMember = null;
    public $showCreateModal = false;
    public $showEditModal = false;

    public $name = '';
    public $email = '';
    public $phone = '';
    public $address = '';

    protected $listeners = [
        'permission-denied' => 'handlePermissionDenied'
    ];

    public function mount()
    {
        $this->authorize('view-members');
        $this->initializeWithFormValidation();
    }

    protected function getRealTimeValidationRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => $this->selectedMember 
                ? 'required|email|unique:users,email,' . $this->selectedMember->id
                : 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
        ];
    }

    public function updated($field)
    {
        $this->validateField($field);
    }

    public function render()
    {
        return view('livewire.member-manager', [
            'members' => $this->handleLoadingState('fetch', function() {
                return Member::query()
                    ->when($this->search, fn($query) => $query->where('name', 'like', '%' . $this->search . '%'))
                    ->paginate(10);
            }),
            'canCreate' => $this->can('create-members'),
            'canEdit' => $this->can('edit-members'),
            'canDelete' => $this->can('delete-members'),
        ]);
    }

    public function openCreateModal()
    {
        $this->authorize('create-members');
        $this->reset(['name', 'email', 'phone', 'address']);
        $this->showCreateModal = true;
    }

    public function createMember()
    {
        $this->authorize('create-members');

        return $this->handleLoadingState('create', function() {
            $this->validate($this->getRealTimeValidationRules());

            Member::create([
                'name' => $this->name,
                'email' => $this->email,
                'phone_number' => $this->phone,
                'address' => $this->address,
                'membership_status' => 'active',
                'joining_date' => now(),
            ]);

            $this->showCreateModal = false;
            $this->dispatch('member-created', ['message' => 'Member created successfully!']);
        });
    }

    public function editMember($memberId)
    {
        $this->authorize('edit-members');
        
        $this->handleLoadingState('edit-load', function() use ($memberId) {
            $member = Member::findOrFail($memberId);
            $this->selectedMember = $member;
            $this->name = $member->name;
            $this->email = $member->email;
            $this->phone = $member->phone_number;
            $this->address = $member->address;
            $this->showEditModal = true;
        });
    }

    public function updateMember()
    {
        $this->authorize('edit-members');

        return $this->handleLoadingState('update', function() {
            $this->validate($this->getRealTimeValidationRules());

            $this->selectedMember->update([
                'name' => $this->name,
                'email' => $this->email,
                'phone_number' => $this->phone,
                'address' => $this->address,
            ]);

            $this->showEditModal = false;
            $this->dispatch('member-updated', ['message' => 'Member updated successfully!']);
        });
    }

    public function deleteMember($memberId)
    {
        $this->authorize('delete-members');
        
        return $this->handleLoadingState('delete', function() use ($memberId) {
            $member = Member::findOrFail($memberId);
            $member->delete();
            
            $this->dispatch('member-deleted', ['message' => 'Member deleted successfully!']);
        });
    }

    public function handlePermissionDenied($data)
    {
        $this->dispatch('show-error', [
            'message' => $data['message']
        ]);
    }
} 