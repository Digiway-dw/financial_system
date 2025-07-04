<?php

namespace App\Application\UseCases;

use App\Models\Domain\Entities\Customer;
use App\Domain\Interfaces\CustomerRepository;

class CreateCustomer
{
    private CustomerRepository $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function execute(
        string $name,
        string $mobileNumber,
        ?string $customerCode = null,
        string $gender,
        float $balance,
        bool $is_client,
        ?int $agentId = null,
        int $branchId
    ): Customer
    {
        // Auto-generate customer code if not provided
        if (!$customerCode) {
            $prefix = 'F' . $branchId;
            $lastCustomer = \App\Models\Domain\Entities\Customer::where('branch_id', $branchId)
                ->where('customer_code', 'like', $prefix . '-%')
                ->orderByDesc('id')->first();
            if ($lastCustomer && preg_match('/' . $prefix . '-(\d+)/', $lastCustomer->customer_code, $matches)) {
                $nextNumber = str_pad(((int)$matches[1]) + 1, 5, '0', STR_PAD_LEFT);
            } else {
                $nextNumber = '00001';
            }
            $customerCode = $prefix . '-' . $nextNumber;
        }
        $customer = new Customer();
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