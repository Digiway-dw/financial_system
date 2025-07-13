<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the old constraint if it exists
        try {
            DB::statement("ALTER TABLE `lines` DROP CONSTRAINT `chk_line_status`");
        } catch (\Throwable $e) {
            // Ignore if not exists
        }
        // Add the new constraint with all valid statuses
        DB::statement("ALTER TABLE `lines` ADD CONSTRAINT chk_line_status CHECK (status IN ('active', 'inactive', 'suspended', 'maintenance', 'frozen'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new constraint
        try {
            DB::statement("ALTER TABLE `lines` DROP CONSTRAINT `chk_line_status`");
        } catch (\Throwable $e) {
            // Ignore if not exists
        }
        // Restore the old constraint (only active, inactive, suspended, maintenance)
        DB::statement("ALTER TABLE `lines` ADD CONSTRAINT chk_line_status CHECK (status IN ('active', 'inactive', 'suspended', 'maintenance'))");
    }
}; 