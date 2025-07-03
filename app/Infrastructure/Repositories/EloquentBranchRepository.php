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
        EloquentBranch::destroy($id);
    }

    public function all(): Collection
    {
        return EloquentBranch::all();
    }
} 