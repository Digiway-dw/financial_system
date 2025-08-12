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
        $transactionRows = $this->transactions->map(function ($transaction) {
            $commission = $transaction['commission'] ?? $transaction->commission ?? 0;
            $deduction = $transaction['deduction'] ?? $transaction->deduction ?? 0;
            // Robust branch name extraction
            $branchName = null;
            if (is_array($transaction)) {
                $branchName = $transaction['branch_name'] ?? null;
            }
            if (!$branchName && is_object($transaction)) {
                $branchName = $transaction->branch_name ?? null;
            }
            if (!$branchName && (isset($transaction['branch_id']) || isset($transaction->branch_id))) {
                $branchId = $transaction['branch_id'] ?? $transaction->branch_id ?? null;
                if ($branchId) {
                    $branch = \App\Models\Domain\Entities\Branch::find($branchId);
                    if ($branch) {
                        $branchName = $branch->name;
                    }
                }
            }
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
                'Amount (EGP)' => $transaction['amount'] ?? $transaction->amount ?? 0,
                'Commission (EGP)' => $commission,
                'Deduction (EGP)' => $deduction,
                'Transaction Type' => $transaction['transaction_type'] ?? $transaction->transaction_type ?? '',
                'Status' => $transaction['status'] ?? $transaction->status ?? '',
                'Agent Name' => $transaction['agent_name'] ?? $transaction->agent_name ?? '',
                'Transaction Date/Time' => $transaction['transaction_date_time'] ?? $transaction->transaction_date_time ?? '',
                'Branch' => $branchName,
                'Source' => $transaction['source'] ?? $transaction->source ?? '',
            ];
        });
        // Calculate totals
        $totalAmount = $transactionRows->sum('Amount (EGP)');
        $totalCommission = $transactionRows->sum('Commission (EGP)');
        $totalDeduction = $transactionRows->sum('Deduction (EGP)');
        // Add totals row
        $totalsRow = [
            'Reference Number' => 'الإجمالي (Total)',
            'Customer Name' => '',
            'Customer Code' => '',
            'Amount (EGP)' => $totalAmount,
            'Commission (EGP)' => $totalCommission,
            'Deduction (EGP)' => $totalDeduction,
            'Transaction Type' => '',
            'Status' => '',
            'Agent Name' => '',
            'Transaction Date/Time' => '',
            'Branch' => '',
            'Source' => '',
        ];
        $transactionRows->push($totalsRow);
        return $transactionRows;
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
            'Commission (EGP)',
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
