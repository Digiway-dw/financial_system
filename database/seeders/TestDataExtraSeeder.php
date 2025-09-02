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

class TestDataExtraSeeder extends Seeder
{
    public function run()
    {
        // Create extra test branches
        for ($i = 4; $i <= 6; $i++) {
            Branch::updateOrCreate(
                ['branch_code' => 'TB' . str_pad($i, 3, '0', STR_PAD_LEFT)],
                [
                    'name' => 'test_branch_' . $i,
                    'location' => 'test_location_' . $i,
                    'description' => 'Test branch ' . $i,
                ]
            );
        }

        // Create extra test users for each role
        $roles = ['admin', 'agent', 'general_supervisor', 'branch_manager', 'trainee', 'auditor'];
        foreach ($roles as $idx => $role) {
            $user = User::updateOrCreate(
                ['email' => 'test_' . $role . '_' . ($idx + 7) . '@example.com'],
                [
                    'name' => 'test_' . $role . '_' . ($idx + 7),
                    'password' => Hash::make('password'),
                    'branch_id' => Branch::inRandomOrder()->first()->id,
                ]
            );
            $user->assignRole($role);
        }

        // Create extra test customers
        for ($i = 6; $i <= 10; $i++) {
            \App\Models\Domain\Entities\Customer::updateOrCreate(
                ['mobile_number' => '01000000' . str_pad($i, 2, '0', STR_PAD_LEFT)],
                [
                    'name' => 'test_customer_' . $i,
                    'customer_code' => 'TC' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'gender' => 'female',
                    'is_client' => true,
                    'balance' => rand(100, 1000),
                    'branch_id' => Branch::inRandomOrder()->first()->id,
                ]
            );
        }

        // Create extra test lines
        $networks = ['orange', 'vodafone', 'etisalat', 'we', 'Fawry'];
        for ($i = 6; $i <= 10; $i++) {
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

        // Create extra test transactions
        $customers = \App\Models\Domain\Entities\Customer::all();
        $lines = Line::all();
        $users = User::all();
        $transactionTypes = ['Transfer', 'Withdrawal', 'Deposit', 'Adjustment', 'Receive'];
        for ($i = 11; $i <= 20; $i++) {
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

        // Create extra test working hours for users
        $users = User::all();
        $days = ['saturday', 'sunday'];
        foreach ($users as $user) {
            foreach ($days as $day) {
                WorkingHour::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'day_of_week' => $day,
                    ],
                    [
                        'start_time' => '10:00',
                        'end_time' => '16:00',
                        'is_enabled' => true,
                    ]
                );
            }
        }
    }
}
