<?php

namespace App\Application\UseCases;

use App\Domain\Entities\Customer;
use App\Domain\Interfaces\CustomerRepository;

class DeleteCustomer
{
    private CustomerRepository $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function execute(string $id): bool
    {
        $customer = $this->customerRepository->findById($id);

        if (!$customer) {
            return false;
        }

        $this->customerRepository->delete($customer);

        return true;
    }
} 