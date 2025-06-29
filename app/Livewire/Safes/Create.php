<?php

namespace App\Livewire\Safes;

use App\Application\UseCases\CreateSafe;
use App\Models\Domain\Entities\Branch;
use Livewire\Component;
use Livewire\Attributes\Validate;

class Create extends Component
{
    #[Validate('required|string|max:255')] 
    public $name = '';

    #[Validate('required|numeric|min:0')] 
    public $currentBalance = 0.00;

    #[Validate('required|exists:branches,id')] 
    public $branchId = '';

    #[Validate('nullable|string|max:1000')] 
    public $description = '';

    private CreateSafe $createSafeUseCase;

    public $branches = [];

    public function boot(CreateSafe $createSafeUseCase)
    {
        $this->createSafeUseCase = $createSafeUseCase;
    }

    public function mount()
    {
        $this->authorize('manage-safes'); // Enforce authorization
        // $this->branches = Branch::all(); // Moved to render()
    }

    public function createSafe()
    {
        $this->validate();

        try {
            $this->createSafeUseCase->execute([
                'name' => $this->name,
                'current_balance' => (float) $this->currentBalance,
                'branch_id' => $this->branchId,
                'description' => $this->description,
            ]);

            session()->flash('message', 'Safe created successfully.');
            $this->reset(); // Clear form fields after submission
            $this->redirect(route('safes.index'), navigate: true); // Redirect after successful creation
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create safe: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $this->branches = Branch::all(); // Load branches in render method
        return view('livewire.safes.create');
    }
}
