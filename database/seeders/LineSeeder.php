<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Domain\Entities\Line;
use App\Models\Domain\Entities\Branch;

class LineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first branch or create one if none exists
        $branch = Branch::first();
        if (!$branch) {
            $branch = Branch::create([
                'name' => 'Main Branch',
                'location' => 'Cairo',
                'branch_code' => 'MN001', // Fixed to match constraint: 2 letters + 3 digits
            ]);
        }

        // Create sample lines
        $lines = [
            [
                'mobile_number' => '+201234567890',
                'current_balance' => 1500.00,
                'daily_limit' => 5000.00,
                'monthly_limit' => 150000.00,
                'daily_usage' => 2300.00,
                'monthly_usage' => 45000.00,
                'network' => 'vodafone',
                'status' => 'active',
                'branch_id' => $branch->id,
            ],
            [
                'mobile_number' => '+201234567891',
                'current_balance' => 2800.00,
                'daily_limit' => 7000.00,
                'monthly_limit' => 200000.00,
                'daily_usage' => 1200.00,
                'monthly_usage' => 38000.00,
                'network' => 'orange',
                'status' => 'active',
                'branch_id' => $branch->id,
            ],
            [
                'mobile_number' => '+201234567892',
                'current_balance' => 850.00,
                'daily_limit' => 3000.00,
                'monthly_limit' => 90000.00,
                'daily_usage' => 1800.00,
                'monthly_usage' => 22000.00,
                'network' => 'etisalat',
                'status' => 'inactive',
                'branch_id' => $branch->id,
            ],
            [
                'mobile_number' => '+201234567893',
                'current_balance' => 3200.00,
                'daily_limit' => 8000.00,
                'monthly_limit' => 250000.00,
                'daily_usage' => 950.00,
                'monthly_usage' => 15000.00,
                'network' => 'we',
                'status' => 'active',
                'branch_id' => $branch->id,
            ],
            [
                'mobile_number' => '+201234567894',
                'current_balance' => 125.00,
                'daily_limit' => 2000.00,
                'monthly_limit' => 60000.00,
                'daily_usage' => 1750.00,
                'monthly_usage' => 35000.00,
                'network' => 'vodafone',
                'status' => 'active',
                'branch_id' => $branch->id,
            ],
        ];

        foreach ($lines as $lineData) {
            Line::create($lineData);
        }
    }
}
