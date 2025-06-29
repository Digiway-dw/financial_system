<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\User;
use App\Domain\Interfaces\UserRepository;

class EloquentUserRepository implements UserRepository
{
    public function findById(string $id): ?User
    {
        return User::find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function save(User $user): User
    {
        $user->save();
        return $user;
    }

    public function all(): array
    {
        return User::all()->all();
    }

    public function getUsersByBranch(string $branchId): array
    {
        return User::where('branch_id', $branchId)->get()->all();
    }
} 