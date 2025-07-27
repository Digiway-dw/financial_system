<?php

namespace App\Livewire\Customers;

use App\Application\UseCases\UpdateCustomer;
use App\Domain\Interfaces\CustomerRepository;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Gate;
use App\Domain\Interfaces\UserRepository;
use App\Domain\Interfaces\BranchRepository;
use App\Domain\Entities\User;
use Illuminate\Database\Eloquent\Collection;

class Edit extends Component
{
    public $customer;
    public $customerId;

    public $name = '';
    public $mobileNumbers = [];
    public $customerCode = '';
    public $gender = '';
    public $balance = 0;
    public $is_client = false;
    public $agent_id = null;
    public $branch_id = '';

    public Collection $agents;
    public Collection $branches;

    private CustomerRepository $customerRepository;
    private UpdateCustomer $updateCustomerUseCase;
    private UserRepository $userRepository;
    private BranchRepository $branchRepository;

    public function boot(
        CustomerRepository $customerRepository,
        UpdateCustomer $updateCustomerUseCase,
        UserRepository $userRepository,
        BranchRepository $branchRepository
    ) {
        $this->customerRepository = $customerRepository;
        $this->updateCustomerUseCase = $updateCustomerUseCase;
        $this->userRepository = $userRepository;
        $this->branchRepository = $branchRepository;
    }

    protected function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'mobileNumbers' => 'required|array|min:1',
            'mobileNumbers.*' => 'required|digits:11|distinct',
            'customerCode' => 'nullable|string|max:255',
            'gender' => 'required|in:male,female',
            'is_client' => 'boolean',
            'branch_id' => 'required|exists:branches,id',
        ];

        // Only admins can edit balance
        if ($this->canEditBalance()) {
            $rules['balance'] = 'required|integer|min:0';
        }

        return $rules;
    }

    public function canEditBalance(): bool
    {
        $user = auth()->user();
        return $user && $user->hasRole('admin');
    }

    public function canToggleWallet(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasRole('admin') || $user->hasRole('general_supervisor'));
    }

    public function canEditAgent(): bool
    {
        // No one can edit the agent field
        return false;
    }

    protected function validationAttributes(): array
    {
        return [
            'name' => 'Customer Name',
            'mobileNumber' => 'Mobile Number',
            'customerCode' => 'Customer Code',
            'gender' => 'Gender',
            'balance' => 'Balance',
            'is_client' => 'Is Client',
            'agent_id' => 'Agent',
            'branch_id' => 'Branch',
        ];
    }

    public function mount($customerId)
    {
        $user = auth()->user();
        if ($user->hasAnyRole(['agent', 'trainee'])) {
            abort(403, 'You are not authorized to edit customers.');
        }
        Gate::authorize('manage-customers');
        $this->customerId = $customerId;
        $this->customer = $this->customerRepository->findById($customerId);

        if ($this->customer) {
            $this->name = $this->customer->name;
            $this->mobileNumbers = $this->customer->mobileNumbers->pluck('mobile_number')->toArray();
            if (empty($this->mobileNumbers)) {
                $this->mobileNumbers = [$this->customer->mobile_number];
            }
            $this->customerCode = $this->customer->customer_code;
            $this->gender = $this->customer->gender;
            $this->balance = (int) $this->customer->balance;
            $this->is_client = $this->customer->is_client;
            $this->agent_id = $this->customer->agent_id;
            $this->branch_id = $this->customer->branch_id;

            $this->agents = User::role('agent')->get();
            $this->branches = $this->branchRepository->all();
        } else {
            abort(404);
        }
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

    public function updateCustomer()
    {
        $user = auth()->user();
        if ($user->hasAnyRole(['agent', 'trainee', 'auditor'])) {
            abort(403, 'You are not authorized to edit customers.');
        }
        $this->validate();

        try {
            // Use the first mobile number as the primary
            $primaryMobile = $this->mobileNumbers[0];
            
            // Preserve original values for restricted fields
            $originalCustomer = $this->customer;
            
            // Only admins can modify balance
            $balanceToUpdate = $this->canEditBalance() ? (int) $this->balance : (int) $originalCustomer->balance;
            
            // No one can edit agent_id - preserve original
            $agentIdToUpdate = $originalCustomer->agent_id;
            
            // Only admin and supervisor can toggle is_client
            $isClientToUpdate = $this->canToggleWallet() ? $this->is_client : $originalCustomer->is_client;

            $this->updateCustomerUseCase->execute(
                $this->customerId,
                $this->name,
                $primaryMobile,
                $this->customerCode,
                $this->gender,
                $balanceToUpdate,
                $isClientToUpdate,
                $agentIdToUpdate,
                $this->branch_id
            );
            
            // Sync mobile numbers
            $this->customer->mobileNumbers()->delete();
            foreach ($this->mobileNumbers as $number) {
                $this->customer->mobileNumbers()->create(['mobile_number' => $number]);
            }
            
            // If admin changed balance, record a transaction
            if ($this->canEditBalance() && $originalCustomer->balance != $this->balance) {
                \App\Models\Domain\Entities\Transaction::create([
                    'customer_name' => $this->customer->name,
                    'customer_mobile_number' => $this->customer->mobile_number,
                    'customer_code' => $this->customer->customer_code,
                    'amount' => abs($this->balance - $originalCustomer->balance),
                    'commission' => 0,
                    'deduction' => 0,
                    'transaction_type' => 'Adjustment',
                    'agent_id' => $user->id,
                    'transaction_date_time' => now(),
                    'status' => 'completed',
                    'branch_id' => $this->customer->branch_id,
                    'reference_number' => uniqid('BAL-'),
                    'notes' => 'Admin balance adjustment',
                ]);
            }
            
            session()->flash('message', 'Customer updated successfully.');
            return redirect()->route('customers.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update customer: ' . $e->getMessage());
        }
    }

    public function activateWallet()
    {
        if (!$this->canToggleWallet()) {
            session()->flash('error', 'You are not authorized to activate wallets.');
            return;
        }
        $this->is_client = true;
        $this->updateCustomer();
    }

    public function deactivateWallet()
    {
        if (!$this->canToggleWallet()) {
            session()->flash('error', 'You are not authorized to deactivate wallets.');
            return;
        }
        $this->is_client = false;
        $this->updateCustomer();
    }

    public function render()
    {
        return view('livewire.customers.edit');
    }
}
