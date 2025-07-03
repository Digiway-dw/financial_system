<?php

namespace App\Livewire\Lines;

use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Domain\Interfaces\LineRepository;
use App\Domain\Interfaces\UserRepository;
use App\Application\UseCases\TransferLine;
use Illuminate\Support\Facades\Gate;

class Transfer extends Component
{
    public $lineId;

    #[Validate('required|exists:users,id')] 
    public $newUserId = '';

    public $line;
    public $users;

    private LineRepository $lineRepository;
    private UserRepository $userRepository;
    private TransferLine $transferLineUseCase;

    public function boot(
        LineRepository $lineRepository,
        UserRepository $userRepository,
        TransferLine $transferLineUseCase
    )
    {
        $this->lineRepository = $lineRepository;
        $this->userRepository = $userRepository;
        $this->transferLineUseCase = $transferLineUseCase;
    }

    public function mount($lineId)
    {
        Gate::authorize('manage-sim-lines');

        $this->lineId = $lineId;
        $this->line = $this->lineRepository->findById($lineId);

        if (!$this->line) {
            abort(404);
        }

        $this->users = $this->userRepository->getAll();
    }

    public function transferLine()
    {
        $this->validate();

        try {
            $this->transferLineUseCase->execute($this->lineId, $this->newUserId);
            session()->flash('message', 'SIM line transferred successfully.');
            return $this->redirect(route('lines.index'), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to transfer SIM line: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.lines.transfer');
    }
} 