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
        $query = EloquentTransaction::query();

        if (isset($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        if (isset($filters['agent_id'])) {
            $query->where('agent_id', $filters['agent_id']);
        }

        if (isset($filters['branch_id'])) {
            $query->whereHas('agent', function ($q) use ($filters) {
                $q->where('branch_id', $filters['branch_id']);
            });
        }

        if (isset($filters['customer_name'])) {
            $query->where('customer_name', 'like', '%' . $filters['customer_name'] . '%');
        }

        if (isset($filters['transaction_type'])) {
            $query->where('transaction_type', $filters['transaction_type']);
        }

        if (isset($filters['customer_code']) && $filters['customer_code']) {
            $query->where('customer_code', 'like', '%' . $filters['customer_code'] . '%');
        }
        if (isset($filters['receiver_mobile']) && $filters['receiver_mobile']) {
            $query->where('receiver_mobile', 'like', '%' . $filters['receiver_mobile'] . '%');
        }
        if (isset($filters['transfer_line']) && $filters['transfer_line']) {
            $query->where('line_number', 'like', '%' . $filters['transfer_line'] . '%');
        }
        if (isset($filters['amount']) && $filters['amount']) {
            $query->where('amount', $filters['amount']);
        }
        if (isset($filters['commission']) && $filters['commission']) {
            $query->where('commission', $filters['commission']);
        }
        if (isset($filters['employee_ids']) && is_array($filters['employee_ids']) && count($filters['employee_ids']) > 0) {
            $query->whereIn('agent_id', $filters['employee_ids']);
        }
        if (isset($filters['branch_ids']) && is_array($filters['branch_ids']) && count($filters['branch_ids']) > 0) {
            $query->whereHas('agent', function ($q) use ($filters) {
                $q->whereIn('branch_id', $filters['branch_ids']);
            });
        }

        $transactions = $query->with(['agent', 'agent.branch'])->get()->map(function ($transaction) {
            $transactionArray = $transaction->toArray();
            $transactionArray['agent_name'] = $transaction->agent ? $transaction->agent->name : 'N/A';
            $transactionArray['branch_name'] = $transaction->agent && $transaction->agent->branch ? $transaction->agent->branch->name : 'N/A';
            return $transactionArray;
        });

        $totalTransferred = $transactions->sum('amount');
        $totalCommission = $transactions->sum('commission');
        $totalDeductions = $transactions->sum('deduction');
        $netProfit = $totalCommission - $totalDeductions;

        return [
            'transactions' => $transactions->toArray(),
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
        // Fetch ordinary transactions
        $ordinary = EloquentTransaction::query();
        // Apply filters as in filter() method (copy relevant filter logic)
        if (isset($filters['start_date'])) {
            $ordinary->whereDate('created_at', '>=', $filters['start_date']);
        }
        if (isset($filters['end_date'])) {
            $ordinary->whereDate('created_at', '<=', $filters['end_date']);
        }
        if (isset($filters['agent_id'])) {
            $ordinary->where('agent_id', $filters['agent_id']);
        }
        if (isset($filters['branch_id'])) {
            $ordinary->whereHas('agent', function ($q) use ($filters) {
                $q->where('branch_id', $filters['branch_id']);
            });
        }
        if (isset($filters['customer_name'])) {
            $ordinary->where('customer_name', 'like', '%' . $filters['customer_name'] . '%');
        }
        if (isset($filters['transaction_type'])) {
            $ordinary->where('transaction_type', $filters['transaction_type']);
        }
        if (isset($filters['customer_code']) && $filters['customer_code']) {
            $ordinary->where('customer_code', 'like', '%' . $filters['customer_code'] . '%');
        }
        if (isset($filters['receiver_mobile']) && $filters['receiver_mobile']) {
            $ordinary->where('receiver_mobile', 'like', '%' . $filters['receiver_mobile'] . '%');
        }
        if (isset($filters['transfer_line']) && $filters['transfer_line']) {
            $ordinary->where('line_number', 'like', '%' . $filters['transfer_line'] . '%');
        }
        if (isset($filters['amount']) && $filters['amount']) {
            $ordinary->where('amount', $filters['amount']);
        }
        if (isset($filters['commission']) && $filters['commission']) {
            $ordinary->where('commission', $filters['commission']);
        }
        if (isset($filters['employee_ids']) && is_array($filters['employee_ids']) && count($filters['employee_ids']) > 0) {
            $ordinary->whereIn('agent_id', $filters['employee_ids']);
        }
        if (isset($filters['branch_ids']) && is_array($filters['branch_ids']) && count($filters['branch_ids']) > 0) {
            $ordinary->whereHas('agent', function ($q) use ($filters) {
                $q->whereIn('branch_id', $filters['branch_ids']);
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

        // Fetch cash transactions
        $cash = CashTransaction::query();
        if (isset($filters['start_date'])) {
            $cash->whereDate('created_at', '>=', $filters['start_date']);
        }
        if (isset($filters['end_date'])) {
            $cash->whereDate('created_at', '<=', $filters['end_date']);
        }
        if (isset($filters['customer_name'])) {
            $cash->where('customer_name', 'like', '%' . $filters['customer_name'] . '%');
        }
        if (isset($filters['transaction_type'])) {
            $cash->where('transaction_type', $filters['transaction_type']);
        }
        if (isset($filters['amount']) && $filters['amount']) {
            $cash->where('amount', $filters['amount']);
        }
        // Add agent_id and employee_ids filter for cash transactions
        if (isset($filters['agent_id'])) {
            $cash->where('agent_id', $filters['agent_id']);
        }
        if (isset($filters['employee_ids']) && is_array($filters['employee_ids']) && count($filters['employee_ids']) > 0) {
            $cash->whereIn('agent_id', $filters['employee_ids']);
        }
        $cashTxs = $cash->with(['agent', 'agent.branch'])->get()->map(function ($transaction) {
            return [
                'id' => $transaction->id,
                'customer_name' => $transaction->customer_name,
                'customer_mobile_number' => null,
                'receiver_mobile_number' => null,
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
                'reference_number' => null,
                'created_at' => $transaction->created_at,
                'updated_at' => $transaction->updated_at,
                'source_table' => 'cash_transactions',
            ];
        });

        $ordinaryTxsArr = $ordinaryTxs->toArray();
        $cashTxsArr = $cashTxs->toArray();

        $all = collect(array_merge($ordinaryTxsArr, $cashTxsArr))
            ->sortByDesc('transaction_date_time')
            ->values();

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
}
