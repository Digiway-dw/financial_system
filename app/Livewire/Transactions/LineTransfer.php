<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Domain\Entities\Line;
use App\Models\Domain\Entities\Transaction;
use App\Application\UseCases\CreateTransaction;

class LineTransfer extends Component
{
    // Line Transfer Details
    #[Validate('required|exists:lines,id')]
    public $fromLineId = '';
    
    #[Validate('required|exists:lines,id|different:fromLineId')]
    public $toLineId = '';
    
    #[Validate('required|numeric|min:0.01')]
    public $amount = null;
    
    #[Validate('nullable|numeric|min:0')]
    public $extraFee = 0.0;
    
    #[Validate('nullable|string|max:500')]
    public $notes = '';

    // Line options
    public $availableLines = [];
    public $fromLineSearch = '';
    public $toLineSearch = '';
    public $fromLineSuggestions = [];
    public $toLineSuggestions = [];

    // Calculated values
    public $baseFee = 0;
    public $totalDeducted = 0;
    public $finalAmount = 0;

    // UI State
    public $errorMessage = '';
    public $successMessage = '';

    protected function rules()
    {
        return [
            'fromLineId' => 'required|exists:lines,id',
            'toLineId' => 'required|exists:lines,id|different:fromLineId',
            'amount' => 'required|numeric|min:0.01',
            'extraFee' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ];
    }

    public function mount()
    {
        $this->loadAvailableLines();
    }

    public function loadAvailableLines()
    {
        $user = Auth::user();
        
        // Load lines based on user role
        if ($user->hasRole('admin') || $user->hasRole('general_supervisor')) {
            // Admins and supervisors can see all active lines
            $this->availableLines = Line::where('status', 'active')
                ->with('branch')
                ->orderBy('mobile_number')
                ->get(['id', 'mobile_number', 'current_balance', 'branch_id'])
                ->toArray();
        } else {
            // Other users can only see lines from their branch
            $this->availableLines = Line::where('status', 'active')
                ->where('branch_id', $user->branch_id)
                ->with('branch')
                ->orderBy('mobile_number')
                ->get(['id', 'mobile_number', 'current_balance', 'branch_id'])
                ->toArray();
        }
    }

    public function updatedFromLineSearch()
    {
        if (strlen($this->fromLineSearch) >= 3) {
            $this->fromLineSuggestions = collect($this->availableLines)
                ->filter(function ($line) {
                    return str_contains($line['mobile_number'], $this->fromLineSearch);
                })
                ->take(5)
                ->values()
                ->toArray();
        } else {
            $this->fromLineSuggestions = [];
        }
    }

    public function updatedToLineSearch()
    {
        if (strlen($this->toLineSearch) >= 3) {
            $this->toLineSuggestions = collect($this->availableLines)
                ->filter(function ($line) {
                    return str_contains($line['mobile_number'], $this->toLineSearch) && 
                           $line['id'] != $this->fromLineId; // Exclude selected from line
                })
                ->take(5)
                ->values()
                ->toArray();
        } else {
            $this->toLineSuggestions = [];
        }
    }

    public function selectFromLine($lineId)
    {
        $line = collect($this->availableLines)->firstWhere('id', $lineId);
        if ($line) {
            $this->fromLineId = $lineId;
            $this->fromLineSearch = $line['mobile_number'];
            $this->fromLineSuggestions = [];
            $this->calculateFees();
        }
    }

    public function selectToLine($lineId)
    {
        $line = collect($this->availableLines)->firstWhere('id', $lineId);
        if ($line) {
            $this->toLineId = $lineId;
            $this->toLineSearch = $line['mobile_number'];
            $this->toLineSuggestions = [];
        }
    }

    public function updatedAmount()
    {
        $this->calculateFees();
    }

    public function updatedExtraFee()
    {
        $this->calculateFees();
    }

    public function calculateFees()
    {
        if ($this->amount > 0) {
            // Base fee = 1% of amount
            $this->baseFee = $this->amount * 0.01;
            
            // Total deducted = amount + base fee + extra fee
                $this->totalDeducted = $this->amount + $this->baseFee + (float)($this->extraFee ?: 0);
            
            // Final amount received by to line = original amount
            $this->finalAmount = $this->amount;
        } else {
            $this->baseFee = 0;
            $this->totalDeducted = 0;
            $this->finalAmount = 0;
        }
    }

    public function submitTransfer()
    {
        $this->validate();

        // Clear previous messages
        $this->errorMessage = '';
        $this->successMessage = '';

        DB::beginTransaction();
        try {
            // Get the from and to lines
            $fromLine = Line::findOrFail($this->fromLineId);
            $toLine = Line::findOrFail($this->toLineId);

            // Validate that lines are different
            if ($fromLine->id === $toLine->id) {
                throw new \Exception('لا يمكن التحويل من خط إلى نفس الخط.');
            }

            // Calculate fees
            $this->calculateFees();

            // Check from line balance
            if ($fromLine->current_balance < $this->totalDeducted) {
                throw new \Exception(
                    'رصيد الخط المرسل غير كافي. الرصيد المتاح: ' . number_format($fromLine->current_balance, 2) . 
                    ' ج.م، المطلوب: ' . number_format($this->totalDeducted, 2) . ' ج.م'
                );
            }

            $user = Auth::user();
            $isAdminOrSupervisor = $user->hasRole('admin') || $user->hasRole('general_supervisor');
            $needsApproval = !$isAdminOrSupervisor;
            // Always use 'Pending' for transactions needing approval, 'Completed' otherwise
            $status = $needsApproval ? 'Pending' : 'Completed';

            // Generate reference number using the branch assigned to the fromLine
            $branchName = $fromLine->branch ? $fromLine->branch->name : 'Unknown';
            $referenceNumber = generate_reference_number($branchName);

            // Create the transaction using the new use case method
            $createTransactionUseCase = app(\App\Application\UseCases\CreateTransaction::class);
            $transaction = $createTransactionUseCase->executeLineTransfer(
                $this->fromLineId,
                $this->toLineId,
                $this->amount,
                (float)($this->extraFee ?? 0),
                $user->id,
                $status,
                $this->notes,
                $referenceNumber
            );

            // No notifications for line transfer transactions

            DB::commit();

            if ($isAdminOrSupervisor) {
                // For admin/supervisor, redirect to receipt print screen
                return redirect()->route('transactions.receipt', ['referenceNumber' => $referenceNumber]);
            } else {
                // For regular users, redirect to waiting approval page
                return redirect()->route('transactions.waiting-approval', ['transactionId' => $transaction->id]);
            }

        } catch (\Exception $e) {
            DB::rollback();
            $this->errorMessage = 'خطأ في التحويل: ' . $e->getMessage();
        }
    }

    // Notification method removed - line transfers do not send notifications

    public function resetForm()
    {
        $this->reset([
            'fromLineId', 'toLineId', 'amount', 'extraFee', 'notes',
            'fromLineSearch', 'toLineSearch', 'fromLineSuggestions', 'toLineSuggestions',
            'baseFee', 'totalDeducted', 'finalAmount'
        ]);
        $this->loadAvailableLines();
    }

    public function render()
    {
        return view('livewire.transactions.line-transfer');
    }
}
