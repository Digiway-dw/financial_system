<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Update all users to have the email 'admin@example.com'
            // and set their password to 'password'.
            // Note: This is a destructive operation and should be used with caution in production.
            DB::table('users')->update([
                'email' => 'admin@example.com',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No direct way to reverse the specific email/password change for all users
        // without knowing their original credentials.
        // If this migration is rolled back, the email and password will remain 'admin@example.com' and 'password'.
    }
};
