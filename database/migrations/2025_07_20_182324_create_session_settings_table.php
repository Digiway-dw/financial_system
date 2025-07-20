<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            Schema::create('session_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->string('value');
                $table->text('description')->nullable();
                $table->timestamps();
            });

            // Insert default session lifetime value (in minutes)
            DB::table('session_settings')->insert([
                'key' => 'session_lifetime',
                'value' => '120', // 2 hours default
                'description' => 'Session lifetime in minutes. How long a user can be inactive before being logged out.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Table already exists, check if the setting exists
            try {
                $setting = DB::table('session_settings')->where('key', '=', 'session_lifetime')->first();
                if (!$setting) {
                    DB::table('session_settings')->insert([
                        'key' => 'session_lifetime',
                        'value' => '120', // 2 hours default
                        'description' => 'Session lifetime in minutes. How long a user can be inactive before being logged out.',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } catch (\Exception $e) {
                // Something else went wrong, but we can continue
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_settings');
    }
};
