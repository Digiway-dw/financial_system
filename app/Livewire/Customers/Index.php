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

    public function loadCustomers()
    {
        $filters = [
            'name' => $this->name,
            'phone' => $this->phone,
            'code' => $this->code,
            'region' => $this->region,
            'date_added_start' => $this->date_added_start,
            'date_added_end' => $this->date_added_end,
        ];
        $result = $this->listCustomersUseCase->execute($filters);
        $this->customers = $result['customers'] ?? $result;
        $this->topByTransactionCount = $result['topByTransactionCount'] ?? [];
        $this->topByTransferred = $result['topByTransferred'] ?? [];
        $this->topByCommissions = $result['topByCommissions'] ?? [];
    }

    public function filter()
    {
        $this->loadCustomers();
    }

    public function updatedName()
    {
        $this->loadCustomers();
    }

    public function deleteCustomer(string $customerId)
    {
        Gate::authorize('edit-all-data'); // Only admin can delete customers (assuming for now)
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

    public function render()
    {
        return view('livewire.customers.index', [
            'customers' => $this->customers,
        ]);
    }
}
