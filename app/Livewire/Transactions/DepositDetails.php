<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Domain\Entities\Transaction;
use App\Models\Domain\Entities\Safe;
use Illuminate\Support\Facades\DB;

class DepositDetails extends Component
{
    public $depositType = 'direct';
    public $direct_name;
    public $direct_amount;
    public $direct_note;
    public $successMessage;
    public $errorMessage;

    protected function rules()
    {
        if ($this->depositType === 'direct') {
            return [
                'direct_name' => 'required|string|max:255',
                'direct_amount' => 'required|numeric|min:0.01',
                'direct_note' => 'nullable|string|max:1000',
            ];
        }
        return [];
    }

    public function submitDirectDeposit()
    {
        $this->validate();
        try {
            DB::transaction(function () {
                $user = Auth::user();
                $branch = $user->branch;
                $safe = $branch ? $branch->safe : null;
                if (!$safe) {
                    throw new \Exception('No safe found for your branch.');
                }
                // Create transaction record
                Transaction::create([
                    'customer_name' => $this->direct_name,
                    'amount' => $this->direct_amount,
                    'transaction_type' => 'Deposit',
                    'safe_id' => $safe->id,
                    'notes' => $this->direct_note,
                    'status' => 'Completed',
                    'transaction_date_time' => now(),
                    'agent_id' => $user->id,
                ]);
                // Update safe balance
                $safe->increment('current_balance', $this->direct_amount);
            });
            $this->successMessage = 'Direct deposit successful!';
            $this->errorMessage = null;
            $this->direct_name = null;
            $this->direct_amount = null;
            $this->direct_note = null;
        } catch (\Exception $e) {
            $this->errorMessage = 'Deposit failed: ' . $e->getMessage();
            $this->successMessage = null;
        }
    }

    public function render()
    {
        return view('livewire.transactions.deposit-details');
    }
}
