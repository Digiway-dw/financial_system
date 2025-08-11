<?php

namespace App\Services;

use App\Models\Domain\Entities\Transaction;
use App\Models\Domain\Entities\CashTransaction;
use Illuminate\Support\Collection;

/**
 * Service for calculating financial totals across different transaction types
 * Provides consistent totals calculation for all reports
 */
class TotalsService
{
    /**
     * Calculate totals for a filtered dataset
     *
     * @param array $filters
     * @return array
     */
    public function calculateTotals(array $filters = []): array
    {
        $repository = new \App\Infrastructure\Repositories\EloquentTransactionRepository();
        $result = $repository->allUnified($filters);
        
        return [
            'total_turnover' => $result['totals']['total_transferred'] ?? 0,
            'total_commissions' => $result['totals']['total_commission'] ?? 0,
            'total_deductions' => $result['totals']['total_deductions'] ?? 0,
            'net_profit' => $result['totals']['net_profit'] ?? 0,
            'transactions_count' => count($result['transactions'] ?? []),
            'total_expenses' => $this->calculateBranchExpenses($filters),
        ];
    }

    /**
     * Calculate branch expenses for the given filters
     *
     * @param array $filters
     * @return float
     */
    public function calculateBranchExpenses(array $filters = []): float
    {
        $query = CashTransaction::where('transaction_type', 'Withdrawal')
            ->where('customer_name', 'like', 'Expense:%');

        // Apply date filters
        if (isset($filters['start_date'])) {
            $query->whereDate('transaction_date_time', '>=', $filters['start_date']);
        }
        if (isset($filters['end_date'])) {
            $query->whereDate('transaction_date_time', '<=', $filters['end_date']);
        }

        // Apply branch filters
        if (isset($filters['branch_ids']) && is_array($filters['branch_ids']) && count($filters['branch_ids']) > 0) {
            $query->whereIn('destination_branch_id', $filters['branch_ids']);
        } elseif (isset($filters['branch_id'])) {
            $query->where('destination_branch_id', $filters['branch_id']);
        }

        return $query->sum('amount');
    }

    /**
     * Calculate net profit with branch expenses subtracted (for Branch Report)
     *
     * @param array $filters
     * @return float
     */
    public function calculateNetProfitWithExpenses(array $filters = []): float
    {
        $totals = $this->calculateTotals($filters);
        return $totals['net_profit'] - $totals['total_expenses'];
    }

    /**
     * Get customer balances for the report
     *
     * @return array
     */
    public function getCustomerBalances(): array
    {
        return \App\Models\Domain\Entities\Customer::where('is_client', true)
            ->where('balance', '>', 0)
            ->get()
            ->map(function ($customer) {
                return [
                    'customer' => $customer->name,
                    'balance' => $customer->balance,
                    'customer_code' => $customer->customer_code,
                ];
            })
            ->all();
    }

    /**
     * Get safe balances by branch
     *
     * @param array $branchIds Optional array of branch IDs to filter
     * @return array
     */
    public function getSafeBalances(array $branchIds = []): array
    {
        $query = \App\Models\Domain\Entities\Safe::with('branch');
        
        if (!empty($branchIds)) {
            $query->whereIn('branch_id', $branchIds);
        }

        return $query->get()
            ->groupBy('branch_id')
            ->map(function ($safes) {
                return [
                    'branch' => $safes->first()->branch->name ?? 'N/A',
                    'balance' => $safes->sum('current_balance'),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Get line balances by branch
     *
     * @param array $branchIds Optional array of branch IDs to filter
     * @return array
     */
    public function getLineBalances(array $branchIds = []): array
    {
        $query = \App\Models\Domain\Entities\Line::with('branch');
        
        if (!empty($branchIds)) {
            $query->whereIn('branch_id', $branchIds);
        }

        return $query->get()
            ->groupBy('branch_id')
            ->map(function ($lines) {
                return [
                    'branch' => $lines->first()->branch->name ?? 'N/A',
                    'balance' => $lines->sum('current_balance'),
                ];
            })
            ->values()
            ->all();
    }
}
