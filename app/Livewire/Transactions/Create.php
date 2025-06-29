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

    public $amount = 0;

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

    // New property for absolute withdrawal
    public $isAbsoluteWithdrawal = false;

    private CreateTransaction $createTransactionUseCase;

    public $branches;
    public $lines;
    public $safes;

    public function rules()
    {
        return [
            'amount' => [
                'required',
                'integer',
                'min:5',
                function ($attribute, $value, $fail) {
                    if ($value % 5 !== 0) {
                        $fail('The ' . $attribute . ' must be a multiple of 5.');
                    }
                },
            ],
        ];
    }

    public function boot(CreateTransaction $createTransactionUseCase)
    {
        $this->createTransactionUseCase = $createTransactionUseCase;
    }

    public function mount()
    {
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

    public function updatedTransactionType()
    {
        // Reset absolute withdrawal flag if transaction type changes from Withdrawal
        if ($this->transactionType !== 'Withdrawal') {
            $this->isAbsoluteWithdrawal = false;
        }
    }

    public function createTransaction()
    {
        $this->validate();

        // Ensure isAbsoluteWithdrawal is only true for Admin and Withdrawal type
        if (!Auth::user()->isAdmin() || $this->transactionType !== 'Withdrawal') {
            $this->isAbsoluteWithdrawal = false;
        }

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
                Auth::user()->id, // Pass agent_id instead of agentName
                $this->status,
                $this->branchId,
                $this->lineId,
                $this->safeId,
                $this->isAbsoluteWithdrawal // Pass the new parameter
            );

            session()->flash('message', 'Transaction created successfully.');
            $this->reset(['customerName', 'customerMobileNumber', 'lineMobileNumber', 'customerCode', 'amount', 'commission', 'deduction', 'transactionType', 'branchId', 'lineId', 'safeId', 'isAbsoluteWithdrawal']); // Clear form fields after submission
            $this->calculateCommission(); // Recalculate commission after reset
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create transaction: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $this->branches = Branch::all();
        $this->lines = Line::all();
        $this->safes = Safe::all();

        return view('livewire.transactions.create', [
            'branches' => $this->branches,
            'lines' => $this->lines,
            'safes' => $this->safes,
        ]);
    }
}
