<?php

namespace App\Livewire\Branches;

use App\Application\UseCases\UpdateBranch;
use App\Domain\Interfaces\BranchRepository;
use Livewire\Component;
use Livewire\Attributes\Validate;

class Edit extends Component
{
    public $branch;
    public $branchId;

    #[Validate('required|string|max:255')] 
    public $name = '';

    #[Validate('nullable|string|max:1000')] 
    public $description = '';

    private BranchRepository $branchRepository;
    private UpdateBranch $updateBranchUseCase;

    public function boot(BranchRepository $branchRepository, UpdateBranch $updateBranchUseCase)
    {
        $this->branchRepository = $branchRepository;
        $this->updateBranchUseCase = $updateBranchUseCase;
    }

    public function mount(string $branchId)
    {
        $this->branchId = $branchId;
        $this->branch = $this->branchRepository->findById($branchId);

        if (!$this->branch) {
            abort(404);
        }

        $this->name = $this->branch->name;
        $this->description = $this->branch->description;
    }

    public function updateBranch()
    {
        $this->validate();

        try {
            $this->updateBranchUseCase->execute(
                $this->branchId,
                [
                    'name' => $this->name,
                    'description' => $this->description,
                ]
            );

            session()->flash('message', 'Branch updated successfully.');
            $this->redirect(route('branches.index'), navigate: true); // Redirect after successful update
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update branch: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.branches.edit');
    }
} 