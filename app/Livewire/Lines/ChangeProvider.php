<?php

namespace App\Livewire\Lines;

use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Domain\Interfaces\LineRepository;
use App\Application\UseCases\UpdateLineNetwork;
use Illuminate\Support\Facades\Gate;

class ChangeProvider extends Component
{
    public $lineId;

    #[Validate('required|string|max:255')] 
    public $newNetwork = '';

    public $line;
    public array $networks = [
        'Mobily',
        'STC',
        'Zain',
        'Lebara',
        'Virgin Mobile',
        'Friendi',
        'Red Bull Mobile',
    ];

    private LineRepository $lineRepository;
    private UpdateLineNetwork $updateLineNetworkUseCase;

    public function boot(
        LineRepository $lineRepository,
        UpdateLineNetwork $updateLineNetworkUseCase
    )
    {
        $this->lineRepository = $lineRepository;
        $this->updateLineNetworkUseCase = $updateLineNetworkUseCase;
    }

    public function mount($lineId)
    {
        Gate::authorize('manage-sim-lines');

        $this->lineId = $lineId;
        $this->line = $this->lineRepository->findById($lineId);

        if (!$this->line) {
            abort(404);
        }

        $this->newNetwork = $this->line->network; // Set current network as default
    }

    public function updateNetwork()
    {
        $this->validate();

        try {
            $this->updateLineNetworkUseCase->execute($this->lineId, $this->newNetwork);
            session()->flash('message', 'SIM provider updated successfully.');
            return $this->redirect(route('lines.index'), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update SIM provider: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.lines.change-provider');
    }
} 