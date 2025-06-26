<?php

namespace App\Livewire\Safes;

use App\Application\UseCases\ListSafes;
use App\Application\UseCases\DeleteSafe;
use Livewire\Component;

class Index extends Component
{
    public array $safes;

    private ListSafes $listSafesUseCase;
    private DeleteSafe $deleteSafeUseCase;

    public function boot(ListSafes $listSafesUseCase, DeleteSafe $deleteSafeUseCase)
    {
        $this->listSafesUseCase = $listSafesUseCase;
        $this->deleteSafeUseCase = $deleteSafeUseCase;
    }

    public function mount()
    {
        $this->loadSafes();
    }

    public function loadSafes()
    {
        $this->safes = $this->listSafesUseCase->execute();
    }

    public function deleteSafe(string $safeId)
    {
        try {
            $this->deleteSafeUseCase->execute($safeId);
            session()->flash('message', 'Safe deleted successfully.');
            $this->loadSafes();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete safe: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.safes.index', [
            'safes' => $this->safes,
        ]);
    }
}
