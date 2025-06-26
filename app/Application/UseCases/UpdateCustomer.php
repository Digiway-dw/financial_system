<?php

namespace App\Application\UseCases;

use App\Domain\Entities\Customer;
use App\Domain\Interfaces\CustomerRepository;

class UpdateCustomer
{
    private CustomerRepository $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function execute(string $id, string $name, string $mobileNumber, ?string $customerCode = null): ?Customer
    {
        $customer = $this->customerRepository->findById($id);

        if (!$customer) {
            return null;
        }

        $customer->name = $name;
        $customer->mobile_number = $mobileNumber;
        $customer->customer_code = $customerCode;

        return $this->customerRepository->save($customer);
    }
} 