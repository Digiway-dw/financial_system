<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class TransactionsExport implements FromCollection, WithHeadings
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
            return [
                'Reference Number' => $transaction['reference_number'] ?? '',
                'Customer Name' => $transaction['customer_name'] ?? '',
                'Customer Code' => $transaction['customer_code'] ?? '',
                'Amount (EGP)' => $transaction['amount'] ?? '',
                'Commission (EGP)' => $transaction['commission'] ?? '',
                'Deduction (EGP)' => $transaction['deduction'] ?? '',
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
            'Commission (EGP)',
            'Deduction (EGP)',
            'Transaction Type',
            'Status',
            'Agent Name',
            'Transaction Date/Time',
            'Source',
        ];
    }
}
