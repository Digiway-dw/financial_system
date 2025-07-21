<?php

namespace App\Livewire\Branches;

use App\Application\UseCases\ListBranches;
use App\Application\UseCases\DeleteBranch;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Collection;

class Index extends Component
{
    public Collection $branches;

    public $sortField = 'name';
    public $sortDirection = 'asc';

    public $selectedBranch = null;
    public $branchTransactions = [];

    private ListBranches $listBranchesUseCase;
    private DeleteBranch $deleteBranchUseCase;

    public function boot(ListBranches $listBranchesUseCase, DeleteBranch $deleteBranchUseCase)
    {
        $this->listBranchesUseCase = $listBranchesUseCase;
        $this->deleteBranchUseCase = $deleteBranchUseCase;
    }

    public function mount()
    {
        Gate::authorize('view-branches');
        $this->loadBranches();
    }

    public function loadBranches()
    {
        $filters = [
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
        ];
        $this->branches = $this->listBranchesUseCase->execute($filters);
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->loadBranches();
    }

    public function deleteBranch(string $branchId)
    {
        Gate::authorize('delete-branches');
        try {
            $this->deleteBranchUseCase->execute($branchId);
            session()->flash('message', 'Branch deleted successfully.');
            $this->loadBranches();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete branch: ' . $e->getMessage());
        }
    }

    public function viewBranch($branchId)
    {
        $branch = $this->branches->firstWhere('id', $branchId);
        $this->selectedBranch = $branch ? $branch->toArray() : null;
        if ($this->selectedBranch) {
            // Load all transactions for this branch (ordinary + cash)
            $transactions = \App\Models\Domain\Entities\Transaction::where('branch_id', $branchId)->get()->toArray();
            $cashTransactions = \App\Models\Domain\Entities\CashTransaction::where('destination_branch_id', $branchId)
                ->orWhereHas('safe', function($q) use ($branchId) {
                    $q->where('branch_id', $branchId);
                })->get()->toArray();
            $this->branchTransactions = array_merge($transactions, $cashTransactions);
        } else {
            $this->branchTransactions = [];
        }
    }

    public function render()
    {
        return view('livewire.branches.index', [
            'branches' => $this->branches->toArray(),
            'selectedBranch' => $this->selectedBranch,
            'branchTransactions' => $this->branchTransactions,
        ]);
    }
} 