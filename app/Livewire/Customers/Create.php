<?php

namespace App\Livewire\Customers;

use App\Application\UseCases\CreateCustomer;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use App\Domain\Interfaces\UserRepository;
use App\Domain\Interfaces\BranchRepository;
use App\Domain\Entities\User;

class Create extends Component
{
    #[Validate('required|string|max:255')]
    public $name = '';

    #[Validate('required|array|min:1')]
    public $mobileNumbers = [''];

    #[Validate('required|in:male,female')]
    public $gender = '';

    #[Validate('required|numeric|min:0')]
    public $balance = 0.00;

    #[Validate('boolean')]
    public $is_client = false; // Default: customer does not have a wallet

    #[Validate('nullable|exists:users,id')]
    public $agent_id = null;

    #[Validate('required|exists:branches,id')]
    public $branch_id = '';

    public $agents = [];
    public $branches = [];

    // Status tracking
    public $showStatusPage = false;
    public $createdCustomer = null;
    public $creationStatus = null; // 'success', 'error'
    public $statusMessage = '';

    public $useInitialBalance = false;

    private CreateCustomer $createCustomerUseCase;
    private UserRepository $userRepository;
    private BranchRepository $branchRepository;

    public function boot(
        CreateCustomer $createCustomerUseCase,
        UserRepository $userRepository,
        BranchRepository $branchRepository
    ) {
        $this->createCustomerUseCase = $createCustomerUseCase;
        $this->userRepository = $userRepository;
        $this->branchRepository = $branchRepository;
    }

    public function mount()
    {
        $user = Auth::user();
        // Remove the abort for agents, rely on Gate authorization
        Gate::authorize('manage-customers');
        $this->agents = User::role('agent')->get();
        $this->branches = $this->branchRepository->all();
    }

    public function addMobileNumber()
    {
        $this->mobileNumbers[] = '';
    }

    public function removeMobileNumber($index)
    {
        unset($this->mobileNumbers[$index]);
        $this->mobileNumbers = array_values($this->mobileNumbers);
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'mobileNumbers' => 'required|array|min:1',
            'mobileNumbers.*' => 'required|digits:11|distinct',
            'gender' => 'required|in:male,female',
            'balance' => $this->useInitialBalance ? 'required|numeric|min:0' : 'nullable|numeric|min:0',
            'is_client' => 'required|boolean',
            'agent_id' => 'required|exists:users,id',
            'branch_id' => 'required|exists:branches,id',
        ];
    }

    public function createCustomer()
    {
        $this->validate();

        // Custom validation for unique mobile numbers
        foreach ($this->mobileNumbers as $number) {
            if (\App\Models\Domain\Entities\CustomerMobileNumber::where('mobile_number', $number)->exists()) {
                $this->addError('mobileNumbers', 'The mobile number ' . $number . ' is already used. Please enter another number.');
                return;
            }
        }

        try {
            // Use the first mobile number as the primary
            $primaryMobile = $this->mobileNumbers[0];
            $balance = $this->useInitialBalance ? $this->balance : 0.00;
            $customer = $this->createCustomerUseCase->execute(
                $this->name,
                $primaryMobile,
                null, // customerCode is auto-generated
                $this->gender,
                $balance,
                $this->is_client,
                $this->agent_id,
                $this->branch_id
            );

            // Save all mobile numbers
            foreach ($this->mobileNumbers as $number) {
                $customer->mobileNumbers()->create(['mobile_number' => $number]);
            }
            // Set created_by
            $customer->created_by = Auth::id();
            $customer->save();

            // Set success status
            $this->createdCustomer = $customer->fresh(['mobileNumbers', 'branch', 'agent', 'createdBy']); // Reload with relationships
            $this->creationStatus = 'success';
            $this->statusMessage = 'Customer created successfully!';
            $this->showStatusPage = true;
        } catch (\Exception $e) {
            // Set error status
            $this->creationStatus = 'error';
            $this->statusMessage = 'Failed to create customer: ' . $e->getMessage();
            $this->showStatusPage = true;
        }
    }

    public function backToForm()
    {
        $this->showStatusPage = false;
        $this->createdCustomer = null;
        $this->creationStatus = null;
        $this->statusMessage = '';
        $this->reset(['name', 'mobileNumbers', 'gender', 'balance', 'is_client', 'agent_id', 'branch_id']);
        $this->mobileNumbers = [''];
    }

    public function goToCustomersList()
    {
        $this->redirect(route('customers.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.customers.create');
    }
}
