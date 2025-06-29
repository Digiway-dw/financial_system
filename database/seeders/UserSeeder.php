<?php

namespace Database\Seeders;

use App\Application\UseCases\CreateUser;
use App\Application\UseCases\CreateBranch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    private CreateUser $createUserUseCase;
    private CreateBranch $createBranchUseCase;

    public function __construct(CreateUser $createUserUseCase, CreateBranch $createBranchUseCase)
    {
        $this->createUserUseCase = $createUserUseCase;
        $this->createBranchUseCase = $createBranchUseCase;
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

        // Create a branch first
        $branch = $this->createBranchUseCase->execute([
            'name' => 'Main Branch',
            'location' => 'Downtown',
            'branch_code' => 'BR001'
        ]);

        $this->createUserUseCase->execute(
            name: 'Branch Manager User',
            email: 'manager@example.com',
            password: 'password',
            role: 'branch_manager',
            branch_id: $branch->id
        );

        $this->createUserUseCase->execute(
            name: 'Agent User',
            email: 'agent@example.com',
            password: 'password',
            role: 'agent',
            branch_id: $branch->id
        );

        $this->createUserUseCase->execute(
            name: 'Trainee User',
            email: 'trainee@example.com',
            password: 'password',
            role: 'trainee',
            branch_id: $branch->id
        );
    }
}
