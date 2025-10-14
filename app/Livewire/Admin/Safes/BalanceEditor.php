<?php

namespace App\Livewire\Admin\Safes;

use Livewire\Component;
use App\Models\Domain\Entities\Safe;
use App\Models\Domain\Entities\Transaction;
use App\Models\Domain\Entities\Branch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BalanceEditor extends Component
{
    public $safes = [];
    public $selectedSafeId = null;
    public $selectedSafe = null;
    public $newBalance = '';
    public $adjustmentReason = '';
    public $showConfirmation = false;
    public $successMessage = '';
    public $errorMessage = '';

    protected $rules = [
        'selectedSafeId' => 'required|exists:safes,id',
        'newBalance' => 'required|integer|min:0',
        'adjustmentReason' => 'required|string|min:10|max:500',
    ];

    protected $messages = [
        'selectedSafeId.required' => 'يرجى اختيار خزينة أولاً.',
        'newBalance.required' => 'يرجى إدخال الرصيد الجديد.',
        'newBalance.integer' => 'الرصيد يجب أن يكون عددا صحيحا (بدون فواصل).',
        'newBalance.min' => 'الرصيد لا يمكن أن يكون سالبا.',
        'adjustmentReason.required' => 'يرجى تقديم سبب لهذا التعديل في الرصيد.',
        'adjustmentReason.min' => 'يجب أن يكون السبب على الأقل 10 أحرف.',
        'adjustmentReason.max' => 'السبب لا يمكن أن يتجاوز 500 حرف.',
    ];

    public function mount()
    {
        // Only allow admins to access this feature
        if (!Auth::user() || !Auth::user()->hasRole('admin')) {
            abort(403, 'لا يمكن تعديل رصيد الخزينة للمستخدمين غير المديرين.');
        }

        $this->loadSafes();
    }

    public function loadSafes()
    {
        $this->safes = Safe::with('branch')
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function ($safe) {
                return [
                    'id' => $safe->id,
                    'name' => $safe->name,
                    'current_balance' => $safe->current_balance,
                    'branch_name' => $safe->branch->name ?? 'Unknown',
                    'type' => $safe->type ?? 'standard',
                ];
            })
            ->toArray();
    }

    public function updatedSelectedSafeId()
    {
        if ($this->selectedSafeId) {
            $this->selectedSafe = collect($this->safes)->firstWhere('id', $this->selectedSafeId);
            $this->newBalance = $this->selectedSafe['current_balance'] ?? '';
        } else {
            $this->selectedSafe = null;
            $this->newBalance = '';
        }
        
        $this->resetValidation();
        $this->showConfirmation = false;
    }

    public function prepareBalanceUpdate()
    {
        $this->validate();

        if (!$this->selectedSafe) {
            $this->errorMessage = 'يرجى اختيار خزينة أولاً.';
            return;
        }

        $currentBalance = $this->selectedSafe['current_balance'];
        $newBalance = (int) $this->newBalance;

        if ($currentBalance == $newBalance) {
            $this->errorMessage = 'الرصيد الجديد هو نفس الرصيد الحالي. لا توجد تغييرات.';
            return;
        }

        $this->showConfirmation = true;
        $this->errorMessage = '';
    }

    public function confirmBalanceUpdate()
    {
        $this->validate();

        if (!$this->selectedSafe) {
            $this->errorMessage = 'فقدت اختيار الخزينة. يرجى المحاولة مرة أخرى.';
            return;
        }

        try {
            DB::beginTransaction();

            $safe = Safe::findOrFail($this->selectedSafeId);
            $oldBalance = $safe->current_balance;
            $newBalance = (int) $this->newBalance;
            $difference = $newBalance - $oldBalance;

            // Update safe balance
            $safe->current_balance = $newBalance;
            $safe->save();

            // Create transaction log entry
            $transaction = Transaction::create([
                'customer_name' => 'ADMIN BALANCE ADJUSTMENT',
                'customer_mobile_number' => null,
                'line_id' => null,
                'customer_code' => 'ADMIN',
                'amount' => abs($difference),
                'commission' => 0,
                'deduction' => 0,
                'transaction_type' => 'Adjustment',
                'agent_id' => Auth::id(),
                'transaction_date_time' => now(),
                'status' => 'completed',
                'safe_id' => $safe->id,
                'notes' => ($difference > 0 ? 'Safe balance increase' : 'Safe balance decrease') . " by " . Auth::user()->name . ". Reason: " . $this->adjustmentReason,
                'approved_at' => now(),
                'approved_by' => Auth::id(),
                'reference_number' => generate_reference_number($safe->branch->name ?? 'ADMIN'),
            ]);

         

            DB::commit();

            $this->successMessage = "Safe balance successfully updated from {$oldBalance} to {$newBalance}. Transaction logged with ID: {$transaction->id}";
            $this->resetForm();
            $this->loadSafes(); // Refresh the safes list

        } catch (\Exception $e) {
            DB::rollBack();
           
            $this->errorMessage = 'فشل تحديث رصيد الخزينة. يرجى المحاولة مرة أخرى أو الاتصال بالدعم.';
        }
    }

    public function cancelConfirmation()
    {
        $this->showConfirmation = false;
        $this->errorMessage = '';
    }

    public function resetForm()
    {
        $this->selectedSafeId = null;
        $this->selectedSafe = null;
        $this->newBalance = '';
        $this->adjustmentReason = '';
        $this->showConfirmation = false;
        $this->errorMessage = '';
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.safes.balance-editor');
    }
}
