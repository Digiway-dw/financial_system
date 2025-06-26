<?php

namespace App\Livewire\Safes;

use App\Application\UseCases\UpdateSafe;
use App\Domain\Interfaces\SafeRepository;
use App\Models\Domain\Entities\Branch;
use Livewire\Component;
use Livewire\Attributes\Validate;

class Edit extends Component
{
    public $safe;
    public $safeId;

    #[Validate('required|string|max:255')] 
    public $name = '';

    #[Validate('required|numeric|min:0')] 
    public $balance = 0.00;

    #[Validate('required|exists:branches,id')] 
    public $branchId = '';

    #[Validate('nullable|string|max:1000')] 
    public $description = '';

    private SafeRepository $safeRepository;
    private UpdateSafe $updateSafeUseCase;

    public function boot(SafeRepository $safeRepository, UpdateSafe $updateSafeUseCase)
    {
        $this->safeRepository = $safeRepository;
        $this->updateSafeUseCase = $updateSafeUseCase;
    }

    public function mount($safeId)
    {
        $this->safeId = $safeId;
        $this->safe = $this->safeRepository->findById($safeId);

        if ($this->safe) {
            $this->name = $this->safe->name;
            $this->balance = $this->safe->balance;
            $this->branchId = $this->safe->branch_id;
            $this->description = $this->safe->description;
            $this->branches = Branch::all();
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
                    'balance' => (float) $this->balance,
                    'branch_id' => $this->branchId,
                    'description' => $this->description,
                ]
            );

            session()->flash('message', 'Safe updated successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update safe: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.safes.edit');
    }
}
