<?php

namespace App\Livewire\Transactions;

use App\Livewire\Transactions\Create;
use App\Domain\Entities\User;
use App\Application\UseCases\CreateTransaction;
use App\Domain\Interfaces\CustomerRepository;
use Illuminate\Support\Facades\Auth;
use App\Models\Domain\Entities\Transaction;

class Deposit extends Create
{
    public $depositType = 'direct';
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
        $this->transactionType = 'Deposit';
        $this->depositType = 'direct';
        $this->branchUsers = User::where('branch_id', Auth::user()->branch_id ?? null)->get();
        $this->branchSafes = \App\Models\Domain\Entities\Safe::where('branch_id', Auth::user()->branch_id ?? null)->get();
        if (count($this->branchSafes) > 0) {
            $this->safeId = $this->branchSafes[0]->id;
        }
    }

    public function submitDeposit()
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
            $transactionType = 'Deposit';
            $notes = $this->notes;

            if ($this->depositType === 'user') {
                $customerName = User::find($this->userId)?->name ?? '';
            }
            if ($this->depositType === 'client_wallet') {
                $paymentMethod = 'client wallet';
                $customer = $this->customerRepository->findByCustomerCode($this->clientCode) ?? $this->customerRepository->findByMobileNumber($this->clientNumber);
                if (!$customer || !isset($customer->balance)) {
                    session()->flash('message', 'لا يوجد محفظة لهذا العميل.');
                    return;
                }
                $customerName = $customer->name;
                $customerMobileNumber = $customer->mobile_number;
                $customerCode = $customer->customer_code;
                $isClient = true;
            }
            if ($this->depositType === 'admin') {
                $customerName = 'اداري';
            }

            // For direct deposit, bypass CreateTransaction use case and save transaction directly
            if ($this->depositType === 'direct') {
                Transaction::create([
                    'customer_name' => $customerName,
                    'amount' => $this->amount,
                    'transaction_type' => $transactionType,
                    'safe_id' => $safeId,
                    'notes' => $this->notes,
                    'status' => 'Completed',
                    'transaction_date_time' => now(),
                ]);
                session()->flash('message', 'تم حفظ الإيداع المباشر بنجاح!');
                $this->reset(['customerName', 'amount', 'notes', 'userId', 'clientCode', 'clientNumber', 'clientNationalNumber']);
                return;
            }

            $this->createTransactionUseCase->execute(
                $customerName,
                $customerMobileNumber,
                $customerCode,
                $this->amount,
                $commission,
                $deduction,
                $transactionType,
                $agent->id,
                0, // lineId as int
                $safeId,
                false,
                $paymentMethod,
                $gender,
                $isClient
            );
            session()->flash('message', 'تم حفظ الإيداع بنجاح!');
            $this->reset(['customerName', 'amount', 'notes', 'userId', 'clientCode', 'clientNumber', 'clientNationalNumber']);
        } catch (\Exception $e) {
            session()->flash('message', 'خطأ: ' . $e->getMessage());
        }
    }
    public function render()
    {
        return view('livewire.transactions.deposit');
    }
}
