<?php

namespace Database\Seeders;

use App\Models\SessionSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SessionSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the session lifetime setting if it doesn't exist
        SessionSetting::firstOrCreate(
            ['key' => 'session_lifetime'],
            [
                'value' => '120', // 2 hours default
                'description' => 'Session lifetime in minutes. How long a user can be inactive before being logged out.',
            ]
        );
    }
}
