<?php

namespace App\Livewire\Branches;

use App\Application\UseCases\CreateBranch;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Gate;

class Create extends Component
{
    #[Validate('required|string|max:255')] 
    public $name = '';

    #[Validate('nullable|string|max:1000')] 
    public $description = '';

    #[Validate('required|string|max:255')] 
    public $location = '';

    #[Validate('required|string|max:255|unique:branches,branch_code')] 
    public $branch_code = '';

    #[Validate('required|numeric|min:0')]
    public $safe_initial_balance;

    #[Validate('nullable|string|max:1000')]
    public $safe_description = '';

    private CreateBranch $createBranchUseCase;

    public function boot(CreateBranch $createBranchUseCase)
    {
        $this->createBranchUseCase = $createBranchUseCase;
    }

    public function mount()
    {
        Gate::authorize('manage-branches');
    }

    public function createBranch()
    {
        $this->validate();

        try {
            $this->createBranchUseCase->execute([
                'name' => $this->name,
                'description' => $this->description,
                'location' => $this->location,
                'branch_code' => $this->branch_code,
                // Safe fields
                'safe_initial_balance' => $this->safe_initial_balance,
                'safe_description' => $this->safe_description,
            ]);

            session()->flash('message', 'Branch created successfully.');
            $this->redirect(route('branches.index'), navigate: true); // Redirect after successful creation
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create branch: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.branches.create');
    }
} 