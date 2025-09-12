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
    public $name = '';

    public $sortField = 'name';
    public $sortDirection = 'asc';
    
    // Separate sorting variables for client wallets table
    public $clientSortField = 'name';
    public $clientSortDirection = 'asc';

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
        Gate::authorize('view-safes');
        $this->loadSafes();
        $this->loadClients();
    }

    public function loadSafes()
    {
        $filters = [
            'name' => $this->name,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
        ];
        $this->safes = $this->listSafesUseCase->execute($filters);
    }

    public function loadClients()
    {
        $filters = [
            'sortField' => $this->clientSortField,
            'sortDirection' => $this->clientSortDirection,
        ];
        $result = $this->customerRepository->getAll($filters);
        $clients = $result['customers'] ?? $result;
        // Only include customers with active wallets (is_client = true)
        $this->clients = array_filter($clients, function($client) {
            return isset($client['is_client']) && $client['is_client'] === true;
        });
    }

    public function filter()
    {
        $this->loadSafes();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->loadSafes();
    }

    public function sortClientsBy($field)
    {
        if ($this->clientSortField === $field) {
            $this->clientSortDirection = $this->clientSortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->clientSortField = $field;
            $this->clientSortDirection = 'asc';
        }
        $this->loadClients();
    }

    public function deleteSafe(string $safeId)
    {
        Gate::authorize('delete-safes');
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
