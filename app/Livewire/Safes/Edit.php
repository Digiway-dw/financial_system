<?php

namespace App\Livewire\Safes;

use App\Application\UseCases\UpdateSafe;
use App\Domain\Interfaces\SafeRepository;
use App\Models\Domain\Entities\Branch;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Gate;

class Edit extends Component
{
    public $safe;
    public $safeId;

    #[Validate('required|string|max:255')] 
    public $name = '';

    #[Validate('required|numeric|min:0')] 
    public $currentBalance = 0.00;

    #[Validate('required|exists:branches,id')] 
    public $branchId = '';

    #[Validate('required|string')]
    public $type = '';

    #[Validate('nullable|string|max:1000')] 
    public $description = '';

    private SafeRepository $safeRepository;
    private UpdateSafe $updateSafeUseCase;

    public $branches = [];

    public function boot(SafeRepository $safeRepository, UpdateSafe $updateSafeUseCase)
    {
        $this->safeRepository = $safeRepository;
        $this->updateSafeUseCase = $updateSafeUseCase;
    }

    public function mount($safeId)
    {
        $this->authorize('manage-safes');

        $this->safeId = $safeId;
        $this->safe = $this->safeRepository->findById($safeId);

        if ($this->safe) {
            $this->name = $this->safe->name;
            $this->currentBalance = $this->safe->current_balance;
            $this->branchId = $this->safe->branch_id;
            $this->description = $this->safe->description;
            $this->type = $this->safe->type;
        } else {
            abort(404);
        }
    }

    public function updateSafe()
    {
        $this->validate();

        try {
            $this->updateSafeUseCase->execute(
                $this->safeId,
                [
                    'name' => $this->name,
                    'current_balance' => (float) $this->currentBalance,
                    'branch_id' => $this->branchId,
                    'description' => $this->description,
                    'type' => $this->type,
                ]
            );

            session()->flash('message', 'Safe updated successfully.');
            $this->redirect(route('safes.index'), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update safe: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $this->branches = Branch::all();
        return view('livewire.safes.edit');
    }
}
