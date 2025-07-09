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
        // Create enhanced audit and logging tables for better tracking

        // Create transaction audit table
        Schema::create('transaction_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->string('action'); // created, updated, approved, rejected, cancelled
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->foreignId('performed_by')->constrained('users')->onDelete('cascade');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('reason')->nullable(); // For rejections or modifications
            $table->timestamps();

            // Indexes for audit queries
            $table->index(['transaction_id', 'created_at'], 'idx_transaction_audits_transaction_date');
            $table->index(['performed_by', 'created_at'], 'idx_transaction_audits_user_date');
            $table->index(['action', 'created_at'], 'idx_transaction_audits_action_date');
        });

        // Create system events table for comprehensive logging
        Schema::create('system_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_type'); // login, logout, transaction, approval, system_change
            $table->string('event_category'); // security, financial, administrative, system
            $table->string('severity'); // low, medium, high, critical
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('subject_type')->nullable(); // Model class name
            $table->unsignedBigInteger('subject_id')->nullable(); // Model ID
            $table->json('event_data')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('occurred_at')->useCurrent();
            $table->timestamps();

            // Indexes for event queries
            $table->index(['event_type', 'occurred_at'], 'idx_system_events_type_date');
            $table->index(['event_category', 'severity', 'occurred_at'], 'idx_system_events_category_severity_date');
            $table->index(['user_id', 'occurred_at'], 'idx_system_events_user_date');
            $table->index(['subject_type', 'subject_id'], 'idx_system_events_subject');
        });

        // Create daily summaries table for reporting
        Schema::create('daily_summaries', function (Blueprint $table) {
            $table->id();
            $table->date('summary_date');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('cascade');
            $table->foreignId('agent_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('summary_type'); // branch, agent, system
            $table->integer('total_transactions')->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('total_commission', 15, 2)->default(0);
            $table->decimal('total_deductions', 15, 2)->default(0);
            $table->integer('successful_transactions')->default(0);
            $table->integer('failed_transactions')->default(0);
            $table->integer('pending_transactions')->default(0);
            $table->json('transaction_breakdown')->nullable(); // By type
            $table->timestamps();

            // Unique constraint to prevent duplicate summaries
            $table->unique(['summary_date', 'branch_id', 'agent_id', 'summary_type'], 'unq_daily_summaries');

            // Indexes for summary queries
            $table->index(['summary_date', 'summary_type'], 'idx_daily_summaries_date_type');
            $table->index(['branch_id', 'summary_date'], 'idx_daily_summaries_branch_date');
            $table->index(['agent_id', 'summary_date'], 'idx_daily_summaries_agent_date');
        });

        // Create security alerts table
        Schema::create('security_alerts', function (Blueprint $table) {
            $table->id();
            $table->string('alert_type'); // suspicious_activity, failed_login, large_transaction, etc.
            $table->string('severity'); // low, medium, high, critical
            $table->string('status')->default('open'); // open, investigating, resolved, false_positive
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->onDelete('set null');
            $table->string('title');
            $table->text('description');
            $table->json('alert_data')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('detected_at')->useCurrent();
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamps();

            // Indexes for alert management
            $table->index(['status', 'severity', 'detected_at'], 'idx_security_alerts_status_severity_date');
            $table->index(['alert_type', 'detected_at'], 'idx_security_alerts_type_date');
            $table->index(['assigned_to', 'status'], 'idx_security_alerts_assigned_status');
        });

        // Create notification preferences table
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('notification_type'); // email, sms, in_app, push
            $table->string('event_category'); // transactions, security, system, reports
            $table->string('event_type'); // specific event types
            $table->boolean('enabled')->default(true);
            $table->json('conditions')->nullable(); // When to send notification
            $table->timestamps();

            // Unique constraint for user preferences
            $table->unique(['user_id', 'notification_type', 'event_category', 'event_type'], 'unq_notification_preferences');

            // Index for preference queries
            $table->index(['user_id', 'enabled'], 'idx_notification_preferences_user_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
        Schema::dropIfExists('security_alerts');
        Schema::dropIfExists('daily_summaries');
        Schema::dropIfExists('system_events');
        Schema::dropIfExists('transaction_audits');
    }
};
