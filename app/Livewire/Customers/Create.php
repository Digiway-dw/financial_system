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

    #[Validate('required|array|min:1')] 
    public $mobileNumbers = [''];

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
        $user = auth()->user();
        if ($user->hasAnyRole(['agent', 'trainee'])) {
            abort(403, 'You do not have permission to create customers.');
        }
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
            'mobileNumbers.*' => 'required|string|max:20|distinct',
            'gender' => 'required|in:male,female',
            'balance' => 'required|numeric|min:0',
            'is_client' => 'boolean',
            'agent_id' => 'nullable|exists:users,id',
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
            $customer = $this->createCustomerUseCase->execute(
                $this->name,
                $primaryMobile,
                null, // customerCode is auto-generated
                $this->gender,
                $this->balance,
                $this->is_client,
                $this->agent_id,
                $this->branch_id
            );
            // Save all mobile numbers
            foreach ($this->mobileNumbers as $number) {
                $customer->mobileNumbers()->create(['mobile_number' => $number]);
            }
            session()->flash('message', 'Customer created successfully.');
            $this->redirect(route('customers.index'), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create customer: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.customers.create');
    }
}
