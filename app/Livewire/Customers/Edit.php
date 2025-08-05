<?php

namespace App\Livewire\Customers;

use App\Application\UseCases\UpdateCustomer;
use App\Domain\Interfaces\CustomerRepository;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Gate;
use App\Domain\Interfaces\UserRepository;
use App\Domain\Interfaces\BranchRepository;
use App\Domain\Interfaces\SafeRepository;
use App\Domain\Entities\User;
use App\Models\Domain\Entities\Safe;
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
    private SafeRepository $safeRepository;

    public function boot(
        CustomerRepository $customerRepository,
        UpdateCustomer $updateCustomerUseCase,
        UserRepository $userRepository,
        BranchRepository $branchRepository,
        SafeRepository $safeRepository
    ) {
        $this->customerRepository = $customerRepository;
        $this->updateCustomerUseCase = $updateCustomerUseCase;
        $this->userRepository = $userRepository;
        $this->branchRepository = $branchRepository;
        $this->safeRepository = $safeRepository;
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

    private function findBranchSafe(int $branchId): ?Safe
    {
        $safes = $this->safeRepository->allWithBranch();
        foreach ($safes as $safe) {
            if (($safe['branch_id'] ?? $safe->branch_id) == $branchId) {
                return is_array($safe) ? Safe::find($safe['id']) : $safe;
            }
        }
        return null;
    }

    private function updateSafeBalance(int $branchId, float $amount, string $operation = 'add'): void
    {
        $safe = $this->findBranchSafe($branchId);
        if (!$safe) {
            throw new \Exception("No safe found for branch ID: {$branchId}");
        }

        $currentBalance = (float) $safe->current_balance;
        $newBalance = $operation === 'add' ? $currentBalance + $amount : $currentBalance - $amount;

        if ($newBalance < 0) {
            throw new \Exception("Insufficient funds in safe. Current balance: {$currentBalance}, Required: {$amount}");
        }

        $this->safeRepository->update($safe->id, ['current_balance' => $newBalance]);
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
            
            // Handle safe balance updates when customer balance changes
            if ($this->canEditBalance() && $originalCustomer->balance != $this->balance) {
                $oldBalance = (float) $originalCustomer->balance;
                $newBalance = (float) $this->balance;

                // Step 1: Deduct the current customer balance from the safe
                if ($oldBalance > 0) {
                    $this->updateSafeBalance($this->customer->branch_id, $oldBalance, 'subtract');
                }

                // Step 2: Add the new customer balance to the safe
                if ($newBalance > 0) {
                    $this->updateSafeBalance($this->customer->branch_id, $newBalance, 'add');
                }

                // Record a transaction for audit
                \App\Models\Domain\Entities\Transaction::create([
                    'customer_name' => $this->customer->name,
                    'customer_mobile_number' => $this->customer->mobile_number,
                    'customer_code' => $this->customer->customer_code,
                    'amount' => abs($newBalance - $oldBalance),
                    'commission' => 0,
                    'deduction' => 0,
                    'transaction_type' => 'Adjustment',
                    'agent_id' => $user->id,
                    'transaction_date_time' => now(),
                    'status' => 'completed',
                    'branch_id' => $this->customer->branch_id,
                    'reference_number' => generate_reference_number($this->customer->branch->name ?? 'ADMIN'),
                    'notes' => ($newBalance > $oldBalance) ? 'Admin balance increase' : 'Admin balance decrease',
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
        session()->flash('message', 'Wallet activated. Click "تعديل العميل" to save changes.');
    }

    public function deactivateWallet()
    {
        if (!$this->canToggleWallet()) {
            session()->flash('error', 'You are not authorized to deactivate wallets.');
            return;
        }
        $this->is_client = false;
        session()->flash('message', 'Wallet deactivated. Click "تعديل العميل" to save changes.');
    }

    public function render()
    {
        return view('livewire.customers.edit');
    }
}
