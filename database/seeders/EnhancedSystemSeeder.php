<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EnhancedSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed exchange rates
        $this->seedExchangeRates();

        // Seed transaction fees
        $this->seedTransactionFees();

        // Seed financial limits
        $this->seedFinancialLimits();

        // Seed notification preferences
        $this->seedNotificationPreferences();

        // Seed sample security alerts
        $this->seedSecurityAlerts();
    }

    private function seedExchangeRates(): void
    {
        $exchangeRates = [
            [
                'from_currency' => 'USD',
                'to_currency' => 'EGP',
                'rate' => 30.85,
                'buy_rate' => 30.80,
                'sell_rate' => 30.90,
                'source' => 'central_bank',
                'effective_from' => now()->subDays(30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'from_currency' => 'EUR',
                'to_currency' => 'EGP',
                'rate' => 33.45,
                'buy_rate' => 33.40,
                'sell_rate' => 33.50,
                'source' => 'central_bank',
                'effective_from' => now()->subDays(30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'from_currency' => 'SAR',
                'to_currency' => 'EGP',
                'rate' => 8.23,
                'buy_rate' => 8.20,
                'sell_rate' => 8.26,
                'source' => 'central_bank',
                'effective_from' => now()->subDays(30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('exchange_rates')->insert($exchangeRates);
    }

    private function seedTransactionFees(): void
    {
        $transactionFees = [
            [
                'fee_name' => 'Standard Transfer Fee',
                'transaction_type' => 'Transfer',
                'calculation_method' => 'percentage',
                'fixed_amount' => null,
                'percentage_rate' => 0.0250, // 2.5%
                'minimum_fee' => 5.00,
                'maximum_fee' => 100.00,
                'amount_threshold_min' => 100.00,
                'amount_threshold_max' => null,
                'customer_type' => 'regular',
                'is_active' => true,
                'effective_from' => now()->subDays(30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'fee_name' => 'VIP Transfer Fee',
                'transaction_type' => 'Transfer',
                'calculation_method' => 'percentage',
                'fixed_amount' => null,
                'percentage_rate' => 0.0150, // 1.5%
                'minimum_fee' => 3.00,
                'maximum_fee' => 50.00,
                'amount_threshold_min' => 1000.00,
                'amount_threshold_max' => null,
                'customer_type' => 'vip',
                'is_active' => true,
                'effective_from' => now()->subDays(30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'fee_name' => 'Withdrawal Fee',
                'transaction_type' => 'Withdrawal',
                'calculation_method' => 'fixed',
                'fixed_amount' => 10.00,
                'percentage_rate' => null,
                'minimum_fee' => 10.00,
                'maximum_fee' => 10.00,
                'amount_threshold_min' => 50.00,
                'amount_threshold_max' => null,
                'customer_type' => null,
                'is_active' => true,
                'effective_from' => now()->subDays(30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'fee_name' => 'Large Withdrawal Fee',
                'transaction_type' => 'Withdrawal',
                'calculation_method' => 'percentage',
                'fixed_amount' => null,
                'percentage_rate' => 0.0100, // 1%
                'minimum_fee' => 50.00,
                'maximum_fee' => 500.00,
                'amount_threshold_min' => 10000.00,
                'amount_threshold_max' => null,
                'customer_type' => null,
                'is_active' => true,
                'effective_from' => now()->subDays(30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('transaction_fees')->insert($transactionFees);
    }

    private function seedFinancialLimits(): void
    {
        // Get role IDs
        $adminRoleId = DB::table('roles')->where('name', 'admin')->value('id');
        $agentRoleId = DB::table('roles')->where('name', 'agent')->value('id');
        $traineeRoleId = DB::table('roles')->where('name', 'trainee')->value('id');
        $branchManagerRoleId = DB::table('roles')->where('name', 'branch_manager')->value('id');

        $financialLimits = [
            // Agent limits
            [
                'limit_type' => 'daily_withdrawal',
                'applies_to' => 'role',
                'applies_to_id' => $agentRoleId,
                'limit_amount' => 50000.00,
                'currency' => 'EGP',
                'period_type' => 'daily',
                'is_active' => true,
                'effective_from' => now()->subDays(30),
                'created_by' => 1,
                'description' => 'Daily withdrawal limit for agents',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'limit_type' => 'monthly_transfer',
                'applies_to' => 'role',
                'applies_to_id' => $agentRoleId,
                'limit_amount' => 1000000.00,
                'currency' => 'EGP',
                'period_type' => 'monthly',
                'is_active' => true,
                'effective_from' => now()->subDays(30),
                'created_by' => 1,
                'description' => 'Monthly transfer limit for agents',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Trainee limits
            [
                'limit_type' => 'daily_withdrawal',
                'applies_to' => 'role',
                'applies_to_id' => $traineeRoleId,
                'limit_amount' => 10000.00,
                'currency' => 'EGP',
                'period_type' => 'daily',
                'is_active' => true,
                'effective_from' => now()->subDays(30),
                'created_by' => 1,
                'description' => 'Daily withdrawal limit for trainees',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'limit_type' => 'monthly_transfer',
                'applies_to' => 'role',
                'applies_to_id' => $traineeRoleId,
                'limit_amount' => 100000.00,
                'currency' => 'EGP',
                'period_type' => 'monthly',
                'is_active' => true,
                'effective_from' => now()->subDays(30),
                'created_by' => 1,
                'description' => 'Monthly transfer limit for trainees',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Branch Manager limits
            [
                'limit_type' => 'daily_withdrawal',
                'applies_to' => 'role',
                'applies_to_id' => $branchManagerRoleId,
                'limit_amount' => 200000.00,
                'currency' => 'EGP',
                'period_type' => 'daily',
                'is_active' => true,
                'effective_from' => now()->subDays(30),
                'created_by' => 1,
                'description' => 'Daily withdrawal limit for branch managers',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('financial_limits')->insert($financialLimits);
    }

    private function seedNotificationPreferences(): void
    {
        // Get all user IDs
        $userIds = DB::table('users')->pluck('id');

        $notificationTypes = ['email', 'in_app'];
        $eventCategories = ['transactions', 'security', 'system'];
        $eventTypes = [
            'transactions' => ['large_transaction', 'failed_transaction', 'pending_approval'],
            'security' => ['failed_login', 'suspicious_activity', 'password_change'],
            'system' => ['system_maintenance', 'system_alert', 'backup_complete']
        ];

        $preferences = [];
        foreach ($userIds as $userId) {
            foreach ($notificationTypes as $notificationType) {
                foreach ($eventCategories as $eventCategory) {
                    foreach ($eventTypes[$eventCategory] as $eventType) {
                        $preferences[] = [
                            'user_id' => $userId,
                            'notification_type' => $notificationType,
                            'event_category' => $eventCategory,
                            'event_type' => $eventType,
                            'enabled' => true,
                            'conditions' => json_encode([
                                'minimum_amount' => $eventType === 'large_transaction' ? 10000 : null,
                                'severity' => $eventCategory === 'security' ? 'medium' : 'low'
                            ]),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            }
        }

        // Insert in chunks to avoid memory issues
        foreach (array_chunk($preferences, 100) as $chunk) {
            DB::table('notification_preferences')->insert($chunk);
        }
    }

    private function seedSecurityAlerts(): void
    {
        $securityAlerts = [
            [
                'alert_type' => 'large_transaction',
                'severity' => 'medium',
                'status' => 'resolved',
                'title' => 'Large Transaction Detected',
                'description' => 'Transaction amount exceeds normal patterns for this agent',
                'alert_data' => json_encode([
                    'amount' => 150000,
                    'threshold' => 100000,
                    'agent_average' => 25000
                ]),
                'detected_at' => now()->subHours(24),
                'resolved_at' => now()->subHours(20),
                'resolution_notes' => 'Verified as legitimate business transaction',
                'created_at' => now()->subHours(24),
                'updated_at' => now()->subHours(20),
            ],
            [
                'alert_type' => 'failed_login',
                'severity' => 'high',
                'status' => 'investigating',
                'title' => 'Multiple Failed Login Attempts',
                'description' => 'Multiple failed login attempts detected from suspicious IP address',
                'alert_data' => json_encode([
                    'ip_address' => '192.168.1.100',
                    'attempt_count' => 15,
                    'time_window' => '30 minutes'
                ]),
                'detected_at' => now()->subHours(2),
                'resolved_at' => null,
                'resolution_notes' => null,
                'created_at' => now()->subHours(2),
                'updated_at' => now()->subHours(2),
            ],
            [
                'alert_type' => 'suspicious_activity',
                'severity' => 'high',
                'status' => 'open',
                'title' => 'Unusual Transaction Pattern',
                'description' => 'Agent performing transactions outside normal working hours',
                'alert_data' => json_encode([
                    'transaction_time' => '02:30 AM',
                    'normal_hours' => '09:00 AM - 06:00 PM',
                    'transaction_count' => 5
                ]),
                'detected_at' => now()->subHours(6),
                'resolved_at' => null,
                'resolution_notes' => null,
                'created_at' => now()->subHours(6),
                'updated_at' => now()->subHours(6),
            ],
        ];

        DB::table('security_alerts')->insert($securityAlerts);
    }
}
