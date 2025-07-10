<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Interfaces\TransactionRepository;
use App\Models\Domain\Entities\Transaction as EloquentTransaction;
use App\Models\Domain\Entities\Transaction;

class EloquentTransactionRepository implements TransactionRepository
{
    public function create(array $attributes): Transaction
    {
        return EloquentTransaction::create($attributes);
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
}
