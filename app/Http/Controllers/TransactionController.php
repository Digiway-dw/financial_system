<?php

namespace App\Http\Controllers;

use App\Models\Domain\Entities\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function receipt($referenceNumber)
    {
        $transaction = Transaction::with(['agent', 'branch'])->where('reference_number', $referenceNumber)->firstOrFail();
        return view('receipt', compact('transaction'));
    }

    public function cashReceipt($referenceNumber)
    {
        $cashTransaction = \App\Models\Domain\Entities\CashTransaction::with(['agent', 'safe.branch'])->where('reference_number', $referenceNumber)->firstOrFail();
        return view('cash_receipt', compact('cashTransaction'));
    }
} 