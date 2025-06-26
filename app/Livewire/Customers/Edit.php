<?php

namespace App\Livewire\Customers;

use App\Application\UseCases\UpdateCustomer;
use App\Domain\Interfaces\CustomerRepository;
use Livewire\Component;
use Livewire\Attributes\Validate;

class Edit extends Component
{
    public $customer;
    public $customerId;

    #[Validate('required|string|max:255')] 
    public $name = '';

    #[Validate('required|string|max:20')] 
    public $mobileNumber = '';

    #[Validate('nullable|string|max:255')] 
    public $customerCode = '';

    private CustomerRepository $customerRepository;
    private UpdateCustomer $updateCustomerUseCase;

    public function boot(CustomerRepository $customerRepository, UpdateCustomer $updateCustomerUseCase)
    {
        $this->customerRepository = $customerRepository;
        $this->updateCustomerUseCase = $updateCustomerUseCase;
    }

    public function mount($customerId)
    {
        $this->customerId = $customerId;
        $this->customer = $this->customerRepository->findById($customerId);

        if ($this->customer) {
            $this->name = $this->customer->name;
            $this->mobileNumber = $this->customer->mobile_number;
            $this->customerCode = $this->customer->customer_code;
        } else {
            abort(404);
        }
    }

    public function updateCustomer()
    {
        $this->validate();

        try {
            $this->updateCustomerUseCase->execute(
                $this->customerId,
                $this->name,
                $this->mobileNumber,
                $this->customerCode
            );

            session()->flash('message', 'Customer updated successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update customer: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.customers.edit');
    }
}
