<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Interfaces\TransactionRepository;
use App\Models\Domain\Entities\Transaction as EloquentTransaction;
use App\Models\Domain\Entities\Transaction;
use App\Models\Domain\Entities\CashTransaction;

class EloquentTransactionRepository implements TransactionRepository
{
    public function create(array $attributes): Transaction
    {
        // Filter attributes to only those fillable in the model
        $fillable = (new EloquentTransaction())->getFillable();
        $filtered = array_intersect_key($attributes, array_flip($fillable));
        return EloquentTransaction::create($filtered);
    }

    public function findById(string $id): ?Transaction
    {
        return EloquentTransaction::find($id);
    }

    public function update(string $id, array $attributes): Transaction
    {
        $transaction = EloquentTransaction::findOrFail($id);
        $transaction->update($attributes);
        return $transaction;
    }

    public function delete(string $id): void
    {
        EloquentTransaction::destroy($id);
    }

    public function all(): array
    {
        return EloquentTransaction::with('agent')->get()->map(function ($transaction) {
            $transactionArray = $transaction->toArray();
            $transactionArray['agent_name'] = $transaction->agent ? $transaction->agent->name : 'N/A';
            return $transactionArray;
        })->toArray();
    }

    public function findByStatus(string $status, ?int $branchId = null): array
    {
        $query = EloquentTransaction::where('status', $status)->with('agent');

        if ($branchId) {
            $query->whereHas('agent', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }

        return $query->get()->map(function ($transaction) {
            $transactionArray = $transaction->toArray();
            $transactionArray['agent_name'] = $transaction->agent ? $transaction->agent->name : 'N/A';
            return $transactionArray;
        })->toArray();
    }

    public function filter(array $filters = []): array
    {
        // Ordinary transactions
        $query = EloquentTransaction::query();
        // Flag to skip cash transactions if filtering by transfer line
        $skipCashTransactions = false;
        
        // Date filters re-enabled
        if (isset($filters['start_date']) && $filters['start_date']) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }
        if (isset($filters['end_date']) && $filters['end_date']) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }
        if (isset($filters['agent_id'])) {
            $query->where('agent_id', $filters['agent_id']);
        }
        if (isset($filters['branch_id'])) {
            $query->where(function($q) use ($filters) {
                // Include transactions where the agent belongs to the branch
                $q->whereHas('agent', function ($agentQuery) use ($filters) {
                    $agentQuery->where('branch_id', $filters['branch_id']);
                });
                // OR include transactions that have the branch_id set directly
                $q->orWhere('branch_id', $filters['branch_id']);
            });
        }
        if (isset($filters['customer_name'])) {
            $query->where('customer_name', 'like', '%' . $filters['customer_name'] . '%');
        }
        if (isset($filters['transaction_type'])) {
            $query->where('transaction_type', $filters['transaction_type']);
        }
        // Regular transactions - customer code search
        if (isset($filters['customer_code']) && $filters['customer_code']) {
            // Standardize the customer code format - remove spaces and make case-insensitive
            $customerCode = trim($filters['customer_code']);
            $query->where(function($q) use ($customerCode) {
                $q->whereRaw('LOWER(customer_code) LIKE ?', ['%' . strtolower($customerCode) . '%']);
            });
        }
        if (isset($filters['receiver_mobile_number']) && $filters['receiver_mobile_number']) {
            // Clean the mobile number to ensure it only contains digits
            $mobileNumber = preg_replace('/[^0-9]/', '', $filters['receiver_mobile_number']);
            $query->where(function($q) use ($mobileNumber) {
                $q->whereRaw("REPLACE(REPLACE(REPLACE(receiver_mobile_number, '-', ''), ' ', ''), '+', '') LIKE ?", ['%' . $mobileNumber . '%']);
            });
        }
        
        // When filtering by transfer_line, we should exclude cash transactions since they don't have line_id
        if (isset($filters['transfer_line']) && $filters['transfer_line'] && !empty($filters['transfer_line'])) {
            // Set flag to skip cash transactions
            $skipCashTransactions = true;
            
            // For regular transactions, search by joining with lines table
            $transferLine = trim($filters['transfer_line']);
            $query->whereHas('line', function($q) use ($transferLine) {
                $q->where('mobile_number', 'like', '%' . $transferLine . '%');
            });
        }
        
        // Handle amount range filtering
        if (isset($filters['amount_from']) && $filters['amount_from'] !== '') {
            $query->where('amount', '>=', $filters['amount_from']);
        }
        if (isset($filters['amount_to']) && $filters['amount_to'] !== '') {
            $query->where('amount', '<=', $filters['amount_to']);
        }
        if (isset($filters['commission']) && $filters['commission']) {
            $query->where('commission', $filters['commission']);
        }
        if (isset($filters['employee_ids']) && is_array($filters['employee_ids']) && count($filters['employee_ids']) > 0) {
            $query->whereIn('agent_id', $filters['employee_ids']);
        }
        if (isset($filters['branch_ids']) && is_array($filters['branch_ids']) && count($filters['branch_ids']) > 0) {
            $query->where(function($q) use ($filters) {
                // Include transactions where the agent belongs to one of the branches
                $q->whereHas('agent', function ($agentQuery) use ($filters) {
                    $agentQuery->whereIn('branch_id', $filters['branch_ids']);
                });
                // OR include transactions that have the branch_id set directly to one of the branches
                $q->orWhereIn('branch_id', $filters['branch_ids']);
            });
        }
        // Add reference_number filter for regular transactions
        if (isset($filters['reference_number']) && $filters['reference_number']) {
            // Standardize the reference number format - remove spaces and make case-insensitive
            $refNumber = trim($filters['reference_number']);
            $query->where(function($q) use ($refNumber) {
                $q->whereRaw('LOWER(reference_number) LIKE ?', ['%' . strtolower($refNumber) . '%']);
            });
        }
        $ordinaryTxs = $query->with(['agent', 'agent.branch'])->get()->map(function ($transaction) {
            $transactionArray = $transaction->toArray();
            $transactionArray['agent_name'] = $transaction->agent ? $transaction->agent->name : 'N/A';
            $transactionArray['branch_name'] = $transaction->agent && $transaction->agent->branch ? $transaction->agent->branch->name : 'N/A';
            $transactionArray['commission'] = $transaction->commission ?? 0;
            $transactionArray['deduction'] = $transaction->deduction ?? 0;
            $transactionArray['source_table'] = 'transactions';
            return $transactionArray;
        });

        // Cash transactions - skip if filtering by transfer line
        $cashTxs = collect();
        if (!$skipCashTransactions) {
            $cash = CashTransaction::query();
            // Date filters re-enabled
            if (isset($filters['start_date']) && $filters['start_date']) {
                $cash->whereDate('created_at', '>=', $filters['start_date']);
            }
            if (isset($filters['end_date']) && $filters['end_date']) {
                $cash->whereDate('created_at', '<=', $filters['end_date']);
            }
            if (isset($filters['customer_name'])) {
                $cash->where('customer_name', 'like', '%' . $filters['customer_name'] . '%');
            }
            if (isset($filters['transaction_type'])) {
                $cash->where('transaction_type', $filters['transaction_type']);
            }
            // Cash transactions - customer code search
            if (isset($filters['customer_code']) && $filters['customer_code']) {
                // Standardize the customer code format - remove spaces and make case-insensitive
                $customerCode = trim($filters['customer_code']);
                $cash->where(function($q) use ($customerCode) {
                    $q->whereRaw('LOWER(customer_code) LIKE ?', ['%' . strtolower($customerCode) . '%']);
                });
            }
            // Handle amount range filtering for cash transactions
            if (isset($filters['amount_from']) && $filters['amount_from'] !== '') {
                $cash->where('amount', '>=', $filters['amount_from']);
            }
            if (isset($filters['amount_to']) && $filters['amount_to'] !== '') {
                $cash->where('amount', '<=', $filters['amount_to']);
            }
            if (isset($filters['agent_id'])) {
                $cash->where('agent_id', $filters['agent_id']);
            }
            if (isset($filters['employee_ids']) && is_array($filters['employee_ids']) && count($filters['employee_ids']) > 0) {
                $cash->whereIn('agent_id', $filters['employee_ids']);
            }
            if (isset($filters['branch_id'])) {
                $cash->whereHas('safe', function ($q) use ($filters) {
                    $q->where('branch_id', $filters['branch_id']);
                });
            }
            // Add filter for receiver_mobile_number (using depositor_mobile_number)
            if (isset($filters['receiver_mobile_number']) && $filters['receiver_mobile_number']) {
                // Clean the mobile number to ensure it only contains digits
                $mobileNumber = preg_replace('/[^0-9]/', '', $filters['receiver_mobile_number']);
                $cash->where(function($q) use ($mobileNumber) {
                    $q->whereRaw("REPLACE(REPLACE(REPLACE(depositor_mobile_number, '-', ''), ' ', ''), '+', '') LIKE ?", ['%' . $mobileNumber . '%']);
                });
            }
            // Add reference_number filter for cash transactions
            if (isset($filters['reference_number']) && $filters['reference_number']) {
                // Standardize the reference number format - remove spaces and make case-insensitive
                $refNumber = trim($filters['reference_number']);
                $cash->where(function($q) use ($refNumber) {
                    $q->whereRaw('LOWER(reference_number) LIKE ?', ['%' . strtolower($refNumber) . '%']);
                });
            }
            $cashTxs = $cash->with(['agent', 'agent.branch'])->get()->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'customer_name' => $transaction->customer_name,
                    'customer_mobile_number' => null,
                    'receiver_mobile_number' => $transaction->depositor_mobile_number ?? null,
                    'customer_code' => $transaction->customer_code ?? null,
                    'amount' => $transaction->amount,
                    'commission' => 0,
                    'deduction' => 0,
                    'discount_notes' => null,
                    'notes' => $transaction->notes,
                    'transaction_type' => $transaction->transaction_type,
                    'agent_id' => $transaction->agent_id,
                    'agent_name' => $transaction->agent ? $transaction->agent->name : 'N/A',
                    'branch_name' => $transaction->agent && $transaction->agent->branch ? $transaction->agent->branch->name : 'N/A',
                    'status' => $transaction->status,
                    'transaction_date_time' => $transaction->transaction_date_time,
                    'line_id' => null,
                    'safe_id' => $transaction->safe_id,
                    'is_absolute_withdrawal' => null,
                    'payment_method' => null,
                    'reference_number' => $transaction->reference_number, // Use actual reference_number
                    'created_at' => $transaction->created_at,
                    'updated_at' => $transaction->updated_at,
                    'source_table' => 'cash_transactions',
                ];
            });
        }

        // Merge and sort
        $all = collect(array_merge($ordinaryTxs->toArray(), $cashTxs->toArray()));
        $sortField = $filters['sortField'] ?? 'transaction_date_time';
        $sortDirection = $filters['sortDirection'] ?? 'desc';
        $all = $all->sortBy(function($tx) use ($sortField) {
            return $tx[$sortField] ?? $tx['transaction_date_time'] ?? $tx['created_at'] ?? null;
        }, SORT_REGULAR, $sortDirection === 'desc');

        $totalTransferred = $all->sum('amount');
        $totalCommission = $all->sum('commission');
        $totalDeductions = $all->sum('deduction');
        $netProfit = $totalCommission - $totalDeductions;

        return [
            'transactions' => $all->values()->all(),
            'totals' => [
                'total_transferred' => $totalTransferred,
                'total_commission' => $totalCommission,
                'total_deductions' => $totalDeductions,
                'net_profit' => $netProfit,
            ],
        ];
    }

    public function save(Transaction $transaction): Transaction
    {
        $transaction->save();
        return $transaction;
    }

    public function getTransactionsByLineAndDateRange(string $lineId, \Carbon\Carbon $startDate, \Carbon\Carbon $endDate)
    {
        return EloquentTransaction::where('line_id', $lineId)
            ->whereBetween('transaction_date_time', [$startDate, $endDate])
            ->get();
    }

    public function getTotalReceivedForLine(string $lineId, \Carbon\Carbon $startDate, \Carbon\Carbon $endDate): float
    {
        return EloquentTransaction::where('line_id', $lineId)
            ->whereIn('transaction_type', ['Deposit', 'Receive'])
            ->whereBetween('transaction_date_time', [$startDate, $endDate])
            ->sum('amount');
    }

    public function getTotalSentForLine(string $lineId, \Carbon\Carbon $startDate, \Carbon\Carbon $endDate): float
    {
        return EloquentTransaction::where('line_id', $lineId)
            ->whereIn('transaction_type', ['Transfer', 'Withdrawal'])
            ->whereBetween('transaction_date_time', [$startDate, $endDate])
            ->sum('amount');
    }

    /**
     * Fetch all transactions (ordinary + cash) as a unified list for UI and logic.
     */
    public function allUnified(array $filters = []): array
    {
        // Flag to skip cash transactions if filtering by transfer line
        $skipCashTransactions = false;
        
        // Fetch ordinary transactions
        $ordinary = EloquentTransaction::query();
        // Apply filters as in filter() method (copy relevant filter logic)
        if (isset($filters['start_date']) && $filters['start_date']) {
            $ordinary->whereDate('created_at', '>=', $filters['start_date']);
        }
        if (isset($filters['end_date']) && $filters['end_date']) {
            $ordinary->whereDate('created_at', '<=', $filters['end_date']);
        }
        if (isset($filters['agent_id'])) {
            $ordinary->where('agent_id', $filters['agent_id']);
        }
        if (isset($filters['branch_id'])) {
            $ordinary->where(function($q) use ($filters) {
                // Include transactions where the agent belongs to the branch
                $q->whereHas('agent', function ($agentQuery) use ($filters) {
                    $agentQuery->where('branch_id', $filters['branch_id']);
                });
                // OR include transactions that have the branch_id set directly
                $q->orWhere('branch_id', $filters['branch_id']);
            });
        }
        if (isset($filters['customer_name'])) {
            $ordinary->where('customer_name', 'like', '%' . $filters['customer_name'] . '%');
        }
        if (isset($filters['transaction_type'])) {
            $ordinary->where('transaction_type', $filters['transaction_type']);
        }
        // allUnified method - regular transactions customer code search
        if (isset($filters['customer_code']) && $filters['customer_code']) {
            // Standardize the customer code format - remove spaces and make case-insensitive
            $customerCode = trim($filters['customer_code']);
            $ordinary->where(function($q) use ($customerCode) {
                $q->whereRaw('LOWER(customer_code) LIKE ?', ['%' . strtolower($customerCode) . '%']);
            });
        }
        if (isset($filters['receiver_mobile_number']) && $filters['receiver_mobile_number']) {
            // Clean the mobile number to ensure it only contains digits
            $mobileNumber = preg_replace('/[^0-9]/', '', $filters['receiver_mobile_number']);
            $ordinary->where(function($q) use ($mobileNumber) {
                $q->whereRaw("REPLACE(REPLACE(REPLACE(receiver_mobile_number, '-', ''), ' ', ''), '+', '') LIKE ?", ['%' . $mobileNumber . '%']);
            });
        }
        
        // When filtering by transfer_line, exclude cash transactions since they don't have line_id
        if (isset($filters['transfer_line']) && $filters['transfer_line'] && !empty($filters['transfer_line'])) {
            // Set flag to skip cash transactions
            $skipCashTransactions = true;
            
            // For regular transactions, search by joining with lines table
            $transferLine = trim($filters['transfer_line']);
            $ordinary->whereHas('line', function($q) use ($transferLine) {
                $q->where('mobile_number', 'like', '%' . $transferLine . '%');
            });
        }
        
        // Handle amount range filtering
        if (isset($filters['amount_from']) && $filters['amount_from'] !== '') {
            $ordinary->where('amount', '>=', $filters['amount_from']);
        }
        if (isset($filters['amount_to']) && $filters['amount_to'] !== '') {
            $ordinary->where('amount', '<=', $filters['amount_to']);
        }
        if (isset($filters['commission']) && $filters['commission']) {
            $ordinary->where('commission', $filters['commission']);
        }
        if (isset($filters['employee_ids']) && is_array($filters['employee_ids']) && count($filters['employee_ids']) > 0) {
            $ordinary->whereIn('agent_id', $filters['employee_ids']);
        }
        if (isset($filters['branch_ids']) && is_array($filters['branch_ids']) && count($filters['branch_ids']) > 0) {
            $ordinary->where(function($q) use ($filters) {
                // Include transactions where the agent belongs to one of the branches
                $q->whereHas('agent', function ($agentQuery) use ($filters) {
                    $agentQuery->whereIn('branch_id', $filters['branch_ids']);
                });
                // OR include transactions that have the branch_id set directly to one of the branches
                $q->orWhereIn('branch_id', $filters['branch_ids']);
            });
        }
        if (isset($filters['reference_number']) && $filters['reference_number']) {
            // Standardize the reference number format - remove spaces and make case-insensitive
            $refNumber = trim($filters['reference_number']);
            $ordinary->where(function($q) use ($refNumber) {
                $q->whereRaw('LOWER(reference_number) LIKE ?', ['%' . strtolower($refNumber) . '%']);
            });
        }
        $ordinaryTxs = $ordinary->with(['agent', 'agent.branch'])->get()->map(function ($transaction) {
            $arr = $transaction->toArray();
            $arr['agent_name'] = $transaction->agent ? $transaction->agent->name : 'N/A';
            $arr['branch_name'] = $transaction->agent && $transaction->agent->branch ? $transaction->agent->branch->name : 'N/A';
            $arr['commission'] = $transaction->commission ?? 0;
            $arr['deduction'] = $transaction->deduction ?? 0;
            $arr['source_table'] = 'transactions';
            return $arr;
        });

        // Fetch cash transactions - skip if filtering by transfer line
        $cashTxs = collect();
        if (!$skipCashTransactions) {
            $cash = CashTransaction::query();
            if (isset($filters['start_date']) && $filters['start_date']) {
                $cash->whereDate('created_at', '>=', $filters['start_date']);
            }
            if (isset($filters['end_date']) && $filters['end_date']) {
                $cash->whereDate('created_at', '<=', $filters['end_date']);
            }
            if (isset($filters['customer_name'])) {
                $cash->where('customer_name', 'like', '%' . $filters['customer_name'] . '%');
            }
            if (isset($filters['transaction_type'])) {
                $cash->where('transaction_type', $filters['transaction_type']);
            }
            // allUnified method - cash transactions customer code search
            if (isset($filters['customer_code']) && $filters['customer_code']) {
                // Standardize the customer code format - remove spaces and make case-insensitive
                $customerCode = trim($filters['customer_code']);
                $cash->where(function($q) use ($customerCode) {
                    $q->whereRaw('LOWER(customer_code) LIKE ?', ['%' . strtolower($customerCode) . '%']);
                });
            }
            // Handle amount range filtering for cash transactions
            if (isset($filters['amount_from']) && $filters['amount_from'] !== '') {
                $cash->where('amount', '>=', $filters['amount_from']);
            }
            if (isset($filters['amount_to']) && $filters['amount_to'] !== '') {
                $cash->where('amount', '<=', $filters['amount_to']);
            }
            // Add agent_id and employee_ids filter for cash transactions
            if (isset($filters['agent_id'])) {
                $cash->where('agent_id', $filters['agent_id']);
            }
            if (isset($filters['employee_ids']) && is_array($filters['employee_ids']) && count($filters['employee_ids']) > 0) {
                $cash->whereIn('agent_id', $filters['employee_ids']);
            }
            if (isset($filters['reference_number']) && $filters['reference_number']) {
                // Standardize the reference number format - remove spaces and make case-insensitive
                $refNumber = trim($filters['reference_number']);
                $cash->where(function($q) use ($refNumber) {
                    $q->whereRaw('LOWER(reference_number) LIKE ?', ['%' . strtolower($refNumber) . '%']);
                });
            }
            // Add filter for receiver_mobile_number (using depositor_mobile_number)
            if (isset($filters['receiver_mobile_number']) && $filters['receiver_mobile_number']) {
                // Clean the mobile number to ensure it only contains digits
                $mobileNumber = preg_replace('/[^0-9]/', '', $filters['receiver_mobile_number']);
                $cash->where(function($q) use ($mobileNumber) {
                    $q->whereRaw("REPLACE(REPLACE(REPLACE(depositor_mobile_number, '-', ''), ' ', ''), '+', '') LIKE ?", ['%' . $mobileNumber . '%']);
                });
            }
            $cashTxs = $cash->with(['agent', 'agent.branch'])->get()->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'customer_name' => $transaction->customer_name,
                    'customer_mobile_number' => null,
                    'receiver_mobile_number' => $transaction->depositor_mobile_number ?? null,
                    'customer_code' => $transaction->customer_code ?? null,
                    'amount' => $transaction->amount,
                    'commission' => 0,
                    'deduction' => 0,
                    'discount_notes' => null,
                    'notes' => $transaction->notes,
                    'transaction_type' => $transaction->transaction_type,
                    'agent_id' => $transaction->agent_id,
                    'agent_name' => $transaction->agent ? $transaction->agent->name : 'N/A',
                    'branch_name' => $transaction->agent && $transaction->agent->branch ? $transaction->agent->branch->name : 'N/A',
                    'status' => $transaction->status,
                    'transaction_date_time' => $transaction->transaction_date_time,
                    'line_id' => null,
                    'safe_id' => $transaction->safe_id,
                    'is_absolute_withdrawal' => null,
                    'payment_method' => null,
                    'reference_number' => $transaction->reference_number, // Use actual reference_number
                    'created_at' => $transaction->created_at,
                    'updated_at' => $transaction->updated_at,
                    'source_table' => 'cash_transactions',
                ];
            });
        }

        $ordinaryTxsArr = $ordinaryTxs->toArray();
        $cashTxsArr = $cashTxs->toArray();

        $all = collect(array_merge($ordinaryTxsArr, $cashTxsArr));

        // Apply sorting if provided
        if (isset($filters['sortField']) && isset($filters['sortDirection'])) {
            $sortField = $filters['sortField'];
            $sortDirection = $filters['sortDirection'];
            
            // Map frontend field names to actual database field names
            $fieldMapping = [
                'customer_name' => 'customer_name',
                'customer_mobile_number' => 'customer_mobile_number',
                'amount' => 'amount',
                'commission' => 'commission',
                'transaction_type' => 'transaction_type',
                'agent_name' => 'agent_name',
                'created_at' => 'created_at',
                'reference_number' => 'reference_number',
            ];
            
            $actualField = $fieldMapping[$sortField] ?? 'created_at';
            
            if ($sortDirection === 'asc') {
                $all = $all->sortBy($actualField);
            } else {
                $all = $all->sortByDesc($actualField);
            }
        } else {
            // Default sorting by transaction date time descending
            $all = $all->sortByDesc('transaction_date_time');
        }

        $all = $all->values();

        $totalTransferred = $all->sum('amount');
        $totalCommission = $all->sum('commission');
        $totalDeductions = $all->sum('deduction');
        $netProfit = $totalCommission - $totalDeductions;

        return [
            'transactions' => $all->toArray(),
            'totals' => [
                'total_transferred' => $totalTransferred,
                'total_commission' => $totalCommission,
                'total_deductions' => $totalDeductions,
                'net_profit' => $netProfit,
            ],
        ];
    }

    public function countAllPending(): int
    {
        $ordinaryCount = EloquentTransaction::where('status', 'Pending')->count();
        $cashCount = CashTransaction::where('status', 'pending')->count();
        return $ordinaryCount + $cashCount;
    }
}
