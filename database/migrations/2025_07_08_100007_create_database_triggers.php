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
        // Create database triggers for automatic data management
        
        // Trigger to automatically update customer transaction count and last transaction date
        DB::statement("DROP TRIGGER IF EXISTS tr_update_customer_stats_after_transaction_insert");
        DB::statement("
            CREATE TRIGGER tr_update_customer_stats_after_transaction_insert
            AFTER INSERT ON transactions
            FOR EACH ROW
            BEGIN
                UPDATE customers 
                SET 
                    transaction_count = transaction_count + 1,
                    last_transaction_at = NEW.transaction_date_time,
                    updated_at = NOW()
                WHERE mobile_number = NEW.customer_mobile_number 
                   OR customer_code = NEW.customer_code;
            END
        ");

        // Trigger to update customer stats when transaction is updated
        DB::statement("DROP TRIGGER IF EXISTS tr_update_customer_stats_after_transaction_update");
        DB::statement("
            CREATE TRIGGER tr_update_customer_stats_after_transaction_update
            AFTER UPDATE ON transactions
            FOR EACH ROW
            BEGIN
                -- Only update if status changed to completed
                IF NEW.status = 'completed' AND OLD.status != 'completed' THEN
                    UPDATE customers 
                    SET 
                        last_transaction_at = NEW.transaction_date_time,
                        updated_at = NOW()
                    WHERE mobile_number = NEW.customer_mobile_number 
                       OR customer_code = NEW.customer_code;
                END IF;
            END
        ");

        // Trigger to automatically update user last activity
        DB::statement("DROP TRIGGER IF EXISTS tr_update_user_last_activity");
        DB::statement("
            CREATE TRIGGER tr_update_user_last_activity
            AFTER INSERT ON transactions
            FOR EACH ROW
            BEGIN
                UPDATE users 
                SET 
                    last_activity_at = NOW(),
                    updated_at = NOW()
                WHERE id = NEW.agent_id;
            END
        ");

        // Trigger to log transaction audit trail
        DB::statement("DROP TRIGGER IF EXISTS tr_transaction_audit_insert");
        DB::statement("
            CREATE TRIGGER tr_transaction_audit_insert
            AFTER INSERT ON transactions
            FOR EACH ROW
            BEGIN
                INSERT INTO transaction_audits (
                    transaction_id, action, new_values, performed_by, 
                    ip_address, created_at, updated_at
                ) VALUES (
                    NEW.id, 'created', 
                    JSON_OBJECT(
                        'customer_name', NEW.customer_name,
                        'customer_mobile_number', NEW.customer_mobile_number,
                        'amount', NEW.amount,
                        'commission', NEW.commission,
                        'transaction_type', NEW.transaction_type,
                        'status', NEW.status
                    ),
                    NEW.agent_id, NEW.source_ip, NOW(), NOW()
                );
            END
        ");

        // Trigger to log transaction audit trail on updates
        DB::statement("DROP TRIGGER IF EXISTS tr_transaction_audit_update");
        DB::statement("
            CREATE TRIGGER tr_transaction_audit_update
            AFTER UPDATE ON transactions
            FOR EACH ROW
            BEGIN
                INSERT INTO transaction_audits (
                    transaction_id, action, old_values, new_values, performed_by,
                    reason, created_at, updated_at
                ) VALUES (
                    NEW.id, 'updated',
                    JSON_OBJECT(
                        'status', OLD.status,
                        'amount', OLD.amount,
                        'commission', OLD.commission
                    ),
                    JSON_OBJECT(
                        'status', NEW.status,
                        'amount', NEW.amount,
                        'commission', NEW.commission
                    ),
                    NEW.agent_id, 
                    CASE 
                        WHEN NEW.status = 'rejected' THEN 'Transaction rejected'
                        ELSE 'Transaction updated'
                    END,
                    NOW(), NOW()
                );
            END
        ");

        // Trigger to automatically generate reference numbers
        DB::statement("DROP TRIGGER IF EXISTS tr_generate_transaction_reference");
        DB::statement("
            CREATE TRIGGER tr_generate_transaction_reference
            BEFORE INSERT ON transactions
            FOR EACH ROW
            BEGIN
                DECLARE branch_code VARCHAR(10);
                DECLARE ref_number VARCHAR(50);
                
                IF NEW.reference_number IS NULL THEN
                    -- Get branch code from agent's branch
                    SELECT b.branch_code INTO branch_code
                    FROM users u 
                    JOIN branches b ON u.branch_id = b.id 
                    WHERE u.id = NEW.agent_id;
                    
                    -- Generate reference number
                    CALL GenerateTransactionReference(branch_code, ref_number);
                    SET NEW.reference_number = ref_number;
                END IF;
            END
        ");

        // Trigger to validate transaction amounts
        DB::statement("DROP TRIGGER IF EXISTS tr_validate_transaction_amounts");
        DB::statement("
            CREATE TRIGGER tr_validate_transaction_amounts
            BEFORE INSERT ON transactions
            FOR EACH ROW
            BEGIN
                -- Ensure amounts are positive
                IF NEW.amount <= 0 THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Transaction amount must be positive';
                END IF;
                
                IF NEW.commission < 0 THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Commission cannot be negative';
                END IF;
                
                IF NEW.deduction < 0 THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Deduction cannot be negative';
                END IF;
                
                -- Validate commission doesn't exceed amount
                IF NEW.commission > NEW.amount THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Commission cannot exceed transaction amount';
                END IF;
            END
        ");

        // Trigger to automatically update safe balances
        DB::statement("DROP TRIGGER IF EXISTS tr_update_safe_balance_after_transaction");
        DB::statement("
            CREATE TRIGGER tr_update_safe_balance_after_transaction
            AFTER UPDATE ON transactions
            FOR EACH ROW
            BEGIN
                -- Only update when status changes to completed
                IF NEW.status = 'completed' AND OLD.status != 'completed' THEN
                    -- Update source safe
                    IF NEW.safe_id IS NOT NULL THEN
                        UPDATE safes 
                        SET current_balance = current_balance - NEW.amount
                        WHERE id = NEW.safe_id;
                    END IF;
                    
                    -- Update destination safe (for transfers)
                    IF NEW.destination_safe_id IS NOT NULL THEN
                        UPDATE safes 
                        SET current_balance = current_balance + NEW.amount
                        WHERE id = NEW.destination_safe_id;
                    END IF;
                END IF;
                
                -- Handle status change from completed to rejected/cancelled
                IF OLD.status = 'completed' AND NEW.status IN ('rejected', 'cancelled') THEN
                    -- Reverse source safe update
                    IF NEW.safe_id IS NOT NULL THEN
                        UPDATE safes 
                        SET current_balance = current_balance + OLD.amount
                        WHERE id = NEW.safe_id;
                    END IF;
                    
                    -- Reverse destination safe update
                    IF NEW.destination_safe_id IS NOT NULL THEN
                        UPDATE safes 
                        SET current_balance = current_balance - OLD.amount
                        WHERE id = NEW.destination_safe_id;
                    END IF;
                END IF;
            END
        ");

        // Trigger to log security events
        DB::statement("DROP TRIGGER IF EXISTS tr_log_security_events");
        DB::statement("
            CREATE TRIGGER tr_log_security_events
            AFTER INSERT ON login_histories
            FOR EACH ROW
            BEGIN
                INSERT INTO system_events (
                    event_type, event_category, severity, user_id,
                    event_data, occurred_at, created_at, updated_at
                ) VALUES (
                    'user_login', 'security', 'low', NEW.user_id,
                    JSON_OBJECT('login_time', NEW.login_at),
                    NEW.login_at, NOW(), NOW()
                );
            END
        ");

        // Trigger to validate line usage limits
        DB::statement("DROP TRIGGER IF EXISTS tr_validate_line_usage");
        DB::statement("
            CREATE TRIGGER tr_validate_line_usage
            BEFORE UPDATE ON `lines`
            FOR EACH ROW
            BEGIN
                -- Check daily limit
                IF NEW.daily_usage > NEW.daily_limit THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Daily usage cannot exceed daily limit';
                END IF;
                
                -- Check monthly limit
                IF NEW.monthly_usage > NEW.monthly_limit THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Monthly usage cannot exceed monthly limit';
                END IF;
                
                -- Log if approaching limits (80% threshold)
                IF NEW.daily_usage >= (NEW.daily_limit * 0.8) AND OLD.daily_usage < (OLD.daily_limit * 0.8) THEN
                    INSERT INTO system_events (
                        event_type, event_category, severity, subject_type, subject_id,
                        event_data, occurred_at, created_at, updated_at
                    ) VALUES (
                        'line_usage_warning', 'line_management', 'medium', 'App\\\\Models\\\\Domain\\\\Entities\\\\Line', NEW.id,
                        JSON_OBJECT('usage_percent', (NEW.daily_usage / NEW.daily_limit) * 100, 'limit_type', 'daily'),
                        NOW(), NOW(), NOW()
                    );
                END IF;
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS tr_validate_line_usage');
        DB::statement('DROP TRIGGER IF EXISTS tr_log_security_events');
        DB::statement('DROP TRIGGER IF EXISTS tr_update_safe_balance_after_transaction');
        DB::statement('DROP TRIGGER IF EXISTS tr_validate_transaction_amounts');
        DB::statement('DROP TRIGGER IF EXISTS tr_generate_transaction_reference');
        DB::statement('DROP TRIGGER IF EXISTS tr_transaction_audit_update');
        DB::statement('DROP TRIGGER IF EXISTS tr_transaction_audit_insert');
        DB::statement('DROP TRIGGER IF EXISTS tr_update_user_last_activity');
        DB::statement('DROP TRIGGER IF EXISTS tr_update_customer_stats_after_transaction_update');
        DB::statement('DROP TRIGGER IF EXISTS tr_update_customer_stats_after_transaction_insert');
    }
};
