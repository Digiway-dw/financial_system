<?php

namespace App\Domain\Interfaces;

use App\Models\Domain\Entities\Branch;

interface BranchRepository
{
    public function create(array $attributes): Branch;
    public function findById(string $id): ?Branch;
    public function update(string $id, array $attributes): Branch;
    public function delete(string $id): void;
    public function all(): array;
} 