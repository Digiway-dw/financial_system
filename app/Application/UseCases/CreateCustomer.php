<?php

namespace App\Application\UseCases;

use App\Domain\Entities\Customer;
use App\Domain\Interfaces\CustomerRepository;

class CreateCustomer
{
    private CustomerRepository $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function execute(string $name, string $mobileNumber, ?string $customerCode = null): Customer
    {
        $customer = new Customer();
        $customer->name = $name;
        $customer->mobile_number = $mobileNumber;
        $customer->customer_code = $customerCode;
        return $this->customerRepository->save($customer);
    }
} 