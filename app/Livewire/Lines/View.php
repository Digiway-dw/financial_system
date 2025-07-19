<?php

namespace App\Livewire\Lines;

use Livewire\Component;
use App\Models\Domain\Entities\Line;
use App\Models\Domain\Entities\Transaction;

class View extends Component
{
    public $lineId;
    public $line;
    public $transactions = [];

    public function mount($lineId)
    {
        $this->lineId = $lineId;
        $this->line = Line::with('branch')->findOrFail($lineId);
        $this->transactions = Transaction::where('line_id', $lineId)
            ->orderByDesc('transaction_date_time')
            ->get();
    }

    public function render()
    {
        return view('livewire.lines.view');
    }
}
