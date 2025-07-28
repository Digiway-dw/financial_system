<?php

namespace App\Livewire\Lines;

use Livewire\Component;
use App\Models\Domain\Entities\Line;
use App\Models\Domain\Entities\Transaction;

use Livewire\WithPagination;

class View extends Component
{
    use WithPagination;

    public $lineId;
    public $line;
    public $perPage = 10;

    protected $paginationTheme = 'tailwind';

    public function mount($lineId)
    {
        $this->lineId = $lineId;
        $this->line = Line::with('branch')->findOrFail($lineId);
    }

    public function loadMore()
    {
        $this->perPage += 10;
    }

    public function render()
    {
        $transactions = Transaction::where('line_id', $this->lineId)
            ->orderByDesc('transaction_date_time')
            ->paginate($this->perPage);
        return view('livewire.lines.view', [
            'transactions' => $transactions,
        ]);
    }
}
