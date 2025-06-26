<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Customer;
use App\Domain\Interfaces\CustomerRepository;

class EloquentCustomerRepository implements CustomerRepository
{
    public function findById(string $id): ?Customer
    {
        return Customer::find($id);
    }

    public function findByMobileNumber(string $mobileNumber): ?Customer
    {
        return Customer::where('mobile_number', $mobileNumber)->first();
    }

    public function save(Customer $customer): Customer
    {
        $customer->save();
        return $customer;
    }

    public function delete(Customer $customer): void
    {
        $customer->delete();
    }

    public function getAll(): array
    {
        return Customer::all()->toArray();
    }
} 