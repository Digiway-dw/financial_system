<?php

namespace App\Application\UseCases;

use App\Models\Domain\Entities\Customer;
use App\Domain\Interfaces\CustomerRepository;

class UpdateCustomer
{
    private CustomerRepository $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function execute(
        string $id,
        string $name,
        string $mobileNumber,
        ?string $customerCode = null,
        string $gender,
        float $balance,
        bool $is_client,
        ?int $agentId = null,
        int $branchId
    ): ?Customer
    {
        $customer = $this->customerRepository->findById($id);

        if (!$customer) {
            return null;
        }

        $customer->name = $name;
        $customer->mobile_number = $mobileNumber;
        $customer->customer_code = $customerCode;
        $customer->gender = $gender;
        $customer->balance = $balance;
        $customer->is_client = $is_client;
        $customer->agent_id = $agentId;
        $customer->branch_id = $branchId;

        return $this->customerRepository->save($customer);
    }
} 