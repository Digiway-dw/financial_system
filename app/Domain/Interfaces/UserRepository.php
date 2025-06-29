<?php

namespace App\Domain\Interfaces;

use App\Domain\Entities\User;

interface UserRepository
{
    public function findById(string $id): ?User;
    public function findByEmail(string $email): ?User;
    public function save(User $user): User;
    public function all(): array;
    public function getUsersByBranch(string $branchId): array;
} 