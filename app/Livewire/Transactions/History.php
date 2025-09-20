<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Domain\Entities\Transaction;
use App\Models\Domain\Entities\CashTransaction;

class History extends Component
{
    use WithPagination;

    public $activeTab = 'approved'; // 'approved' or 'rejected'
    public $search = '';
    public $referenceNumber = '';
    public $filterType = ''; // 'all', 'transaction', 'cash_transaction'
    public $dateFrom = '';
    public $dateTo = '';

    public function mount()
    {
        // Authorization check
        $user = Auth::user();
        if (!$user->can('approve-pending-transactions') && !$user->can('view-own-branch-data')) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedReferenceNumber()
    {
        $this->resetPage();
    }

    public function updatedFilterType()
    {
        $this->resetPage();
    }

    public function updatedDateFrom()
    {
        $this->resetPage();
    }

    public function updatedDateTo()
    {
        $this->resetPage();
    }

    public function getApprovedTransactionsProperty()
    {
        $user = Auth::user();
        $query = Transaction::whereIn('status', ['completed', 'Completed'])
            ->whereNotNull('approved_at')
            ->with(['agent', 'line', 'fromLine', 'toLine']);

        // Apply branch filter for branch managers
        if ($user->can('view-own-branch-data') && !$user->can('approve-pending-transactions')) {
            $query->where('branch_id', $user->branch_id);
        }

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('customer_name', 'like', '%' . $this->search . '%')
                  ->orWhere('customer_mobile_number', 'like', '%' . $this->search . '%')
                  ->orWhere('receiver_mobile_number', 'like', '%' . $this->search . '%');
            });
        }

        // Apply reference number search
        if ($this->referenceNumber) {
            $query->where('reference_number', 'like', '%' . $this->referenceNumber . '%');
        }

        // Apply date filters
        if ($this->dateFrom) {
            $query->whereDate('approved_at', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->whereDate('approved_at', '<=', $this->dateTo);
        }

        return $query->orderBy('approved_at', 'desc')->paginate(10);
    }

    public function getApprovedCashTransactionsProperty()
    {
        $user = Auth::user();
        $query = CashTransaction::whereIn('status', ['completed', 'Completed'])
            ->with(['agent']);

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('customer_name', 'like', '%' . $this->search . '%')
                  ->orWhere('customer_code', 'like', '%' . $this->search . '%')
                  ->orWhere('depositor_mobile_number', 'like', '%' . $this->search . '%');
            });
        }

        // Apply reference number search
        if ($this->referenceNumber) {
            $query->where('reference_number', 'like', '%' . $this->referenceNumber . '%');
        }

        // Apply date filters - use approved_at if available, otherwise use updated_at
        if ($this->dateFrom) {
            $query->where(function ($q) {
                $q->whereDate('approved_at', '>=', $this->dateFrom)
                  ->orWhere(function ($subQuery) {
                      $subQuery->whereNull('approved_at')
                               ->whereDate('updated_at', '>=', $this->dateFrom);
                  });
            });
        }
        if ($this->dateTo) {
            $query->where(function ($q) {
                $q->whereDate('approved_at', '<=', $this->dateTo)
                  ->orWhere(function ($subQuery) {
                      $subQuery->whereNull('approved_at')
                               ->whereDate('updated_at', '<=', $this->dateTo);
                  });
            });
        }

        return $query->orderByRaw('COALESCE(approved_at, updated_at) desc')->paginate(10);
    }

    public function getRejectedTransactionsProperty()
    {
        $user = Auth::user();
        $query = Transaction::whereIn('status', ['rejected', 'Rejected'])
            ->whereNotNull('rejected_at')
            ->with(['agent', 'line', 'fromLine', 'toLine']);

        // Apply branch filter for branch managers
        if ($user->can('view-own-branch-data') && !$user->can('approve-pending-transactions')) {
            $query->where('branch_id', $user->branch_id);
        }

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('customer_name', 'like', '%' . $this->search . '%')
                  ->orWhere('customer_mobile_number', 'like', '%' . $this->search . '%')
                  ->orWhere('receiver_mobile_number', 'like', '%' . $this->search . '%');
            });
        }

        // Apply reference number search
        if ($this->referenceNumber) {
            $query->where('reference_number', 'like', '%' . $this->referenceNumber . '%');
        }

        // Apply date filters
        if ($this->dateFrom) {
            $query->whereDate('rejected_at', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->whereDate('rejected_at', '<=', $this->dateTo);
        }

        return $query->orderBy('rejected_at', 'desc')->paginate(10);
    }

    public function getRejectedCashTransactionsProperty()
    {
        $user = Auth::user();
        $query = CashTransaction::whereIn('status', ['rejected', 'Rejected'])
            ->whereNotNull('rejected_at')
            ->with(['agent']);

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('customer_name', 'like', '%' . $this->search . '%')
                  ->orWhere('customer_code', 'like', '%' . $this->search . '%')
                  ->orWhere('depositor_mobile_number', 'like', '%' . $this->search . '%');
            });
        }

        // Apply reference number search
        if ($this->referenceNumber) {
            $query->where('reference_number', 'like', '%' . $this->referenceNumber . '%');
        }

        // Apply date filters
        if ($this->dateFrom) {
            $query->whereDate('rejected_at', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->whereDate('rejected_at', '<=', $this->dateTo);
        }

        return $query->orderBy('rejected_at', 'desc')->paginate(10);
    }

    public function render()
    {
        $approvedTransactions = collect();
        $rejectedTransactions = collect();

        if ($this->activeTab === 'approved') {
            if ($this->filterType === 'cash_transaction') {
                $approvedTransactions = $this->approvedCashTransactions;
            } elseif ($this->filterType === 'transaction') {
                $approvedTransactions = $this->approvedTransactions;
            } else {
                // Combine both types
                $regularTransactions = $this->approvedTransactions->getCollection()->map(function ($item) {
                    $item->source_type = 'transaction';
                    return $item;
                });
                $cashTransactions = $this->approvedCashTransactions->getCollection()->map(function ($item) {
                    $item->source_type = 'cash_transaction';
                    return $item;
                });
                $approvedTransactions = $regularTransactions->merge($cashTransactions)->sortByDesc('approved_at');
            }
        } else {
            if ($this->filterType === 'cash_transaction') {
                $rejectedTransactions = $this->rejectedCashTransactions;
            } elseif ($this->filterType === 'transaction') {
                $rejectedTransactions = $this->rejectedTransactions;
            } else {
                // Combine both types
                $regularTransactions = $this->rejectedTransactions->getCollection()->map(function ($item) {
                    $item->source_type = 'transaction';
                    return $item;
                });
                $cashTransactions = $this->rejectedCashTransactions->getCollection()->map(function ($item) {
                    $item->source_type = 'cash_transaction';
                    return $item;
                });
                $rejectedTransactions = $regularTransactions->merge($cashTransactions)->sortByDesc('rejected_at');
            }
        }

        return view('livewire.transactions.history', [
            'approvedTransactions' => $approvedTransactions,
            'rejectedTransactions' => $rejectedTransactions,
        ]);
    }
}
