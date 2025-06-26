<?php

namespace App\Livewire\Customers;

use App\Application\UseCases\ListCustomers;
use App\Application\UseCases\DeleteCustomer;
use Livewire\Component;

class Index extends Component
{
    public array $customers;

    private ListCustomers $listCustomersUseCase;
    private DeleteCustomer $deleteCustomerUseCase;

    public function boot(ListCustomers $listCustomersUseCase, DeleteCustomer $deleteCustomerUseCase)
    {
        $this->listCustomersUseCase = $listCustomersUseCase;
        $this->deleteCustomerUseCase = $deleteCustomerUseCase;
    }

    public function mount()
    {
        $this->loadCustomers();
    }

    public function loadCustomers()
    {
        $this->customers = $this->listCustomersUseCase->execute();
    }

    public function deleteCustomer(string $customerId)
    {
        try {
            $this->deleteCustomerUseCase->execute($customerId);
            session()->flash('message', 'Customer deleted successfully.');
            $this->loadCustomers();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete customer: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.customers.index', [
            'customers' => $this->customers,
        ]);
    }
}
