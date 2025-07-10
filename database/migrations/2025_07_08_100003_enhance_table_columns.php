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
        // Add missing columns and enhance existing tables

        // Enhance transactions table with additional tracking fields
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('reference_number')->unique()->nullable()->after('id');
            $table->decimal('exchange_rate', 10, 6)->default(1.000000)->after('deduction');
            $table->string('currency', 3)->default('EGP')->after('exchange_rate');
            $table->timestamp('scheduled_at')->nullable()->after('transaction_date_time');
            $table->json('metadata')->nullable()->after('payment_method'); // For additional transaction data
            $table->string('source_ip', 45)->nullable()->after('metadata');
            $table->text('notes')->nullable()->after('source_ip');
        });

        // Enhance users table with additional security and profile fields
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_password_change')->nullable()->after('password');
            $table->boolean('two_factor_enabled')->default(false)->after('last_password_change');
            $table->string('two_factor_secret')->nullable()->after('two_factor_enabled');
            $table->json('two_factor_recovery_codes')->nullable()->after('two_factor_secret');
            $table->timestamp('last_activity_at')->nullable()->after('two_factor_recovery_codes');
            $table->string('preferred_language', 5)->default('en')->after('last_activity_at');
            $table->string('timezone', 50)->default('Africa/Cairo')->after('preferred_language');
            $table->boolean('is_active')->default(true)->after('timezone');
            $table->timestamp('employment_start_date')->nullable()->after('is_active');
            $table->timestamp('employment_end_date')->nullable()->after('employment_start_date');
        });

        // Enhance customers table with additional profile and tracking fields
        Schema::table('customers', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable()->after('gender');
            $table->string('nationality', 3)->default('EGY')->after('date_of_birth'); // ISO country code
            $table->string('id_type')->default('national_id')->after('nationality'); // national_id, passport, other
            $table->string('id_number')->nullable()->after('id_type');
            $table->text('address')->nullable()->after('id_number');
            $table->string('city')->nullable()->after('address');
            $table->string('governorate')->nullable()->after('city');
            $table->string('postal_code', 10)->nullable()->after('governorate');
            $table->string('occupation')->nullable()->after('postal_code');
            $table->string('emergency_contact_name')->nullable()->after('occupation');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            $table->decimal('credit_limit', 12, 2)->default(0)->after('balance');
            $table->string('risk_level')->default('low')->after('credit_limit'); // low, medium, high
            $table->timestamp('last_transaction_at')->nullable()->after('risk_level');
            $table->integer('transaction_count')->default(0)->after('last_transaction_at');
            $table->json('preferences')->nullable()->after('transaction_count'); // Customer preferences
        });

        // Enhance branches table with operational details
        Schema::table('branches', function (Blueprint $table) {
            $table->string('phone_number')->nullable()->after('location');
            $table->string('manager_name')->nullable()->after('phone_number');
            $table->string('manager_phone')->nullable()->after('manager_name');
            $table->text('address')->nullable()->after('manager_phone');
            $table->string('city')->nullable()->after('address');
            $table->string('governorate')->nullable()->after('city');
            $table->time('opening_time')->default('09:00:00')->after('governorate');
            $table->time('closing_time')->default('17:00:00')->after('opening_time');
            $table->json('operating_days')->nullable()->after('closing_time'); // Array of operating days
            $table->boolean('is_active')->default(true)->after('operating_days');
            $table->decimal('monthly_target', 15, 2)->nullable()->after('is_active');
            $table->decimal('commission_rate', 5, 4)->default(0.0250)->after('monthly_target'); // 2.5% default
        });

        // Enhance safes table with additional tracking
        Schema::table('safes', function (Blueprint $table) {
            $table->decimal('opening_balance', 15, 2)->default(0)->after('current_balance');
            $table->decimal('daily_limit', 15, 2)->nullable()->after('opening_balance');
            $table->decimal('minimum_balance', 15, 2)->default(0)->after('daily_limit');
            $table->decimal('maximum_balance', 15, 2)->nullable()->after('minimum_balance');
            $table->timestamp('last_reconciliation_at')->nullable()->after('maximum_balance');
            $table->foreignId('reconciled_by')->nullable()->constrained('users')->after('last_reconciliation_at');
            $table->boolean('requires_dual_approval')->default(false)->after('reconciled_by');
        });

        // Enhance lines table with additional management fields
        Schema::table('lines', function (Blueprint $table) {
            $table->string('line_type')->default('prepaid')->after('network'); // prepaid, postpaid
            $table->decimal('setup_cost', 8, 2)->default(0)->after('line_type');
            $table->decimal('monthly_fee', 8, 2)->default(0)->after('setup_cost');
            $table->date('activation_date')->nullable()->after('monthly_fee');
            $table->date('expiry_date')->nullable()->after('activation_date');
            $table->string('sim_serial_number')->nullable()->after('expiry_date');
            $table->string('puk_code')->nullable()->after('sim_serial_number');
            $table->timestamp('last_recharge_at')->nullable()->after('puk_code');
            $table->decimal('last_recharge_amount', 8, 2)->nullable()->after('last_recharge_at');
            $table->boolean('auto_recharge_enabled')->default(false)->after('last_recharge_amount');
            $table->decimal('auto_recharge_threshold', 8, 2)->nullable()->after('auto_recharge_enabled');
            $table->decimal('auto_recharge_amount', 8, 2)->nullable()->after('auto_recharge_threshold');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'reference_number',
                'exchange_rate',
                'currency',
                'scheduled_at',
                'metadata',
                'source_ip',
                'notes'
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'last_password_change',
                'two_factor_enabled',
                'two_factor_secret',
                'two_factor_recovery_codes',
                'last_activity_at',
                'preferred_language',
                'timezone',
                'is_active',
                'employment_start_date',
                'employment_end_date'
            ]);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'date_of_birth',
                'nationality',
                'id_type',
                'id_number',
                'address',
                'city',
                'governorate',
                'postal_code',
                'occupation',
                'emergency_contact_name',
                'emergency_contact_phone',
                'credit_limit',
                'risk_level',
                'last_transaction_at',
                'transaction_count',
                'preferences'
            ]);
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn([
                'phone_number',
                'manager_name',
                'manager_phone',
                'address',
                'city',
                'governorate',
                'opening_time',
                'closing_time',
                'operating_days',
                'is_active',
                'monthly_target',
                'commission_rate'
            ]);
        });

        Schema::table('safes', function (Blueprint $table) {
            $table->dropColumn([
                'opening_balance',
                'daily_limit',
                'minimum_balance',
                'maximum_balance',
                'last_reconciliation_at',
                'reconciled_by',
                'requires_dual_approval'
            ]);
        });

        Schema::table('lines', function (Blueprint $table) {
            $table->dropColumn([
                'line_type',
                'setup_cost',
                'monthly_fee',
                'activation_date',
                'expiry_date',
                'sim_serial_number',
                'puk_code',
                'last_recharge_at',
                'last_recharge_amount',
                'auto_recharge_enabled',
                'auto_recharge_threshold',
                'auto_recharge_amount'
            ]);
        });
    }
};
