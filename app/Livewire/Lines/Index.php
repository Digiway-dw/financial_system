<?php

namespace App\Livewire\Lines;

use App\Application\UseCases\ListLines;
use App\Application\UseCases\DeleteLine;
use App\Application\UseCases\ToggleLineStatus;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

class Index extends Component
{
    public $sortField = 'mobile_number';
    public $sortDirection = 'asc';
    public array $lines = [];

    private ListLines $listLinesUseCase;
    private DeleteLine $deleteLineUseCase;
    private ToggleLineStatus $toggleLineStatusUseCase;

    public function boot(ListLines $listLinesUseCase, DeleteLine $deleteLineUseCase, ToggleLineStatus $toggleLineStatusUseCase)
    {
        $this->listLinesUseCase = $listLinesUseCase;
        $this->deleteLineUseCase = $deleteLineUseCase;
        $this->toggleLineStatusUseCase = $toggleLineStatusUseCase;
    }

    public function mount()
    {
        Gate::authorize('view-lines');
        $this->loadLines();
    }

    public function loadLines()
    {
        $this->lines = $this->listLinesUseCase->execute([
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
        ]);
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->loadLines();
    }

    public function deleteLine(string $lineId)
    {
        Gate::authorize('manage-sim-lines');
        try {
            $this->deleteLineUseCase->execute($lineId);
            session()->flash('message', 'Line deleted successfully.');
            $this->loadLines();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete line: ' . $e->getMessage());
        }
    }

    public function toggleStatus(string $lineId)
    {
        Gate::authorize('manage-sim-lines');
        try {
            $this->toggleLineStatusUseCase->execute($lineId);
            session()->flash('message', 'Line status updated successfully.');
            $this->loadLines();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update line status: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.lines.index', [
            'lines' => $this->lines,
        ]);
    }
}
