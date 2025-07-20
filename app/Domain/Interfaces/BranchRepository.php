<?php

namespace App\Domain\Interfaces;

use App\Models\Domain\Entities\Branch;
use Illuminate\Database\Eloquent\Collection;

interface BranchRepository
{
    public function create(array $attributes): Branch;
    public function findById(string $id): ?Branch;
    public function update(string $id, array $attributes): Branch;
    public function delete(string $id): void;
    public function all(string $sortField = 'name', string $sortDirection = 'asc'): Collection;
} 