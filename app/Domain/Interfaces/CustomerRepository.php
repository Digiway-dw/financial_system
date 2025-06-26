<?php

namespace App\Domain\Interfaces;

use App\Domain\Entities\Customer;

interface CustomerRepository
{
    public function findById(string $id): ?Customer;
    public function findByMobileNumber(string $mobileNumber): ?Customer;
    public function save(Customer $customer): Customer;
    public function delete(Customer $customer): void;
    public function getAll(): array;
} 