<?php

namespace App\Livewire\Transactions;

use App\Livewire\Transactions\Create;

class Send extends Create
{
    public function mount()
    {
        parent::mount();
        $this->transactionType = 'Transfer';
        $this->branches = \App\Models\Domain\Entities\Branch::all();
        $this->lines = \App\Models\Domain\Entities\Line::all();
        $this->safes = \App\Models\Domain\Entities\Safe::all();
    }
    public function render()
    {
        return view('livewire.transactions.send');
    }
} 