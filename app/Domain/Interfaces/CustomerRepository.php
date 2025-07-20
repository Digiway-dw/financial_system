<?php

namespace App\Domain\Interfaces;

use App\Models\Domain\Entities\Customer;

interface CustomerRepository
{
    public function findById(string $id): ?Customer;
    public function findByMobileNumber(string $mobileNumber): ?Customer;
    public function findByCustomerCode(string $customerCode): ?Customer;
    public function save(Customer $customer): Customer;
    public function delete(Customer $customer): void;
    public function getAll(array $filters = []): array;
    public function getAllClients(): array;
    /**
     * Search customers by name or mobile number (partial match).
     * @param string $query
     * @return array
     */
    public function searchByNameOrMobile(string $query): array;
} 