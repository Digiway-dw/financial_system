<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\Domain\Entities\CashTransaction;

class Cash extends Component
{
    public $user;

    public function mount()
    {
        $this->user = Auth::user();

        // Check if user has access to cash transactions
        if (!Gate::allows('cash-transactions')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to access cash transactions.');
        }
    }

    public function render()
    {
        return view('livewire.transactions.cash', [
            'canDeposit' => Gate::allows('deposit-cash'),
            'canWithdraw' => Gate::allows('withdraw-cash'),
            'recentCashTransactions' => CashTransaction::orderByDesc('transaction_date_time')->limit(5)->get(),
        ]);
    }

    public function deleteCashTransaction($id)
    {
        \App\Models\Domain\Entities\CashTransaction::find($id)?->delete();
        // No need to manually refresh, as render() will re-query
    }
}
