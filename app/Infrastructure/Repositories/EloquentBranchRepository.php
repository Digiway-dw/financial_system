<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Interfaces\BranchRepository;
use App\Models\Domain\Entities\Branch as EloquentBranch;
use App\Models\Domain\Entities\Branch;
use Illuminate\Database\Eloquent\Collection;

class EloquentBranchRepository implements BranchRepository
{
    public function create(array $attributes): Branch
    {
        return EloquentBranch::create($attributes);
    }

    public function findById(string $id): ?Branch
    {
        return EloquentBranch::find($id);
    }

    public function update(string $id, array $attributes): Branch
    {
        $branch = EloquentBranch::findOrFail($id);
        $branch->update($attributes);
        return $branch;
    }

    public function delete(string $id): void
    {
        $branch = EloquentBranch::find($id);
        if ($branch) {
            // Delete all associated safes
            foreach ($branch->safes as $safe) {
                $safe->delete();
            }
            $branch->delete();
        }
    }

    public function all(string $sortField = 'name', string $sortDirection = 'asc'): Collection
    {
        // Handle sorting by safe balance (related table)
        if ($sortField === 'safe_balance') {
            // Use a subquery to get branches with their safe balances
            $branches = EloquentBranch::with('safe')
                ->get()
                ->sortBy(function ($branch) {
                    return $branch->safe ? $branch->safe->current_balance : 0;
                });
            
            if ($sortDirection === 'desc') {
                $branches = $branches->reverse();
            }
            
            return $branches;
        } else {
            // Regular sorting for branches table columns
            return EloquentBranch::with('safe')
                ->orderBy($sortField, $sortDirection)
                ->get();
        }
    }
} 