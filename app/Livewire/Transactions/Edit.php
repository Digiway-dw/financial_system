<?php

namespace App\Livewire\Transactions;

use App\Application\UseCases\UpdateTransaction;
use App\Domain\Interfaces\TransactionRepository;
use App\Models\Domain\Entities\Branch;
use App\Models\Domain\Entities\Line;
use App\Models\Domain\Entities\Safe;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class Edit extends Component
{
    public $transaction;
    public $transactionId;

    #[Validate('required|string|max:255')] 
    public $customerName = '';

    #[Validate('required|string|max:20')] 
    public $customerMobileNumber = '';

    #[Validate('required|string|max:20')] 
    public $lineMobileNumber = '';

    #[Validate('nullable|string|max:255')] 
    public $customerCode = '';

    #[Validate('required|numeric|min:0.01')] 
    public $amount = 0.00;

    #[Validate('required|numeric|min:0')] 
    public $commission = 0.00;

    #[Validate('required|numeric|min:0')] 
    public $deduction = 0.00;

    #[Validate('required|string|in:Transfer,Withdrawal,Deposit,Adjustment')] 
    public $transactionType = 'Transfer';

    #[Validate('required|string|max:255')] 
    public $agentName = '';

    #[Validate('required|string|in:Completed,Pending,Rejected')] 
    public $status = 'Pending';

    #[Validate('required|exists:branches,id')] 
    public $branchId = '';

    #[Validate('required|exists:lines,id')] 
    public $lineId = '';

    #[Validate('required|exists:safes,id')] 
    public $safeId = '';

    public $branches;
    public $lines;
    public $safes;

    private TransactionRepository $transactionRepository;
    private UpdateTransaction $updateTransactionUseCase;

    public function boot(TransactionRepository $transactionRepository, UpdateTransaction $updateTransactionUseCase)
    {
        $this->transactionRepository = $transactionRepository;
        $this->updateTransactionUseCase = $updateTransactionUseCase;
    }

    public function mount($transactionId)
    {
        $this->transactionId = $transactionId;
        $this->transaction = $this->transactionRepository->findById($transactionId);

        if (!$this->transaction) {
            abort(404);
        }

        // Authorization check for editing transactions
        $user = Auth::user();
        if ($user->can('edit-all-daily-transactions')) {
            // Admin/Auditor can edit any transaction
        } elseif ($user->can('edit-own-branch-transactions') && $user->branch_id === $this->transaction->branch_id) {
            // Branch Manager can edit transactions in their own branch
        } else {
            abort(403, 'Unauthorized action.');
        }

        $this->customerName = $this->transaction->customer_name;
        $this->customerMobileNumber = $this->transaction->customer_mobile_number;
        $this->lineMobileNumber = $this->transaction->line_mobile_number;
        $this->customerCode = $this->transaction->customer_code;
        $this->amount = $this->transaction->amount;
        $this->commission = $this->transaction->commission;
        $this->deduction = $this->transaction->deduction;
        $this->transactionType = $this->transaction->transaction_type;
        $this->agentName = $this->transaction->agent_name;
        $this->status = $this->transaction->status;
        $this->branchId = $this->transaction->branch_id;
        $this->lineId = $this->transaction->line_id;
        $this->safeId = $this->transaction->safe_id;

        $this->branches = Branch::all();
        $this->lines = Line::all();
        $this->safes = Safe::all();
    }

    public function updateTransaction()
    {
        $this->validate();

        try {
            $this->updateTransactionUseCase->execute(
                $this->transactionId,
                [
                    'customer_name' => $this->customerName,
                    'customer_mobile_number' => $this->customerMobileNumber,
                    'line_mobile_number' => $this->lineMobileNumber,
                    'customer_code' => $this->customerCode,
                    'amount' => (float) $this->amount,
                    'commission' => (float) $this->commission,
                    'deduction' => (float) $this->deduction,
                    'transaction_type' => $this->transactionType,
                    'agent_name' => $this->agentName,
                    'status' => $this->status,
                    'branch_id' => $this->branchId,
                    'line_id' => $this->lineId,
                    'safe_id' => $this->safeId,
                ]
            );

            session()->flash('message', 'Transaction updated successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update transaction: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.transactions.edit');
    }
}
