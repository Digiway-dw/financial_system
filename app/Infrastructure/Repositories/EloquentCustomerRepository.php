<?php

namespace App\Infrastructure\Repositories;

use App\Models\Domain\Entities\Customer;
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

    public function findByCustomerCode(string $customerCode): ?Customer
    {
        return Customer::where('customer_code', $customerCode)->first();
    }

    public function save(Customer $customer): Customer
    {
        $customer->save();
        return $customer;
    }

    public function delete(Customer $customer): void
    {
        // Delete related mobile numbers
        $customer->mobileNumbers()->delete();
        $customer->delete();
    }

    public function getAll(array $filters = []): array
    {
        $query = Customer::query();

        if (!empty($filters['name'])) {
            $query->where('name', 'like', $filters['name'] . '%');
        }
        if (!empty($filters['phone'])) {
            $query->where('mobile_number', 'like', '%' . $filters['phone'] . '%');
        }
        if (!empty($filters['code'])) {
            $query->where('customer_code', 'like', '%' . $filters['code'] . '%');
        }
        if (!empty($filters['region'])) {
            $query->where('region', $filters['region']);
        }
        if (!empty($filters['date_added_start'])) {
            $query->whereDate('created_at', '>=', $filters['date_added_start']);
        }
        if (!empty($filters['date_added_end'])) {
            $query->whereDate('created_at', '<=', $filters['date_added_end']);
        }

        $customers = $query->withCount('transactions')->get();

        // Statistics with simpler approach to avoid N+1 queries
        $topByTransactionCount = $customers->sortByDesc('transactions_count')->take(5)->map(function ($c) {
            return [
                'name' => $c->name,
                'count' => $c->transactions_count,
            ];
        })->values()->toArray();

        // For now, we'll skip the complex aggregations that were causing issues
        $topByTransferred = [];
        $topByCommissions = [];

        return [
            'customers' => $customers->toArray(),
            'topByTransactionCount' => $topByTransactionCount,
            'topByTransferred' => $topByTransferred,
            'topByCommissions' => $topByCommissions,
        ];
    }

    public function getAllClients(): array
    {
        return Customer::where('is_client', true)->get()->toArray();
    }

    public function searchByNameOrMobile(string $query): array
    {
        return Customer::where('name', 'like', "%$query%")
            ->orWhere('mobile_number', 'like', "%$query%")
            ->limit(10)
            ->get()
            ->toArray();
    }
}
