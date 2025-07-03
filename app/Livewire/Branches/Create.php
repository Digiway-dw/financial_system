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
            ]);

            session()->flash('message', 'Branch created successfully.');
            $this->reset(); // Clear form fields after submission
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