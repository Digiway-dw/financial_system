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
    public $perPage = 30;
    public $dailyReceive = 0;
    public $monthlyReceive = 0;

    protected $paginationTheme = 'tailwind';

    public function mount($lineId)
    {
        $this->lineId = $lineId;
        $this->line = Line::with('branch')->findOrFail($lineId);
        
        // Calculate daily and monthly receive amounts
        $this->calculateReceiveAmounts();
    }
    
    private function calculateReceiveAmounts()
    {
        // Calculate daily receive (transactions received today)
        $today = now()->startOfDay();
        $this->dailyReceive = Transaction::where('line_id', $this->lineId)
            ->where('transaction_type', 'receive')
            ->whereDate('transaction_date_time', $today)
            ->sum('amount');
            
        // Calculate monthly receive (transactions received this month)
        $thisMonth = now()->startOfMonth();
        $this->monthlyReceive = Transaction::where('line_id', $this->lineId)
            ->where('transaction_type', 'receive')
            ->whereDate('transaction_date_time', '>=', $thisMonth)
            ->sum('amount');
    }

    public function loadMore()
    {
        $this->perPage += 30;
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
