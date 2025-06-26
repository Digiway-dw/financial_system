<?php

namespace App\Domain\Interfaces;

use App\Models\Domain\Entities\Line;

interface LineRepository
{
    public function create(array $attributes): Line;
    public function findById(string $id): ?Line;
    public function update(string $id, array $attributes): Line;
    public function delete(string $id): void;
    public function all(): array;
} 