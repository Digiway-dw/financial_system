<?php

namespace App\Http\Controllers;

use App\Models\Domain\Entities\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function receipt($transactionId)
    {
        $transaction = Transaction::with(['agent', 'branch'])->findOrFail($transactionId);
        return view('receipt', compact('transaction'));
    }

    public function cashReceipt($cashTransactionId)
    {
        $cashTransaction = \App\Models\Domain\Entities\CashTransaction::with(['agent', 'safe.branch'])->findOrFail($cashTransactionId);
        return view('cash_receipt', compact('cashTransaction'));
    }
} 