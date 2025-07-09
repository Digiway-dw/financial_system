<?php

namespace App\Livewire\Transactions;

use App\Livewire\Transactions\Create;

class Withdrawal extends Create
{
    public function mount()
    {
        parent::mount();
        $this->transactionType = 'Withdrawal';
    }
    public function render()
    {
        return view('livewire.transactions.withdrawal');
    }
} 