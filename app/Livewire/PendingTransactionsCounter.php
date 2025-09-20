<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Domain\Entities\Transaction;
use App\Models\Domain\Entities\CashTransaction;

class PendingTransactionsCounter extends Component
{
    public $pendingCount = 0;

    public function getListeners()
    {
        return [
            'refreshPendingCount' => '$refresh',
        ];
    }

    public function mount()
    {
        $this->loadCount();
    }

    public function loadCount()
    {
        $user = Auth::user();
        
        // Only show count for admin and general_supervisor
        if (!$user->hasAnyRole(['admin', 'general_supervisor'])) {
            $this->pendingCount = 0;
            return;
        }

    // Count all pending regular transactions (withdrawals and line transfers)
    $pendingTxCount = Transaction::where('status', 'Pending')->count();

    // Count all pending cash transactions
    $pendingCashCount = CashTransaction::where('status', 'pending')->count();

    $this->pendingCount = $pendingTxCount + $pendingCashCount;
    }

    public function render()
    {
        $this->loadCount();
        return view('livewire.pending-transactions-counter');
    }
} 