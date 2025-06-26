<?php

namespace App\Livewire\Lines;

use App\Application\UseCases\ListLines;
use App\Application\UseCases\DeleteLine;
use Livewire\Component;

class Index extends Component
{
    public array $lines;

    private ListLines $listLinesUseCase;
    private DeleteLine $deleteLineUseCase;

    public function boot(ListLines $listLinesUseCase, DeleteLine $deleteLineUseCase)
    {
        $this->listLinesUseCase = $listLinesUseCase;
        $this->deleteLineUseCase = $deleteLineUseCase;
    }

    public function mount()
    {
        $this->loadLines();
    }

    public function loadLines()
    {
        $this->lines = $this->listLinesUseCase->execute();
    }

    public function deleteLine(string $lineId)
    {
        try {
            $this->deleteLineUseCase->execute($lineId);
            session()->flash('message', 'Line deleted successfully.');
            $this->loadLines();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete line: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.lines.index', [
            'lines' => $this->lines,
        ]);
    }
}
