<?php

namespace App\Services;

use App\Models\Domain\Entities\Transaction;
use Illuminate\Support\Facades\View;

class ReceiptPrinterService
{
    /**
     * Print a receipt for a transaction using the specified method.
     * @param Transaction $transaction
     * @param string $method 'html' (ESC/POS not supported in new version)
     * @return \Illuminate\View\View|null
     */
    public function printReceipt(Transaction $transaction, $method = 'html')
    {
        // Only HTML printing is supported in the new version
        return view('receipt', ['transaction' => $transaction]);
    }
} 