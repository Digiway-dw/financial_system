<?php

namespace App\Livewire\Customers;

use App\Application\UseCases\ListCustomers;
use App\Application\UseCases\DeleteCustomer;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

class Index extends Component
{
    public array $customers;
    public $name;
    public $phone;
    public $code;
    public $region;
    public $date_added_start;
    public $date_added_end;
    public $topByTransactionCount = [];
    public $topByTransferred = [];
    public $topByCommissions = [];
    public $customerToDelete = null;

    public $sortField = 'name';
    public $sortDirection = 'asc';

    private ListCustomers $listCustomersUseCase;
    private DeleteCustomer $deleteCustomerUseCase;

    public function boot(ListCustomers $listCustomersUseCase, DeleteCustomer $deleteCustomerUseCase)
    {
        $this->listCustomersUseCase = $listCustomersUseCase;
        $this->deleteCustomerUseCase = $deleteCustomerUseCase;
    }

    public function mount()
    {
        Gate::authorize('view-customers');
        $this->loadCustomers();
    }

    public $perPage = 10;
    public $currentPage = 1;
    public $lazyLoading = false;
    public $totalCustomers = 0;

    public function loadCustomers()
    {
        $filters = [
            'name' => $this->name,
            'phone' => $this->phone,
            'code' => $this->code,
            'region' => $this->region,
            'date_added_start' => $this->date_added_start,
            'date_added_end' => $this->date_added_end,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
        ];
        $result = $this->listCustomersUseCase->execute($filters);
        $allCustomers = $result['customers'] ?? $result;
        $this->totalCustomers = count($allCustomers);
        if ($this->lazyLoading) {
            $this->customers = array_slice($allCustomers, 0, $this->perPage * $this->currentPage);
        } else {
            $this->currentPage = 1;
            $this->customers = array_slice($allCustomers, 0, $this->perPage);
        }
        $this->topByTransactionCount = $result['topByTransactionCount'] ?? [];
        $this->topByTransferred = $result['topByTransferred'] ?? [];
        $this->topByCommissions = $result['topByCommissions'] ?? [];
    }

    public function loadMore()
    {
        $this->currentPage++;
        $this->lazyLoading = true;
        $this->loadCustomers();
    }

    public function updatedPerPage($value)
    {
        $this->currentPage = 1;
        $this->lazyLoading = false;
        $this->loadCustomers();
    }

    public function filter()
    {
        $this->currentPage = 1;
        $this->lazyLoading = false;
        $this->loadCustomers();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->currentPage = 1;
        $this->lazyLoading = false;
        $this->loadCustomers();
    }

    public function updatedName()
    {
        $this->currentPage = 1;
        $this->lazyLoading = false;
        $this->loadCustomers();
    }

    public function render()
    {
        return view('livewire.customers.index', [
            'customers' => $this->customers,
            'perPage' => $this->perPage,
            'currentPage' => $this->currentPage,
            'totalCustomers' => $this->totalCustomers,
        ]);
    }

    // (removed duplicate filter)

    // (removed duplicate sortBy)

    // (removed duplicate updatedName)

    public function deleteCustomer(string $customerId)
    {
        Gate::authorize('delete-customers'); // Only admin and managers can delete customers
        try {
            $this->deleteCustomerUseCase->execute($customerId);
            session()->flash('message', 'Customer deleted successfully.');
            $this->loadCustomers();
            $this->customerToDelete = null;
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete customer: ' . $e->getMessage());
            $this->customerToDelete = null;
        }
    }

    // (removed duplicate render)
}
