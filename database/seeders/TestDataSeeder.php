<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Domain\Entities\Branch;
use App\Domain\Entities\User;
use App\Models\Domain\Entities\Line;
use App\Models\WorkingHour;
use App\Models\Domain\Entities\Transaction;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class TestDataSeeder extends Seeder
{
    public function run()
    {
        // Create test branches
        for ($i = 1; $i <= 3; $i++) {
            Branch::updateOrCreate(
                ['branch_code' => 'TB' . str_pad($i, 3, '0', STR_PAD_LEFT)],
                [
                    'name' => 'test_branch_' . $i,
                    'location' => 'test_location_' . $i,
                    'description' => 'Test branch ' . $i,
                ]
            );
        }

        // Create test users for each role
        $roles = ['admin', 'agent', 'general_supervisor', 'branch_manager', 'trainee', 'auditor'];
        foreach ($roles as $idx => $role) {
            $user = User::updateOrCreate(
                ['email' => 'test_' . $role . '_' . ($idx + 1) . '@example.com'],
                [
                    'name' => 'test_' . $role . '_' . ($idx + 1),
                    'password' => Hash::make('password'),
                    'branch_id' => Branch::inRandomOrder()->first()->id,
                ]
            );
            $user->assignRole($role);
        }

        // Create test customers
        for ($i = 1; $i <= 5; $i++) {
            \App\Models\Domain\Entities\Customer::updateOrCreate(
                ['mobile_number' => '01000000' . str_pad($i, 2, '0', STR_PAD_LEFT)],
                [
                    'name' => 'test_customer_' . $i,
                    'customer_code' => 'TC' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'gender' => 'male',
                    'is_client' => true,
                    'balance' => rand(100, 1000),
                    'branch_id' => Branch::inRandomOrder()->first()->id,
                ]
            );
        }

        // Create test lines
        $networks = ['orange', 'vodafone', 'etisalat', 'we', 'Fawry'];
        for ($i = 1; $i <= 5; $i++) {
            Line::updateOrCreate(
                ['mobile_number' => '01100000' . str_pad($i, 2, '0', STR_PAD_LEFT)],
                [
                    'network' => $networks[$i % count($networks)],
                    'current_balance' => rand(100, 1000),
                    'daily_limit' => 500,
                    'monthly_limit' => 5000,
                    'branch_id' => Branch::inRandomOrder()->first()->id,
                    'status' => 'active',
                ]
            );
        }

        // Create test transactions
        $customers = \App\Models\Domain\Entities\Customer::all();
        $lines = Line::all();
        $users = User::all();
        $transactionTypes = ['Transfer', 'Withdrawal', 'Deposit', 'Adjustment', 'Receive'];
        for ($i = 1; $i <= 10; $i++) {
            $customer = $customers->random();
            $line = $lines->random();
            $user = $users->random();
            Transaction::updateOrCreate(
                ['reference_number' => 'test_tx_' . $i],
                [
                    'customer_name' => $customer->name,
                    'customer_mobile_number' => $customer->mobile_number,
                    'line_id' => $line->id,
                    'customer_code' => $customer->customer_code,
                    'amount' => rand(100, 1000),
                    'transaction_type' => $transactionTypes[$i % count($transactionTypes)],
                    'agent_id' => $user->id,
                    'transaction_date_time' => now('Africa/Cairo'),
                    'status' => 'completed',
                    'safe_id' => null,
                    'branch_id' => $line->branch_id,
                ]
            );
        }

        // Create test working hours for users
        $users = User::all();
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        foreach ($users as $user) {
            foreach ($days as $day) {
                WorkingHour::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'day_of_week' => $day,
                    ],
                    [
                        'start_time' => '09:00',
                        'end_time' => '17:00',
                        'is_enabled' => true,
                    ]
                );
            }
        }
    }
}
