<?php

namespace App\Livewire\Branches;

use App\Application\UseCases\UpdateBranch;
use App\Domain\Interfaces\BranchRepository;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Gate;
use App\Domain\Interfaces\SafeRepository;
use App\Application\UseCases\UpdateSafe;

class Edit extends Component
{
    public $branch;
    public $branchId;

    #[Validate('required|string|max:255')] 
    public $name = '';

    #[Validate('nullable|string|max:1000')] 
    public $description = '';

    public $is_active = true;

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public $safe;
    public $safeId;
    public $safe_name = '';
    public $safe_current_balance = 0.00;
    public $safe_description = '';

    private BranchRepository $branchRepository;
    private UpdateBranch $updateBranchUseCase;
    private SafeRepository $safeRepository;
    private UpdateSafe $updateSafeUseCase;

    public function boot(BranchRepository $branchRepository, UpdateBranch $updateBranchUseCase, SafeRepository $safeRepository, UpdateSafe $updateSafeUseCase)
    {
        $this->branchRepository = $branchRepository;
        $this->updateBranchUseCase = $updateBranchUseCase;
        $this->safeRepository = $safeRepository;
        $this->updateSafeUseCase = $updateSafeUseCase;
    }

    public function mount(string $branchId)
    {
        Gate::authorize('edit-branches');
        $this->branchId = $branchId;
        $this->branch = $this->branchRepository->findById($branchId);

        if (!$this->branch) {
            abort(404);
        }

        $this->name = $this->branch->name;
        $this->description = $this->branch->description;
        

        
        // Ensure proper boolean conversion
        $rawValue = $this->branch->is_active;
        if (is_string($rawValue)) {
            $this->is_active = $rawValue === '1' || $rawValue === 'true';
        } else {
            $this->is_active = (bool)($rawValue ?? true);
        }
        
        // Debug what's being loaded
        \Log::info('Branch edit form loaded', [
            'branch_id' => $this->branchId,
            'branch_name' => $this->branch->name,
            'raw_is_active' => $rawValue,
            'converted_is_active' => $this->is_active,
            'is_active_type' => gettype($this->is_active),
        ]);
        
        // Force the value to be properly set
        $this->dispatch('branch-status-loaded', ['is_active' => $this->is_active]);

        // Load the first associated safe (main safe)
        $safe = $this->branch->safes->first();
        if ($safe) {
            $this->safe = $safe;
            $this->safeId = $safe->id;
            $this->safe_name = $safe->name;
            $this->safe_current_balance = $safe->current_balance;
            $this->safe_description = $safe->description;
        }
    }

    public function updateBranch()
    {
        \Log::info('Form submitted', [
            'is_active_value' => $this->is_active,
            'is_active_type' => gettype($this->is_active),
        ]);
        
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        try {

            $this->updateBranchUseCase->execute(
                $this->branchId,
                [
                    'name' => $this->name,
                    'description' => $this->description,
                    'is_active' => $this->is_active,
                ]
            );
            // Update safe if loaded
            if ($this->safeId) {
                $this->updateSafeUseCase->execute(
                    $this->safeId,
                    [
                        'name' => $this->safe_name,
                        'current_balance' => (float) $this->safe_current_balance,
                        'description' => $this->safe_description,
                        'is_active' => $this->is_active,
                    ]
                );
            }
            // Update all other safes for this branch to match status
            if ($this->branch) {
                foreach ($this->branch->safes as $safe) {
                    if ($safe->id != $this->safeId) {
                        $this->updateSafeUseCase->execute(
                            $safe->id,
                            ['is_active' => $this->is_active]
                        );
                    }
                }
            }
            session()->flash('message', 'Branch and safe updated successfully.');
            $this->redirect(route('branches.index'), navigate: true); // Redirect after successful update
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update branch or safe: ' . $e->getMessage());
        }
    }



    public function updatedIsActive($value)
    {
        \Log::info('is_active updated', ['new_value' => $value, 'type' => gettype($value)]);
    }

    public function render()
    {
        return view('livewire.branches.edit');
    }
} 