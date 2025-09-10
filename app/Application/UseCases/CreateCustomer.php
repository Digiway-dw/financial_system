<?php

namespace App\Application\UseCases;

use App\Models\Domain\Entities\Customer;
use App\Domain\Interfaces\CustomerRepository;
use App\Domain\Interfaces\SafeRepository;

class CreateCustomer
{
    private CustomerRepository $customerRepository;
    private SafeRepository $safeRepository;

    public function __construct(
        CustomerRepository $customerRepository,
        SafeRepository $safeRepository
    ) {
        $this->customerRepository = $customerRepository;
        $this->safeRepository = $safeRepository;
    }

    public function execute(
        string $name,
        string $mobileNumber,
        ?string $customerCode = null,
        string $gender,
        float $balance,
        bool $is_client,
        ?int $agentId = null,
        int $branchId,
        bool $allowDebt = false,
        ?float $maxDebtLimit = null
    ): Customer
    {
        // Auto-generate customer code if not provided
        if (!$customerCode) {
            // Get the branch to access its name
            $branch = \App\Models\Domain\Entities\Branch::find($branchId);
            if (!$branch) {
                throw new \Exception('Branch not found.');
            }
            
            $prefix = $branch->name; // Use branch name (e.g., "F1", "F2", "F3")
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
        $customer->allow_debt = $allowDebt;
        $customer->max_debt_limit = $maxDebtLimit;
        return $this->customerRepository->save($customer);
    }
} 