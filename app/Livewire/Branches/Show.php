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
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 30;
    public $page = 1;
    public $branchId;

    public function mount($branchId)
    {
        $this->branchId = $branchId;
        $this->branch = Branch::with('safe')->findOrFail($branchId)->toArray();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function loadMore()
    {
        $this->perPage += 30;
    }

    public function getBranchTransactionsProperty()
    {
        $transactions = Transaction::where('branch_id', $this->branchId);
        $cashTransactions = CashTransaction::where('destination_branch_id', $this->branchId)
            ->orWhereHas('safe', function($q) {
                $q->where('branch_id', $this->branchId);
            });

        $all = $transactions->get()->toBase()->merge($cashTransactions->get())->all();
        // Attach agent_name (user name) to each transaction
        foreach ($all as &$tx) {
            $userId = $tx['agent_id'] ?? null;
            $user = $userId ? \App\Domain\Entities\User::find($userId) : null;
            $tx['agent_name'] = $user ? $user->name : '-';
        }
        // Sort
        $sorted = collect($all)->sortBy(function($tx) {
            $field = $this->sortField;
            return $tx[$field] ?? null;
        }, SORT_REGULAR, $this->sortDirection === 'desc');
        // Paginate manually
        return $sorted->values()->slice(0, $this->perPage);
    }

    public function render()
    {
        return view('livewire.branches.show', [
            'branch' => $this->branch,
            'branchTransactions' => $this->branchTransactions,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'hasMore' => count($this->branchTransactions) >= $this->perPage,
        ]);
    }

    public function resetPage()
    {
        $this->perPage = 30;
    }
}