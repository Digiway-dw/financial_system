<?php

namespace App\Livewire\Transactions;

use App\Application\UseCases\CreateTransaction;
use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Models\Domain\Entities\Line;
use App\Models\Domain\Entities\Safe;
use App\Models\Domain\Entities\Branch;
use Illuminate\Support\Facades\Auth;

class Create extends Component
{
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

    public $agentName = '';

    public $status = 'Pending';

    #[Validate('required|exists:branches,id')] 
    public $branchId = '';

    #[Validate('required|exists:lines,id')] 
    public $lineId = '';

    #[Validate('required|exists:safes,id')] 
    public $safeId = '';

    private CreateTransaction $createTransactionUseCase;

    public $branches;
    public $lines;
    public $safes;

    public function boot(CreateTransaction $createTransactionUseCase)
    {
        $this->createTransactionUseCase = $createTransactionUseCase;
    }

    public function mount()
    {
        $this->branches = Branch::all();
        $this->lines = Line::all();
        $this->safes = Safe::all();
        $this->agentName = Auth::user()->name; // Automatically set agent name
        $this->branchId = Auth::user()->branch_id; // Automatically set agent branch

        // Auto-calculate commission based on amount
        $this->calculateCommission();
    }

    public function updatedAmount()
    {
        $this->calculateCommission();
    }

    private function calculateCommission()
    {
        // Example: 5 EGP per 500 EGP
        if ($this->amount > 0) {
            $this->commission = (floor($this->amount / 500) * 5);
        }
    }

    public function createTransaction()
    {
        $this->validate();

        try {
            $this->createTransactionUseCase->execute(
                $this->customerName,
                $this->customerMobileNumber,
                $this->lineMobileNumber,
                $this->customerCode,
                (float) $this->amount,
                (float) $this->commission,
                (float) $this->deduction,
                $this->transactionType,
                $this->agentName,
                $this->status,
                $this->branchId,
                $this->lineId,
                $this->safeId
            );

            session()->flash('message', 'Transaction created successfully.');
            $this->reset(); // Clear form fields after submission
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create transaction: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.transactions.create');
    }
}
