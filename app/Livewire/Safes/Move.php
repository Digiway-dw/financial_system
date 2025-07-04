<?php

namespace App\Livewire\Safes;

use App\Application\UseCases\MoveSafeCash;
use App\Models\Domain\Entities\Safe;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class Move extends Component
{
    #[Validate('required|exists:safes,id')] 
    public $fromSafeId = '';

    #[Validate('required|exists:safes,id|different:fromSafeId')] 
    public $toSafeId = '';

    #[Validate('required|numeric|min:0.01')] 
    public $amount = 0.00;

    public Collection $safes;

    private MoveSafeCash $moveSafeCashUseCase;

    public function boot(MoveSafeCash $moveSafeCashUseCase)
    {
        $this->moveSafeCashUseCase = $moveSafeCashUseCase;
    }

    public function mount()
    {
        $this->authorize('manage-safes'); // Enforce authorization
        // $this->safes = Safe::all(); // Moved to render()
    }

    public function moveCash()
    {
        $this->validate();

        try {
            // $agentName = Auth::user()->name; // Removed, not needed for the use case
            $this->moveSafeCashUseCase->execute(
                $this->fromSafeId,
                $this->toSafeId,
                (float) $this->amount,
                Auth::user()->id // Pass the authenticated user's ID
            );

            session()->flash('message', 'Cash moved successfully.');
            $this->reset(['fromSafeId', 'toSafeId', 'amount']); // Clear form fields
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to move cash: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $this->safes = Safe::all(); // Load safes in render method
        return view('livewire.safes.move');
    }
}
