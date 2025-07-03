<?php

namespace App\Livewire\Lines;

use App\Application\UseCases\UpdateLine;
use App\Domain\Interfaces\LineRepository;
use App\Domain\Entities\User;
use Livewire\Component;
use Livewire\Attributes\Validate;

class Edit extends Component
{
    public $line;
    public $lineId;

    #[Validate('required|string|max:20|unique:lines,mobile_number,except,$this->lineId')] 
    public $mobileNumber = '';

    #[Validate('required|numeric|min:0')] 
    public $currentBalance = 0.00;

    #[Validate('required|numeric|min:0')] 
    public $dailyLimit = 0.00;

    #[Validate('required|numeric|min:0')] 
    public $monthlyLimit = 0.00;

    #[Validate('required|string|in:Vodafone,Orange,Etisalat,We')] 
    public $network = 'Vodafone';

    #[Validate('required|string|in:active,inactive')]
    public $status = 'active';

    #[Validate('required|exists:users,id')] 
    public $userId = '';

    private LineRepository $lineRepository;
    private UpdateLine $updateLineUseCase;

    public $users;

    public function boot(LineRepository $lineRepository, UpdateLine $updateLineUseCase)
    {
        $this->lineRepository = $lineRepository;
        $this->updateLineUseCase = $updateLineUseCase;
    }

    public function mount($lineId)
    {
        $this->authorize('manage-sim-lines');

        $this->lineId = $lineId;
        $this->line = $this->lineRepository->findById($lineId);

        if ($this->line) {
            $this->mobileNumber = $this->line->mobile_number;
            $this->currentBalance = $this->line->current_balance;
            $this->dailyLimit = $this->line->daily_limit;
            $this->monthlyLimit = $this->line->monthly_limit;
            $this->network = $this->line->network;
            $this->userId = $this->line->user_id;
            $this->status = $this->line->status;
            $this->users = User::all();
        } else {
            abort(404);
        }
    }

    public function updateLine()
    {
        $this->validate();

        try {
            $this->updateLineUseCase->execute(
                $this->lineId,
                [
                    'mobile_number' => $this->mobileNumber,
                    'current_balance' => (float) $this->currentBalance,
                    'daily_limit' => (float) $this->dailyLimit,
                    'monthly_limit' => (float) $this->monthlyLimit,
                    'network' => $this->network,
                    'user_id' => $this->userId,
                    'status' => $this->status,
                ]
            );

            session()->flash('message', 'Line updated successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update line: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.lines.edit');
    }
}
