<?php

namespace App\Application\UseCases;

use App\Domain\Entities\User;
use App\Domain\Interfaces\UserRepository;

class CreateUser
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(string $name, string $email, string $password, string $role, ?int $branch_id = null): User
    {
        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->password = bcrypt($password);
        $user->role = $role;
        $user->branch_id = $branch_id;
        return $this->userRepository->save($user);
    }
} 