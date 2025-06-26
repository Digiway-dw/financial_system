<?php

namespace App\Livewire\Customers;

use App\Application\UseCases\CreateCustomer;
use Livewire\Component;
use Livewire\Attributes\Validate;

class Create extends Component
{
    #[Validate('required|string|max:255')] 
    public $name = '';

    #[Validate('required|string|max:20')] 
    public $mobileNumber = '';

    #[Validate('nullable|string|max:255')] 
    public $customerCode = '';

    private CreateCustomer $createCustomerUseCase;

    public function boot(CreateCustomer $createCustomerUseCase)
    {
        $this->createCustomerUseCase = $createCustomerUseCase;
    }

    public function createCustomer()
    {
        $this->validate();

        try {
            $this->createCustomerUseCase->execute(
                $this->name,
                $this->mobileNumber,
                $this->customerCode
            );

            session()->flash('message', 'Customer created successfully.');
            $this->reset(); // Clear form fields after submission
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create customer: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.customers.create');
    }
}
