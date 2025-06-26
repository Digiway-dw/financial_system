<?php

namespace App\Domain\Interfaces;

use App\Models\Domain\Entities\Transaction;

interface TransactionRepository
{
    public function create(array $attributes): Transaction;
    public function findById(string $id): ?Transaction;
    public function update(string $id, array $attributes): Transaction;
    public function delete(string $id): void;
    public function all(): array;
} 