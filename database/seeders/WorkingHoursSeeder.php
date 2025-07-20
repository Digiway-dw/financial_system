<?php

namespace Database\Seeders;

use App\Domain\Entities\User;
use App\Models\WorkingHour;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class WorkingHoursSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding working hours for demo users...');

        // Get all non-admin users
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'admin');
        })->get();

        if ($users->isEmpty()) {
            $this->command->error('No users found to assign working hours.');
            return;
        }

        $count = 0;

        // Default working days (Monday to Friday)
        $workingDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

        foreach ($users as $user) {
            $this->command->info("Setting up working hours for user: {$user->name} (ID: {$user->id})");

            // Create working hours for each day
            foreach ($workingDays as $day) {
                // Check if working hours already exist for this user and day
                $exists = WorkingHour::where('user_id', $user->id)
                    ->where('day_of_week', $day)
                    ->exists();

                if (!$exists) {
                    try {
                        WorkingHour::create([
                            'user_id' => $user->id,
                            'day_of_week' => $day,
                            'start_time' => '09:00',
                            'end_time' => '17:00',
                            'is_enabled' => true,
                        ]);

                        $count++;
                    } catch (\Exception $e) {
                        Log::error("Failed to create working hours for user {$user->id} on {$day}: " . $e->getMessage());
                        $this->command->error("Error for user {$user->name} on {$day}: {$e->getMessage()}");
                    }
                }
            }
        }

        $this->command->info("Successfully created {$count} working hour entries.");
    }
}
