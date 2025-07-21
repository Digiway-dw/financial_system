<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Drop the existing constraint if it exists
        try {
            DB::statement('ALTER TABLE `lines` DROP CONSTRAINT chk_line_network');
        } catch (\Exception $e) {
            // Constraint might not exist, continue
        }
        
        // Add the updated constraint with 'Fawry' included
        DB::statement("ALTER TABLE `lines` ADD CONSTRAINT chk_line_network CHECK (network IN ('orange', 'vodafone', 'etisalat', 'we', 'Fawry'))");
    }

    public function down()
    {
        // Drop the updated constraint
        try {
            DB::statement('ALTER TABLE `lines` DROP CONSTRAINT chk_line_network');
        } catch (\Exception $e) {
            // Constraint might not exist, continue
        }
        
        // Restore the old constraint (without 'Fawry')
        DB::statement("ALTER TABLE `lines` ADD CONSTRAINT chk_line_network CHECK (network IN ('orange', 'vodafone', 'etisalat', 'we'))");
    }
}; 