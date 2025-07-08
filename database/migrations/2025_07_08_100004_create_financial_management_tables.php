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
        // Create financial reporting and analytics tables
        
        // Create exchange rates table for multi-currency support
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->string('from_currency', 3); // USD, EUR, etc.
            $table->string('to_currency', 3); // EGP (base currency)
            $table->decimal('rate', 12, 6);
            $table->decimal('buy_rate', 12, 6)->nullable();
            $table->decimal('sell_rate', 12, 6)->nullable();
            $table->string('source')->default('manual'); // manual, api, bank
            $table->boolean('is_active')->default(true);
            $table->timestamp('effective_from');
            $table->timestamp('effective_until')->nullable();
            $table->timestamps();

            // Indexes for rate queries
            $table->index(['from_currency', 'to_currency', 'effective_from'], 'idx_exchange_rates_currencies_date');
            $table->index(['is_active', 'effective_from'], 'idx_exchange_rates_active_date');
        });

        // Create transaction fees table for flexible fee structure
        Schema::create('transaction_fees', function (Blueprint $table) {
            $table->id();
            $table->string('fee_name');
            $table->string('transaction_type'); // Transfer, Withdrawal, Deposit, etc.
            $table->string('calculation_method'); // fixed, percentage, tiered
            $table->decimal('fixed_amount', 10, 2)->nullable();
            $table->decimal('percentage_rate', 5, 4)->nullable(); // 0.0250 = 2.5%
            $table->decimal('minimum_fee', 10, 2)->default(0);
            $table->decimal('maximum_fee', 10, 2)->nullable();
            $table->json('tiered_structure')->nullable(); // For tiered fee calculations
            $table->decimal('amount_threshold_min', 15, 2)->nullable();
            $table->decimal('amount_threshold_max', 15, 2)->nullable();
            $table->string('customer_type')->nullable(); // regular, vip, corporate
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->timestamp('effective_from');
            $table->timestamp('effective_until')->nullable();
            $table->timestamps();

            // Indexes for fee calculations
            $table->index(['transaction_type', 'is_active', 'effective_from'], 'idx_transaction_fees_type_active_date');
            $table->index(['branch_id', 'is_active'], 'idx_transaction_fees_branch_active');
        });

        // Create compliance reports table
        Schema::create('compliance_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_type'); // aml, suspicious_activity, large_transactions, etc.
            $table->string('report_period'); // daily, weekly, monthly, quarterly, yearly
            $table->date('period_start');
            $table->date('period_end');
            $table->foreignId('generated_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('cascade');
            $table->string('status')->default('generating'); // generating, completed, failed, reviewed
            $table->json('report_data')->nullable();
            $table->string('file_path')->nullable();
            $table->text('summary')->nullable();
            $table->integer('total_records')->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->timestamp('generated_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamps();

            // Indexes for report management
            $table->index(['report_type', 'period_start', 'period_end'], 'idx_compliance_reports_type_period');
            $table->index(['status', 'generated_at'], 'idx_compliance_reports_status_date');
            $table->index(['branch_id', 'report_period'], 'idx_compliance_reports_branch_period');
        });

        // Create financial limits table for flexible limit management
        Schema::create('financial_limits', function (Blueprint $table) {
            $table->id();
            $table->string('limit_type'); // daily_withdrawal, monthly_transfer, etc.
            $table->string('applies_to'); // user, role, branch, customer_type
            $table->unsignedBigInteger('applies_to_id')->nullable(); // ID of the entity
            $table->decimal('limit_amount', 15, 2);
            $table->string('currency', 3)->default('EGP');
            $table->string('period_type'); // daily, weekly, monthly, yearly
            $table->boolean('is_active')->default(true);
            $table->timestamp('effective_from');
            $table->timestamp('effective_until')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->timestamps();

            // Indexes for limit checks
            $table->index(['limit_type', 'applies_to', 'applies_to_id', 'is_active'], 'idx_financial_limits_entity_active');
            $table->index(['effective_from', 'effective_until'], 'idx_financial_limits_period');
        });

        // Create transaction schedules table for recurring transactions
        Schema::create('transaction_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('schedule_name');
            $table->string('schedule_type'); // recurring, one_time
            $table->string('frequency')->nullable(); // daily, weekly, monthly, yearly
            $table->integer('frequency_interval')->default(1); // Every X days/weeks/months
            $table->json('frequency_details')->nullable(); // Specific days, dates, etc.
            $table->foreignId('template_transaction_id')->nullable()->constrained('transactions')->onDelete('cascade');
            $table->string('transaction_type');
            $table->decimal('amount', 15, 2);
            $table->decimal('commission', 15, 2)->default(0);
            $table->string('customer_name');
            $table->string('customer_mobile_number');
            $table->foreignId('agent_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('safe_id')->constrained('safes')->onDelete('cascade');
            $table->timestamp('start_date');
            $table->timestamp('end_date')->nullable();
            $table->timestamp('next_execution')->nullable();
            $table->timestamp('last_execution')->nullable();
            $table->integer('execution_count')->default(0);
            $table->integer('max_executions')->nullable();
            $table->string('status')->default('active'); // active, paused, completed, cancelled
            $table->boolean('requires_approval')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes for schedule processing
            $table->index(['status', 'next_execution'], 'idx_transaction_schedules_status_next');
            $table->index(['agent_id', 'status'], 'idx_transaction_schedules_agent_status');
        });

        // Create performance metrics table for KPI tracking
        Schema::create('performance_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('metric_type'); // agent_performance, branch_performance, system_performance
            $table->string('metric_name'); // transactions_per_day, revenue_generated, etc.
            $table->string('entity_type'); // user, branch, system
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->date('metric_date');
            $table->decimal('metric_value', 15, 4);
            $table->string('metric_unit')->nullable(); // count, amount, percentage, etc.
            $table->json('additional_data')->nullable();
            $table->timestamps();

            // Unique constraint to prevent duplicate metrics
            $table->unique(['metric_type', 'metric_name', 'entity_type', 'entity_id', 'metric_date'], 'unq_performance_metrics');
            
            // Indexes for metric queries
            $table->index(['entity_type', 'entity_id', 'metric_date'], 'idx_performance_metrics_entity_date');
            $table->index(['metric_type', 'metric_date'], 'idx_performance_metrics_type_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_metrics');
        Schema::dropIfExists('transaction_schedules');
        Schema::dropIfExists('financial_limits');
        Schema::dropIfExists('compliance_reports');
        Schema::dropIfExists('transaction_fees');
        Schema::dropIfExists('exchange_rates');
    }
};
