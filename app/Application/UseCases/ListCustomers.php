<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\CustomerRepository;

class ListCustomers
{
    private CustomerRepository $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function execute(): array
    {
        return $this->customerRepository->getAll();
    }
} 