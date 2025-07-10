<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Constants\Roles;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, check if we're using Spatie Permission package
        if (Schema::hasTable('roles')) {
            // We have a roles table, likely from Spatie Permission
            $this->insertSpatieRoles();
        } else {
            // Create a basic roles table if it doesn't exist
            $this->createRolesTable();
        }
    }

    /**
     * Insert roles using Spatie Permission package
     */
    private function insertSpatieRoles(): void
    {
        $roles = Roles::all();

        foreach ($roles as $role) {
            // Check if role already exists before inserting
            $roleExists = DB::table('roles')->where('name', $role)->exists();

            if (!$roleExists) {
                DB::table('roles')->insert([
                    'name' => $role,
                    'guard_name' => 'web',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Log creation in console if running through CLI
                if (app()->runningInConsole()) {
                    echo "Role '{$role}' created successfully.\n";
                }
            } else {
                // Log if role already exists
                if (app()->runningInConsole()) {
                    echo "Role '{$role}' already exists.\n";
                }
            }
        }
    }

    /**
     * Create a basic roles table and insert roles
     */
    private function createRolesTable(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        $roles = Roles::all();
        $descriptions = [
            Roles::ADMIN => 'Full permissions and unrestricted cash withdrawal.',
            Roles::GENERAL_SUPERVISOR => 'All permissions across all branches except managing roles and unrestricted cash withdrawal.',
            Roles::AUDITOR => 'Can approve/reject transactions and edit daily transactions in all branches.',
            Roles::BRANCH_MANAGER => 'Manages branch operations, approves transactions, and generates branch reports.',
            Roles::AGENT => 'Handles regular transactions and requires approval for cash withdrawals.',
            Roles::TRAINEE => 'Can perform transactions only after approval by Admin, Auditor, or Branch Manager.',
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insert([
                'name' => $role,
                'description' => $descriptions[$role] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create a role_user pivot table for user-role assignments
        Schema::create('role_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Each user can have a role only once
            $table->unique(['role_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't drop Spatie tables if they exist
        if (Schema::hasTable('roles') && !Schema::hasTable('permissions')) {
            Schema::dropIfExists('role_user');
            Schema::dropIfExists('roles');
        } else {
            // For Spatie, just remove our specific roles
            $roles = Roles::all();

            foreach ($roles as $role) {
                DB::table('roles')->where('name', $role)->delete();
            }
        }
    }
};
