<?php

namespace App\Livewire\Customers;

use App\Application\UseCases\CreateCustomer;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use App\Domain\Interfaces\UserRepository;
use App\Domain\Interfaces\BranchRepository;
use App\Domain\Interfaces\SafeRepository;
use App\Domain\Entities\User;
use App\Constants\Roles;

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
    private SafeRepository $safeRepository;

    public function boot(
        CreateCustomer $createCustomerUseCase,
        UserRepository $userRepository,
        BranchRepository $branchRepository,
        SafeRepository $safeRepository
    ) {
        $this->createCustomerUseCase = $createCustomerUseCase;
        $this->userRepository = $userRepository;
        $this->branchRepository = $branchRepository;
        $this->safeRepository = $safeRepository;
    }

    public function mount()
    {
        $user = Auth::user();
        // Remove the abort for agents, rely on Gate authorization
        Gate::authorize('manage-customers');
        $this->agents = User::role('agent')->get();
        $this->branches = $this->branchRepository->all();
        
        // Set default branch based on user role
        if (!$this->canSelectBranch()) {
            $this->branch_id = $user->branch_id;
        }
    }

    public function canSelectBranch(): bool
    {
        $user = Auth::user();
        return $user->hasRole(Roles::ADMIN) || $user->hasRole(Roles::GENERAL_SUPERVISOR);
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
                $this->addError('mobileNumbers', 'الرقم ' . $number . ' مستخدم بالفعل. يرجى إدخال رقم آخر.');
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

            // If initial balance is provided, update the branch safe
            $safeUpdated = false;
            if ($this->useInitialBalance && $balance > 0) {
                $this->updateBranchSafeBalance($this->branch_id, $balance);
                $safeUpdated = true;
            }

            // Set success status
            $this->createdCustomer = $customer->fresh(['mobileNumbers', 'branch', 'agent', 'createdBy']); // Reload with relationships
            $this->creationStatus = 'success';
            
            if ($safeUpdated) {
                $branchName = $customer->branch->name ?? 'Unknown Branch';
                $this->statusMessage = "تم إنشاء العميل بنجاح! تم إضافة الرصيد الابتدائي ({$balance} جنيه) إلى خزنة فرع {$branchName}.";
            } else {
                $this->statusMessage = 'تم إنشاء العميل بنجاح!';
            }
            
            $this->showStatusPage = true;
        } catch (\Exception $e) {
            // Set error status
            $this->creationStatus = 'error';
            $this->statusMessage = 'فشل إنشاء العميل: ' . $e->getMessage();
            $this->showStatusPage = true;
        }
    }

    private function updateBranchSafeBalance(int $branchId, float $amount): void
    {
        // Find the branch safe
        $branchSafes = \App\Models\Domain\Entities\Safe::where('branch_id', $branchId)
            ->where('type', 'branch')
            ->get();

        if ($branchSafes->isEmpty()) {
            throw new \Exception('No branch safe found for the selected branch. Please ensure the branch has a safe configured.');
        }

        // Use the first branch safe (there should typically be only one per branch)
        $branchSafe = $branchSafes->first();
        
        // Update the safe balance
        $newBalance = $branchSafe->current_balance + $amount;
        $this->safeRepository->update($branchSafe->id, ['current_balance' => $newBalance]);
        
        // Log the safe update for audit purposes
        \Log::info('Safe balance updated for customer creation', [
            'safe_id' => $branchSafe->id,
            'safe_name' => $branchSafe->name,
            'branch_id' => $branchId,
            'amount_added' => $amount,
            'previous_balance' => $branchSafe->current_balance,
            'new_balance' => $newBalance,
            'created_by' => Auth::id()
        ]);
    }

    public function backToForm()
    {
        $this->showStatusPage = false;
        $this->createdCustomer = null;
        $this->creationStatus = null;
        $this->statusMessage = '';
        $this->reset(['name', 'mobileNumbers', 'gender', 'balance', 'is_client', 'agent_id', 'branch_id', 'useInitialBalance']);
        $this->mobileNumbers = [''];
        
        // Reset branch_id based on user role
        if (!$this->canSelectBranch()) {
            $this->branch_id = Auth::user()->branch_id;
        }
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
