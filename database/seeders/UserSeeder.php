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
    }
}
