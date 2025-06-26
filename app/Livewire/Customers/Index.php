<?php

namespace App\Livewire\Customers;

use App\Application\UseCases\ListCustomers;
use Livewire\Component;

class Index extends Component
{
    public array $customers;

    private ListCustomers $listCustomersUseCase;

    public function boot(ListCustomers $listCustomersUseCase)
    {
        $this->listCustomersUseCase = $listCustomersUseCase;
    }

    public function mount()
    {
        $this->customers = $this->listCustomersUseCase->execute();
    }

    public function render()
    {
        return view('livewire.customers.index', [
            'customers' => $this->customers,
        ]);
    }
}
