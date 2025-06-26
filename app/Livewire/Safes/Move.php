<?php

namespace App\Livewire\Safes;

use App\Application\UseCases\MoveSafeCash;
use App\Models\Domain\Entities\Safe;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Auth;

class Move extends Component
{
    #[Validate('required|exists:safes,id')] 
    public $fromSafeId = '';

    #[Validate('required|exists:safes,id|different:fromSafeId')] 
    public $toSafeId = '';

    #[Validate('required|numeric|min:0.01')] 
    public $amount = 0.00;

    public $safes;

    private MoveSafeCash $moveSafeCashUseCase;

    public function boot(MoveSafeCash $moveSafeCashUseCase)
    {
        $this->moveSafeCashUseCase = $moveSafeCashUseCase;
    }

    public function mount()
    {
        $this->safes = Safe::all();
    }

    public function moveCash()
    {
        $this->validate();

        try {
            $agentName = Auth::user()->name; // Get the name of the logged-in agent
            $this->moveSafeCashUseCase->execute(
                $this->fromSafeId,
                $this->toSafeId,
                (float) $this->amount,
                $agentName
            );

            session()->flash('message', 'Cash moved successfully.');
            $this->reset(['fromSafeId', 'toSafeId', 'amount']); // Clear form fields
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to move cash: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.safes.move');
    }
}
