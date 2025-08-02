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
        Schema::create('custom_expense_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // The custom expense type name
            $table->string('name_ar')->unique(); // Arabic name for display
            $table->integer('usage_count')->default(1); // How many times this type has been used
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_expense_types');
    }
};
