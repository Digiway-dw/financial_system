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
        // Create stored procedures for common operations
        
        // Stored procedure for safe balance reconciliation
        DB::statement("DROP PROCEDURE IF EXISTS ReconcileSafeBalance");
        DB::statement("
            CREATE PROCEDURE ReconcileSafeBalance(
                IN safe_id_param INT,
                IN reconciled_by_param INT,
                OUT result_message VARCHAR(255)
            )
            BEGIN
                DECLARE calculated_balance DECIMAL(15,2);
                DECLARE current_balance DECIMAL(15,2);
                DECLARE balance_difference DECIMAL(15,2);
                DECLARE transaction_count INT;
                
                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    SET result_message = 'Error occurred during reconciliation';
                END;
                
                START TRANSACTION;
                
                -- Calculate balance based on transactions
                SELECT COALESCE(SUM(
                    CASE 
                        WHEN transaction_type = 'Deposit' THEN amount
                        WHEN transaction_type = 'Withdrawal' THEN -amount
                        WHEN transaction_type = 'Transfer' AND destination_safe_id = safe_id_param THEN amount
                        WHEN transaction_type = 'Transfer' AND safe_id = safe_id_param THEN -amount
                        WHEN transaction_type = 'Adjustment' THEN amount
                        ELSE 0
                    END
                ), 0) INTO calculated_balance
                FROM transactions 
                WHERE (safe_id = safe_id_param OR destination_safe_id = safe_id_param) 
                AND status = 'completed' 
                AND deleted_at IS NULL;
                
                -- Get current balance
                SELECT current_balance INTO current_balance FROM safes WHERE id = safe_id_param;
                
                -- Calculate difference
                SET balance_difference = calculated_balance - current_balance;
                
                -- Update safe with reconciliation info
                UPDATE safes 
                SET 
                    current_balance = calculated_balance,
                    last_reconciliation_at = NOW(),
                    reconciled_by = reconciled_by_param
                WHERE id = safe_id_param;
                
                -- Log reconciliation in system events
                INSERT INTO system_events (
                    event_type, event_category, severity, user_id, subject_type, subject_id,
                    event_data, occurred_at, created_at, updated_at
                ) VALUES (
                    'reconciliation', 'financial', 'medium', reconciled_by_param, 'App\\Models\\Domain\\Entities\\Safe', safe_id_param,
                    JSON_OBJECT(
                        'previous_balance', current_balance,
                        'calculated_balance', calculated_balance,
                        'difference', balance_difference
                    ),
                    NOW(), NOW(), NOW()
                );
                
                COMMIT;
                SET result_message = CONCAT('Reconciliation completed. Difference: ', balance_difference);
            END
        ");

        // Stored procedure for calculating agent commission
        DB::statement("DROP PROCEDURE IF EXISTS CalculateAgentCommission");
        DB::statement("
            CREATE PROCEDURE CalculateAgentCommission(
                IN agent_id_param INT,
                IN start_date_param DATE,
                IN end_date_param DATE,
                OUT total_commission DECIMAL(15,2),
                OUT transaction_count INT
            )
            BEGIN
                SELECT 
                    COALESCE(SUM(commission), 0),
                    COUNT(*)
                INTO total_commission, transaction_count
                FROM transactions 
                WHERE agent_id = agent_id_param 
                AND DATE(transaction_date_time) BETWEEN start_date_param AND end_date_param
                AND status = 'completed'
                AND deleted_at IS NULL;
            END
        ");

        // Stored procedure for checking transaction limits
        DB::statement("DROP PROCEDURE IF EXISTS CheckTransactionLimits");
        DB::statement("
            CREATE PROCEDURE CheckTransactionLimits(
                IN agent_id_param INT,
                IN amount_param DECIMAL(15,2),
                IN transaction_type_param VARCHAR(50),
                OUT limit_exceeded BOOLEAN,
                OUT limit_message VARCHAR(255)
            )
            BEGIN
                DECLARE daily_total DECIMAL(15,2);
                DECLARE monthly_total DECIMAL(15,2);
                DECLARE daily_limit DECIMAL(15,2);
                DECLARE monthly_limit DECIMAL(15,2);
                DECLARE user_role VARCHAR(50);
                
                SET limit_exceeded = FALSE;
                SET limit_message = 'Transaction within limits';
                
                -- Get user role
                SELECT r.name INTO user_role 
                FROM users u 
                JOIN model_has_roles mhr ON u.id = mhr.model_id 
                JOIN roles r ON mhr.role_id = r.id 
                WHERE u.id = agent_id_param AND mhr.model_type = 'App\\Domain\\Entities\\User'
                LIMIT 1;
                
                -- Calculate daily total
                SELECT COALESCE(SUM(amount), 0) INTO daily_total
                FROM transactions 
                WHERE agent_id = agent_id_param 
                AND DATE(transaction_date_time) = CURDATE()
                AND transaction_type = transaction_type_param
                AND status IN ('completed', 'pending')
                AND deleted_at IS NULL;
                
                -- Calculate monthly total
                SELECT COALESCE(SUM(amount), 0) INTO monthly_total
                FROM transactions 
                WHERE agent_id = agent_id_param 
                AND YEAR(transaction_date_time) = YEAR(CURDATE())
                AND MONTH(transaction_date_time) = MONTH(CURDATE())
                AND transaction_type = transaction_type_param
                AND status IN ('completed', 'pending')
                AND deleted_at IS NULL;
                
                -- Get limits based on role and transaction type
                SELECT 
                    COALESCE(fl1.limit_amount, 999999999.99),
                    COALESCE(fl2.limit_amount, 999999999.99)
                INTO daily_limit, monthly_limit
                FROM (SELECT 1 as dummy) d
                LEFT JOIN financial_limits fl1 ON fl1.limit_type = CONCAT('daily_', LOWER(transaction_type_param))
                    AND fl1.applies_to = 'role' 
                    AND fl1.applies_to_id = (SELECT id FROM roles WHERE name = user_role)
                    AND fl1.is_active = TRUE
                    AND NOW() BETWEEN fl1.effective_from AND COALESCE(fl1.effective_until, NOW())
                LEFT JOIN financial_limits fl2 ON fl2.limit_type = CONCAT('monthly_', LOWER(transaction_type_param))
                    AND fl2.applies_to = 'role' 
                    AND fl2.applies_to_id = (SELECT id FROM roles WHERE name = user_role)
                    AND fl2.is_active = TRUE
                    AND NOW() BETWEEN fl2.effective_from AND COALESCE(fl2.effective_until, NOW());
                
                -- Check limits
                IF (daily_total + amount_param) > daily_limit THEN
                    SET limit_exceeded = TRUE;
                    SET limit_message = CONCAT('Daily limit exceeded. Limit: ', daily_limit, ', Current: ', daily_total, ', Requested: ', amount_param);
                ELSEIF (monthly_total + amount_param) > monthly_limit THEN
                    SET limit_exceeded = TRUE;
                    SET limit_message = CONCAT('Monthly limit exceeded. Limit: ', monthly_limit, ', Current: ', monthly_total, ', Requested: ', amount_param);
                END IF;
            END
        ");

        // Stored procedure for generating transaction reference number
        DB::statement("DROP PROCEDURE IF EXISTS GenerateTransactionReference");
        DB::statement("
            CREATE PROCEDURE GenerateTransactionReference(
                IN branch_code_param VARCHAR(10),
                OUT reference_number VARCHAR(50)
            )
            BEGIN
                DECLARE sequence_num INT;
                DECLARE date_part VARCHAR(8);
                
                SET date_part = DATE_FORMAT(NOW(), '%Y%m%d');
                
                -- Get next sequence number for today
                SELECT COALESCE(MAX(CAST(SUBSTRING(reference_number, -6) AS UNSIGNED)), 0) + 1 
                INTO sequence_num
                FROM transactions 
                WHERE reference_number LIKE CONCAT(branch_code_param, '-', date_part, '-%')
                AND DATE(created_at) = CURDATE();
                
                SET reference_number = CONCAT(branch_code_param, '-', date_part, '-', LPAD(sequence_num, 6, '0'));
            END
        ");

        // Stored procedure for updating line usage
        DB::statement("DROP PROCEDURE IF EXISTS UpdateLineUsage");
        DB::statement("
            CREATE PROCEDURE UpdateLineUsage(
                IN line_id_param INT,
                IN amount_param DECIMAL(15,2)
            )
            BEGIN
                DECLARE current_daily_usage DECIMAL(15,2);
                DECLARE current_monthly_usage DECIMAL(15,2);
                
                -- Get current usage
                SELECT daily_usage, monthly_usage 
                INTO current_daily_usage, current_monthly_usage
                FROM `lines` 
                WHERE id = line_id_param;
                
                -- Update usage
                UPDATE `lines` 
                SET 
                    daily_usage = current_daily_usage + amount_param,
                    monthly_usage = current_monthly_usage + amount_param,
                    updated_at = NOW()
                WHERE id = line_id_param;
                
                -- Check if auto-recharge is needed
                IF (current_daily_usage + amount_param) >= (SELECT auto_recharge_threshold FROM `lines` WHERE id = line_id_param) THEN
                    -- Log auto-recharge event
                    INSERT INTO system_events (
                        event_type, event_category, severity, subject_type, subject_id,
                        event_data, occurred_at, created_at, updated_at
                    ) VALUES (
                        'auto_recharge_needed', 'line_management', 'medium', 'App\\\\Models\\\\Domain\\\\Entities\\\\Line', line_id_param,
                        JSON_OBJECT('current_usage', current_daily_usage + amount_param, 'threshold_reached', TRUE),
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
        DB::statement('DROP PROCEDURE IF EXISTS UpdateLineUsage');
        DB::statement('DROP PROCEDURE IF EXISTS GenerateTransactionReference');
        DB::statement('DROP PROCEDURE IF EXISTS CheckTransactionLimits');
        DB::statement('DROP PROCEDURE IF EXISTS CalculateAgentCommission');
        DB::statement('DROP PROCEDURE IF EXISTS ReconcileSafeBalance');
    }
};
