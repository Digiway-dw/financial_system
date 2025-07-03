<?php

namespace App\Livewire\Safes;

use App\Application\UseCases\ListSafes;
use App\Application\UseCases\DeleteSafe;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;
use App\Domain\Interfaces\CustomerRepository;

class Index extends Component
{
    public array $safes;
    public array $clients;

    private ListSafes $listSafesUseCase;
    private DeleteSafe $deleteSafeUseCase;
    private CustomerRepository $customerRepository;

    public function boot(ListSafes $listSafesUseCase, DeleteSafe $deleteSafeUseCase, CustomerRepository $customerRepository)
    {
        $this->listSafesUseCase = $listSafesUseCase;
        $this->deleteSafeUseCase = $deleteSafeUseCase;
        $this->customerRepository = $customerRepository;
    }

    public function mount()
    {
        Gate::authorize('manage-safes');
        $this->loadSafes();
        $this->loadClients();
    }

    public function loadSafes()
    {
        $this->safes = $this->listSafesUseCase->execute();
    }

    public function loadClients()
    {
        $this->clients = $this->customerRepository->getAllClients(); // Assuming a method to get only clients
    }

    public function deleteSafe(string $safeId)
    {
        Gate::authorize('manage-safes');
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
            'clients' => $this->clients,
        ]);
    }
}
