<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Interfaces\SafeRepository;
use App\Models\Domain\Entities\Safe as EloquentSafe;
use App\Models\Domain\Entities\Safe;

class EloquentSafeRepository implements SafeRepository
{
    public function create(array $attributes): Safe
    {
        return EloquentSafe::create($attributes);
    }

    public function findById(string $id): ?Safe
    {
        return EloquentSafe::find($id);
    }

    public function update(string $id, array $attributes): Safe
    {
        $safe = EloquentSafe::findOrFail($id);
        $safe->update($attributes);
        return $safe;
    }

    public function delete(string $id): void
    {
        EloquentSafe::destroy($id);
    }

    public function all(): array
    {
        return $this->allWithBranch();
    }

    public function allWithBranch(): array
    {
        return EloquentSafe::with('branch')->get()->toArray();
    }
} 