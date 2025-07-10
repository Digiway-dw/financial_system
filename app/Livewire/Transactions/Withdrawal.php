<?php

namespace App\Livewire\Transactions;

use App\Livewire\Transactions\Create;
use App\Domain\Entities\User;
use App\Application\UseCases\CreateTransaction;
use App\Domain\Interfaces\CustomerRepository;
use Illuminate\Support\Facades\Auth;
use App\Models\Domain\Entities\Transaction;
use App\Models\Domain\Entities\Safe;

class Withdrawal extends Create
{
    public $withdrawalType = 'direct';
    public $customerName, $amount, $notes;
    public $userId, $branchUsers = [];
    public $clientCode, $clientNumber, $clientNationalNumber;
    public $safeId = 0;
    public $branchSafes = [];

    private CreateTransaction $createTransactionUseCase;
    private CustomerRepository $customerRepository;

    public function boot(CreateTransaction $createTransactionUseCase, CustomerRepository $customerRepository)
    {
        $this->createTransactionUseCase = $createTransactionUseCase;
        $this->customerRepository = $customerRepository;
    }

    public function mount()
    {
        parent::mount();
        $this->transactionType = 'Withdrawal';
        $this->withdrawalType = 'direct';
        $this->branchUsers = User::where('branch_id', Auth::user()->branch_id ?? null)->get();
        $this->branchSafes = Safe::where('branch_id', Auth::user()->branch_id ?? null)->get();
        if (count($this->branchSafes) > 0) {
            $this->safeId = $this->branchSafes[0]->id;
        }
    }

    public function submitWithdrawal()
    {
        try {
            $agent = Auth::user();
            $branchId = $agent->branch_id;
            $safeId = $this->safeId ?? 0;
            $lineId = 0;
            $paymentMethod = 'branch safe';
            $customerName = $this->customerName;
            $customerMobileNumber = '';
            $customerCode = '';
            $commission = 0;
            $deduction = 0;
            $gender = 'Male';
            $isClient = false;
            $transactionType = 'Withdrawal';
            $notes = $this->notes;

            if ($this->withdrawalType === 'user') {
                $customerName = User::find($this->userId)?->name ?? '';
            }
            if ($this->withdrawalType === 'client_wallet') {
                $paymentMethod = 'client wallet';
                $customer = $this->customerRepository->findByCustomerCode($this->clientCode) ?? $this->customerRepository->findByMobileNumber($this->clientNumber);
                if (!$customer || !isset($customer->balance)) {
                    session()->flash('error', 'No wallet found for this client.');
                    return;
                }
                
                // Check if customer has sufficient balance
                if ($customer->balance < $this->amount) {
                    session()->flash('error', 'Insufficient balance in client wallet.');
                    return;
                }
                
                $customerName = $customer->name;
                $customerMobileNumber = $customer->mobile_number;
                $customerCode = $customer->customer_code;
                $isClient = true;
            }
            if ($this->withdrawalType === 'admin') {
                $customerName = 'Admin';
            }

            // For direct withdrawal, bypass CreateTransaction use case and save transaction directly
            if ($this->withdrawalType === 'direct') {
                // Check if safe has sufficient balance
                $safe = Safe::find($safeId);
                if (!$safe || $safe->balance < $this->amount) {
                    session()->flash('error', 'Insufficient balance in the selected safe.');
                    return;
                }
                
                Transaction::create([
                    'customer_name' => $customerName,
                    'amount' => -1 * abs($this->amount), // Negative amount for withdrawal
                    'transaction_type' => $transactionType,
                    'safe_id' => $safeId,
                    'notes' => $this->notes,
                    'status' => 'Completed',
                    'transaction_date_time' => now(),
                ]);
                session()->flash('message', 'Direct withdrawal saved successfully!');
                $this->reset(['customerName', 'amount', 'notes', 'userId', 'clientCode', 'clientNumber', 'clientNationalNumber']);
                return;
            }

            $this->createTransactionUseCase->execute(
                $customerName,
                $customerMobileNumber,
                $customerCode,
                -1 * abs($this->amount), // Negative amount for withdrawal
                $commission,
                $deduction,
                $transactionType,
                $branchId,
                $lineId,
                $safeId,
                $paymentMethod,
                $gender,
                $isClient,
                $notes
            );

            session()->flash('message', 'Withdrawal processed successfully!');
            $this->reset(['customerName', 'amount', 'notes', 'userId', 'clientCode', 'clientNumber', 'clientNationalNumber']);
        } catch (\Exception $e) {
            session()->flash('error', 'Error processing withdrawal: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.transactions.withdrawal', [
            'users' => $this->branchUsers,
            'safes' => $this->branchSafes,
        ]);
    }
}
