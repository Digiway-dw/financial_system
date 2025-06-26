<?php

namespace App\Domain\Interfaces;

use App\Models\Domain\Entities\Safe;

interface SafeRepository
{
    public function create(array $attributes): Safe;
    public function findById(string $id): ?Safe;
    public function update(string $id, array $attributes): Safe;
    public function delete(string $id): void;
    public function all(): array;
} 