<?php

namespace App\Livewire\Lines;

use App\Application\UseCases\CreateLine;
use App\Domain\Entities\User;
use Livewire\Component;
use Livewire\Attributes\Validate;

class Create extends Component
{
    #[Validate('required|string|max:20|unique:lines,mobile_number')] 
    public $mobileNumber = '';

    #[Validate('required|numeric|min:0')] 
    public $currentBalance = 0.00;

    #[Validate('required|numeric|min:0')] 
    public $dailyLimit = 0.00;

    #[Validate('required|numeric|min:0')] 
    public $monthlyLimit = 0.00;

    #[Validate('required|string|in:Vodafone,Orange,Etisalat,We')] 
    public $network = 'Vodafone';

    #[Validate('required|exists:users,id')] 
    public $userId = '';

    private CreateLine $createLineUseCase;

    public $users = []; // Initialize as an empty array

    public function boot(CreateLine $createLineUseCase)
    {
        $this->createLineUseCase = $createLineUseCase;
    }

    public function mount()
    {
        // $this->users = User::all(); // Moved to render()
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
                'user_id' => $this->userId,
            ]);

            session()->flash('message', 'Line created successfully.');
            $this->reset(); // Clear form fields after submission
            $this->redirect(route('lines.index'), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create line: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $this->users = User::all(); // Load users in render method
        return view('livewire.lines.create');
    }
}
