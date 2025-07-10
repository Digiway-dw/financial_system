<?php

namespace App\Livewire\Lines;

use App\Application\UseCases\CreateLine;
use App\Domain\Entities\User;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Gate;

class Create extends Component
{
    #[Validate('required|string|max:20|unique:lines,mobile_number')]
    public $mobileNumber = '';

    #[Validate('required|numeric|min:0')]
    public $currentBalance = '';

    #[Validate('required|numeric|min:0')]
    public $dailyLimit = '';

    #[Validate('required|numeric|min:0')]
    public $monthlyLimit = '';

    #[Validate('required|string|in:Vodafone,Orange,Etisalat,We')]
    public $network = 'Vodafone';

    #[Validate('required|exists:branches,id')]
    public $branchId = '';

    private CreateLine $createLineUseCase;

    public $branches = []; // Initialize as an empty array

    public function boot(CreateLine $createLineUseCase)
    {
        $this->createLineUseCase = $createLineUseCase;
    }

    public function mount()
    {
        Gate::authorize('manage-sim-lines');
        // $this->branches = Branch::all(); // Moved to render()
    }

    public function createLine()
    {
        $this->validate();

        try {
            $this->createLineUseCase->execute([
                'mobile_number' => $this->mobileNumber,
                'current_balance' => (float) $this->currentBalance,
                'daily_limit' => (float) $this->dailyLimit,
                'monthly_limit' => (float) $this->monthlyLimit,
                'network' => $this->network,
                'branch_id' => $this->branchId,
            ]);

            session()->flash('message', 'Line created successfully.');
            $this->redirect(route('lines.index'), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create line: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $this->branches = \App\Models\Domain\Entities\Branch::all(); // Load branches in render method
        return view('livewire.lines.create');
    }
}
