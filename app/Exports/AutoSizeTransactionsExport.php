<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Collection;

class AutoSizeTransactionsExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $transactions;

    public function __construct(Collection $transactions)
    {
        $this->transactions = $transactions;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->transactions->map(function ($transaction) {
            $commission = $transaction['commission'] ?? $transaction->commission ?? 0;
            $deduction = $transaction['deduction'] ?? $transaction->deduction ?? 0;
            $finalCommission = $commission - $deduction;
            
            // Robust branch name extraction
            $branchName = null;
            // Try array key first
            if (is_array($transaction)) {
                $branchName = $transaction['branch_name'] ?? null;
            }
            // Try object property
            if (!$branchName && is_object($transaction)) {
                $branchName = $transaction->branch_name ?? null;
            }
            // Try branch_id lookup if still missing
            if (!$branchName && (isset($transaction['branch_id']) || isset($transaction->branch_id))) {
                $branchId = $transaction['branch_id'] ?? $transaction->branch_id ?? null;
                if ($branchId) {
                    $branch = \App\Models\Domain\Entities\Branch::find($branchId);
                    if ($branch) {
                        $branchName = $branch->name;
                    }
                }
            }
            // Try safe->branch if still missing
            if (!$branchName && isset($transaction->safe) && isset($transaction->safe->branch)) {
                $branchName = $transaction->safe->branch->name;
            }
            if (!$branchName) {
                $branchName = 'N/A';
            }
            return [
                'Reference Number' => $transaction['reference_number'] ?? $transaction->reference_number ?? '',
                'Customer Name' => $transaction['customer_name'] ?? $transaction->customer_name ?? '',
                'Customer Code' => $transaction['customer_code'] ?? $transaction->customer_code ?? '',
                'Amount (EGP)' => $transaction['amount'] ?? $transaction->amount ?? '',
                'Final Commission (EGP)' => $finalCommission,
                'Deduction (EGP)' => $deduction,
                'Transaction Type' => $transaction['transaction_type'] ?? $transaction->transaction_type ?? '',
                'Status' => $transaction['status'] ?? $transaction->status ?? '',
                'Agent Name' => $transaction['agent_name'] ?? $transaction->agent_name ?? '',
                'Transaction Date/Time' => $transaction['transaction_date_time'] ?? $transaction->transaction_date_time ?? '',
                'Branch' => $branchName,
                'Source' => $transaction['source'] ?? $transaction->source ?? '',
            ];
        });
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Reference Number',
            'Customer Name',
            'Customer Code',
            'Amount (EGP)',
            'Final Commission (EGP)',
            'Deduction (EGP)',
            'Transaction Type',
            'Status',
            'Agent Name',
            'Transaction Date/Time',
            'Branch',
            'Source',
        ];
    }
}
