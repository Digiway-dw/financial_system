<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_number')->nullable();
            $table->string('national_number', 14)->nullable();
            $table->decimal('salary', 12, 2)->nullable();
            $table->string('address')->nullable();
            $table->string('land_number')->nullable();
            $table->string('relative_phone_number')->nullable();
            $table->text('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone_number',
                'national_number',
                'salary',
                'address',
                'land_number',
                'relative_phone_number',
                'notes',
            ]);
        });
    }
}; 