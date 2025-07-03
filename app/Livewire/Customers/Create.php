<?php

namespace App\Livewire\Customers;

use App\Application\UseCases\CreateCustomer;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Gate;
use App\Domain\Interfaces\UserRepository;
use App\Domain\Interfaces\BranchRepository;
use App\Domain\Entities\User;

class Create extends Component
{
    #[Validate('required|string|max:255')] 
    public $name = '';

    #[Validate('required|string|max:20|unique:customers,mobile_number')] 
    public $mobileNumber = '';

    #[Validate('nullable|string|max:255')] 
    public $customerCode = '';

    #[Validate('required|in:male,female')] 
    public $gender = '';

    #[Validate('required|numeric|min:0')] 
    public $balance = 0.00;

    #[Validate('boolean')] 
    public $is_client = false;

    #[Validate('nullable|exists:users,id')] 
    public $agent_id = null;

    #[Validate('required|exists:branches,id')] 
    public $branch_id = '';

    public $agents = [];
    public $branches = [];

    private CreateCustomer $createCustomerUseCase;
    private UserRepository $userRepository;
    private BranchRepository $branchRepository;

    public function boot(
        CreateCustomer $createCustomerUseCase,
        UserRepository $userRepository,
        BranchRepository $branchRepository
    )
    {
        $this->createCustomerUseCase = $createCustomerUseCase;
        $this->userRepository = $userRepository;
        $this->branchRepository = $branchRepository;
    }

    public function mount()
    {
        Gate::authorize('manage-customers');
        $this->agents = User::role('agent')->get();
        $this->branches = $this->branchRepository->all();
    }

    public function createCustomer()
    {
        $this->validate();

        try {
            $this->createCustomerUseCase->execute(
                $this->name,
                $this->mobileNumber,
                $this->customerCode,
                $this->gender,
                $this->balance,
                $this->is_client,
                $this->agent_id,
                $this->branch_id
            );

            session()->flash('message', 'Customer created successfully.');
            $this->reset(); // Clear form fields after submission
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create customer: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.customers.create');
    }
}
