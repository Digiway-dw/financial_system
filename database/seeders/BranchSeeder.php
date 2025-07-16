<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Domain\Entities\Branch;

class BranchSeeder extends Seeder
{
    public function run()
    {
        Branch::firstOrCreate([
            'name' => 'Main Branch',
            'location' => 'Downtown',
            'branch_code' => 'BR001', // This already matches the constraint: 2 letters + 3 digits
        ]);
    }
}
