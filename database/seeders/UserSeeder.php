<?php

namespace Database\Seeders;

use App\Application\UseCases\CreateUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    private CreateUser $createUserUseCase;

    public function __construct(CreateUser $createUserUseCase)
    {
        $this->createUserUseCase = $createUserUseCase;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createUserUseCase->execute(
            name: 'Admin User',
            email: 'admin@example.com',
            password: 'password',
            role: 'admin'
        );

        $this->createUserUseCase->execute(
            name: 'General Supervisor User',
            email: 'supervisor@example.com',
            password: 'password',
            role: 'general_supervisor'
        );

        $this->createUserUseCase->execute(
            name: 'Branch Manager User',
            email: 'manager@example.com',
            password: 'password',
            role: 'branch_manager'
        );

        $this->createUserUseCase->execute(
            name: 'Agent User',
            email: 'agent@example.com',
            password: 'password',
            role: 'agent'
        );

        $this->createUserUseCase->execute(
            name: 'Trainee User',
            email: 'trainee@example.com',
            password: 'password',
            role: 'trainee'
        );
    }
}
