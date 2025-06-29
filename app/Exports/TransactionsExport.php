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
        return $this->transactions->map(function($transaction) {
            return [
                'Transaction ID' => $transaction->id,
                'Customer Name' => $transaction->customer_name,
                'Customer Mobile' => $transaction->customer_mobile_number,
                'Line Mobile' => $transaction->line_mobile_number,
                'Customer Code' => $transaction->customer_code,
                'Amount (EGP)' => $transaction->amount,
                'Commission (EGP)' => $transaction->commission,
                'Deduction (EGP)' => $transaction->deduction,
                'Transaction Type' => $transaction->transaction_type,
                'Status' => $transaction->status,
                'Agent Name' => $transaction->agent->name ?? 'N/A',
                'Branch Name' => $transaction->branch->name ?? 'N/A',
                'Line Balance' => $transaction->line->current_balance ?? 'N/A',
                'Safe Balance' => $transaction->safe->current_balance ?? 'N/A',
                'Transaction Date/Time' => $transaction->transaction_date_time,
                'Approved At' => $transaction->approved_at,
                'Approved By' => $transaction->approvedBy->name ?? 'N/A',
                'Rejected At' => $transaction->rejected_at,
                'Rejected By' => $transaction->rejectedBy->name ?? 'N/A',
                'Rejection Reason' => $transaction->rejection_reason,
                'Destination Safe' => $transaction->destinationSafe->name ?? 'N/A',
                'Is Absolute Withdrawal' => $transaction->is_absolute_withdrawal ? 'Yes' : 'No',
            ];
        });
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Transaction ID',
            'Customer Name',
            'Customer Mobile',
            'Line Mobile',
            'Customer Code',
            'Amount (EGP)',
            'Commission (EGP)',
            'Deduction (EGP)',
            'Transaction Type',
            'Status',
            'Agent Name',
            'Branch Name',
            'Line Balance',
            'Safe Balance',
            'Transaction Date/Time',
            'Approved At',
            'Approved By',
            'Rejected At',
            'Rejected By',
            'Rejection Reason',
            'Destination Safe',
            'Is Absolute Withdrawal',
        ];
    }
}
