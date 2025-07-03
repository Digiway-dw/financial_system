<?php

namespace App\Application\UseCases;

use App\Models\Domain\Entities\Customer;
use App\Domain\Interfaces\CustomerRepository;

class DeleteCustomer
{
    private CustomerRepository $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function execute(string $id): void
    {
        $customer = $this->customerRepository->findById($id);

        if ($customer) {
            $this->customerRepository->delete($customer);
        }
    }
} 