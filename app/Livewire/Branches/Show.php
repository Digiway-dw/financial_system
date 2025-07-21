<?php

namespace App\Livewire\Branches;

use Livewire\Component;
use App\Models\Domain\Entities\Branch;
use App\Models\Domain\Entities\Transaction;
use App\Models\Domain\Entities\CashTransaction;
use Livewire\WithPagination;

class Show extends Component
{
    use WithPagination;
    public $branch;
    public $branchTransactions = [];
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $sortRefresh = 0;

    public function mount($branchId)
    {
        $this->branch = Branch::with('safe')->findOrFail($branchId)->toArray();
        $transactions = Transaction::where('branch_id', $branchId)->get()->toArray();
        $cashTransactions = CashTransaction::where('destination_branch_id', $branchId)
            ->orWhereHas('safe', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })->get()->toArray();
        $all = array_merge($transactions, $cashTransactions);
        // Attach agent_name (user name) to each transaction
        foreach ($all as &$tx) {
            $userId = $tx['agent_id'] ?? null;
            $user = $userId ? \App\Domain\Entities\User::find($userId) : null;
            $tx['agent_name'] = $user ? $user->name : '-';
        }
        $this->branchTransactions = $all;
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->sortRefresh++;
    }

    public function getSortedTransactions()
    {
        $transactions = collect($this->branchTransactions);
        $field = $this->sortField;
        $direction = $this->sortDirection;
        $sorted = $transactions->sortBy(function($tx) use ($field) {
            return $tx[$field] ?? null;
        }, SORT_REGULAR, $direction === 'desc');
        return $sorted->values()->all();
    }

    public function render()
    {
        $refresh = $this->sortRefresh; // force Livewire to re-render on sort
        return view('livewire.branches.show', [
            'branch' => $this->branch,
            'branchTransactions' => $this->getSortedTransactions(),
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
        ]);
    }
} 