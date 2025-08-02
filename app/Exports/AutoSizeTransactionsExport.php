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
            $commission = $transaction['commission'] ?? 0;
            $deduction = $transaction['deduction'] ?? 0;
            $finalCommission = $commission - $deduction;
            return [
                'Reference Number' => $transaction['reference_number'] ?? '',
                'Customer Name' => $transaction['customer_name'] ?? '',
                'Customer Code' => $transaction['customer_code'] ?? '',
                'Amount (EGP)' => $transaction['amount'] ?? '',
                'Final Commission (EGP)' => $finalCommission,
                'Deduction (EGP)' => $deduction,
                'Transaction Type' => $transaction['transaction_type'] ?? '',
                'Status' => $transaction['status'] ?? '',
                'Agent Name' => $transaction['agent_name'] ?? '',
                'Transaction Date/Time' => $transaction['transaction_date_time'] ?? '',
                'Source' => $transaction['source'] ?? '',
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
            'Source',
        ];
    }
}
