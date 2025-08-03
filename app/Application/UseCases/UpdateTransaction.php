<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\TransactionRepository;
use App\Domain\Interfaces\LineRepository;
use App\Domain\Interfaces\SafeRepository;
use App\Domain\Interfaces\CustomerRepository;
use App\Models\Domain\Entities\Transaction;
use App\Models\Domain\Entities\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AdminNotification;

class UpdateTransaction
{
    private TransactionRepository $transactionRepository;
    private LineRepository $lineRepository;
    private SafeRepository $safeRepository;
    private CustomerRepository $customerRepository;

    public function __construct(
        TransactionRepository $transactionRepository,
        LineRepository $lineRepository,
        SafeRepository $safeRepository,
        CustomerRepository $customerRepository
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->lineRepository = $lineRepository;
        $this->safeRepository = $safeRepository;
        $this->customerRepository = $customerRepository;
    }

    public function execute(string $id, array $attributes): Transaction
    {
        $transaction = $this->transactionRepository->findById($id);

        if (!$transaction) {
            throw new \Exception('Transaction not found.');
        }

        // Store original values for reversal
        $originalAmount = $transaction->amount;
        $originalCommission = $transaction->commission ?? 0;
        $originalDeduction = $transaction->deduction ?? 0;
        $originalTransactionType = $transaction->transaction_type;
        $originalPaymentMethod = $transaction->payment_method ?? 'branch safe';
        $originalStatus = $transaction->status;

        // Get new values
        $newAmount = (float) ($attributes['amount'] ?? $originalAmount);
        $newCommission = (float) ($attributes['commission'] ?? $originalCommission);
        $newDeduction = (float) ($attributes['deduction'] ?? $originalDeduction);
        $newTransactionType = $attributes['transaction_type'] ?? $originalTransactionType;
        $newPaymentMethod = $attributes['payment_method'] ?? $originalPaymentMethod;
        $newStatus = $attributes['status'] ?? $originalStatus;

        // Only proceed with balance updates if the transaction is completed
        $shouldUpdateBalances = in_array($newStatus, ['completed', 'Completed']);

        try {
            DB::beginTransaction();

            // Step 1: Reverse the original transaction effects
            if ($shouldUpdateBalances) {
                $this->reverseTransactionEffects(
                    $transaction,
                    $originalAmount,
                    $originalCommission,
                    $originalDeduction,
                    $originalTransactionType,
                    $originalPaymentMethod
                );
            }

            // Step 2: Update the transaction record
            $updatedTransaction = $this->transactionRepository->update($id, $attributes);

            // Step 3: Apply the new transaction effects
            if ($shouldUpdateBalances) {
                $this->applyTransactionEffects(
                    $updatedTransaction,
                    $newAmount,
                    $newCommission,
                    $newDeduction,
                    $newTransactionType,
                    $newPaymentMethod
                );
            }

            DB::commit();

            Log::info('Transaction updated successfully', [
                'transaction_id' => $id,
                'original_amount' => $originalAmount,
                'new_amount' => $newAmount,
                'original_commission' => $originalCommission,
                'new_commission' => $newCommission,
                'transaction_type' => $newTransactionType,
            ]);

            return $updatedTransaction;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update transaction', [
                'transaction_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Reverse the effects of the original transaction
     */
    private function reverseTransactionEffects(
        Transaction $transaction,
        float $amount,
        float $commission,
        float $deduction,
        string $transactionType,
        string $paymentMethod
    ): void {
        $line = $transaction->line_id ? $this->lineRepository->findById($transaction->line_id) : null;
        $safe = $transaction->safe_id ? $this->safeRepository->findById($transaction->safe_id) : null;
        $customer = $this->findCustomerByMobile($transaction->customer_mobile_number);

        $finalCommission = $commission - $deduction;

        // Reverse Transfer (Send) transaction effects
        if ($transactionType === 'Transfer') {
            if ($line) {
                // Reverse line balance deduction
                $this->lineRepository->update($line->id, [
                    'current_balance' => $line->current_balance + $amount
                ]);
            }

            if ($safe) {
                // Reverse safe balance increase: amount + (commission - deduction)
                $safeDecrease = $amount + $finalCommission;
                $this->safeRepository->update($safe->id, [
                    'current_balance' => $safe->current_balance - $safeDecrease
                ]);
            }

            if ($paymentMethod === 'client wallet' && $customer) {
                // Reverse customer wallet deduction
                $customer->balance += $amount;
                $this->customerRepository->save($customer);
            }
        }

        // Reverse Withdrawal transaction effects
        elseif ($transactionType === 'Withdrawal') {
            if ($safe && $paymentMethod === 'branch safe') {
                // Reverse safe balance deduction
                $this->safeRepository->update($safe->id, [
                    'current_balance' => $safe->current_balance + $amount
                ]);
            }

            if ($line) {
                // Reverse line balance deduction
                $this->lineRepository->update($line->id, [
                    'current_balance' => $line->current_balance + $amount
                ]);
            }

            if ($paymentMethod === 'client wallet' && $customer) {
                // Reverse customer wallet deduction
                $customer->balance += $amount;
                $this->customerRepository->save($customer);
            }
        }

        // Reverse Receive transaction effects
        elseif ($transactionType === 'Receive') {
            if ($line) {
                // Reverse line balance increase and usage
                $this->lineRepository->update($line->id, [
                    'current_balance' => $line->current_balance - $amount,
                    'daily_usage' => max(0, ($line->daily_usage ?? 0) - $amount),
                    'monthly_usage' => max(0, ($line->monthly_usage ?? 0) - $amount),
                    'daily_remaining' => ($line->daily_remaining ?? 0) + $amount,
                    'monthly_remaining' => ($line->monthly_remaining ?? 0) + $amount,
                ]);
            }

            if ($safe) {
                // Reverse safe balance deduction: amount - (commission - deduction)
                $safeIncrease = $amount - $finalCommission;
                $this->safeRepository->update($safe->id, [
                    'current_balance' => $safe->current_balance + $safeIncrease
                ]);
            }
        }

        // Reverse Deposit transaction effects
        elseif ($transactionType === 'Deposit') {
            if ($line) {
                // Reverse line balance increase
                $this->lineRepository->update($line->id, [
                    'current_balance' => $line->current_balance - $amount
                ]);
            }

            if ($paymentMethod === 'client wallet' && $customer) {
                // Reverse customer wallet increase
                $customer->balance -= $amount;
                $this->customerRepository->save($customer);
            }
        }
    }

    /**
     * Apply the effects of the new transaction
     */
    private function applyTransactionEffects(
        Transaction $transaction,
        float $amount,
        float $commission,
        float $deduction,
        string $transactionType,
        string $paymentMethod
    ): void {
        $line = $transaction->line_id ? $this->lineRepository->findById($transaction->line_id) : null;
        $safe = $transaction->safe_id ? $this->safeRepository->findById($transaction->safe_id) : null;
        $customer = $this->findCustomerByMobile($transaction->customer_mobile_number);

        $finalCommission = $commission - $deduction;

        // Apply Transfer (Send) transaction effects
        if ($transactionType === 'Transfer') {
            if ($line) {
                // Check line balance sufficiency
                if (($line->current_balance - $amount) < 0) {
                    throw new \Exception('Insufficient balance in line for this transaction. Available: ' . number_format($line->current_balance, 2) . ' EGP, Required: ' . number_format($amount, 2) . ' EGP');
                }
                
                // Deduct from line balance
                $this->lineRepository->update($line->id, [
                    'current_balance' => $line->current_balance - $amount
                ]);
            }

            if ($safe) {
                // Increase safe balance: amount + (commission - deduction)
                $safeIncrease = $amount + $finalCommission;
                $this->safeRepository->update($safe->id, [
                    'current_balance' => $safe->current_balance + $safeIncrease
                ]);
            }

            if ($paymentMethod === 'client wallet' && $customer) {
                // Check customer wallet sufficiency
                if (($customer->balance - $amount) < 0) {
                    throw new \Exception('Insufficient balance in client wallet for this transaction. Available: ' . number_format($customer->balance, 2) . ' EGP, Required: ' . number_format($amount, 2) . ' EGP');
                }
                
                // Deduct from customer wallet
                $customer->balance -= $amount;
                $this->customerRepository->save($customer);
            }
        }

        // Apply Withdrawal transaction effects
        elseif ($transactionType === 'Withdrawal') {
            if ($safe && $paymentMethod === 'branch safe') {
                // Check safe balance sufficiency
                if (($safe->current_balance - $amount) < 0) {
                    throw new \Exception('Insufficient balance in safe for this transaction. Available: ' . number_format($safe->current_balance, 2) . ' EGP, Required: ' . number_format($amount, 2) . ' EGP');
                }
                
                // Deduct from safe balance
                $this->safeRepository->update($safe->id, [
                    'current_balance' => $safe->current_balance - $amount
                ]);
            }

            if ($line) {
                // Check line balance sufficiency
                if (($line->current_balance - $amount) < 0) {
                    throw new \Exception('Insufficient balance in line for this transaction. Available: ' . number_format($line->current_balance, 2) . ' EGP, Required: ' . number_format($amount, 2) . ' EGP');
                }
                
                // Deduct from line balance
                $this->lineRepository->update($line->id, [
                    'current_balance' => $line->current_balance - $amount
                ]);
            }

            if ($paymentMethod === 'client wallet' && $customer) {
                // Check customer wallet sufficiency
                if (($customer->balance - $amount) < 0) {
                    throw new \Exception('Insufficient balance in client wallet for this transaction. Available: ' . number_format($customer->balance, 2) . ' EGP, Required: ' . number_format($amount, 2) . ' EGP');
                }
                
                // Deduct from customer wallet
                $customer->balance -= $amount;
                $this->customerRepository->save($customer);
            }
        }

        // Apply Receive transaction effects
        elseif ($transactionType === 'Receive') {
            if ($line) {
                // Increase line balance and usage
                $this->lineRepository->update($line->id, [
                    'current_balance' => $line->current_balance + $amount,
                    'daily_usage' => ($line->daily_usage ?? 0) + $amount,
                    'monthly_usage' => ($line->monthly_usage ?? 0) + $amount,
                    'daily_remaining' => max(0, ($line->daily_remaining ?? 0) - $amount),
                    'monthly_remaining' => max(0, ($line->monthly_remaining ?? 0) - $amount),
                ]);
            }

            if ($safe) {
                // Check safe balance sufficiency
                $safeDeduction = $amount - $finalCommission;
                if (($safe->current_balance - $safeDeduction) < 0) {
                    throw new \Exception('Insufficient balance in safe for this receive transaction. Available: ' . number_format($safe->current_balance, 0) . ' EGP, Required: ' . number_format($safeDeduction, 0) . ' EGP');
                }
                
                // Deduct from safe balance: amount - (commission - deduction)
                $this->safeRepository->update($safe->id, [
                    'current_balance' => $safe->current_balance - $safeDeduction
                ]);
            }
        }

        // Apply Deposit transaction effects
        elseif ($transactionType === 'Deposit') {
            if ($line) {
                // Increase line balance
                $this->lineRepository->update($line->id, [
                    'current_balance' => $line->current_balance + $amount
                ]);
            }

            if ($paymentMethod === 'client wallet' && $customer) {
                // Increase customer wallet
                $customer->balance += $amount;
                $this->customerRepository->save($customer);
            }
        }

        // Send notifications for low balances
        $this->checkAndNotifyLowBalances($line, $safe);
    }

    /**
     * Find customer by mobile number
     */
    private function findCustomerByMobile(?string $mobileNumber): ?Customer
    {
        if (!$mobileNumber) {
            return null;
        }

        return Customer::where('mobile_number', $mobileNumber)->first();
    }

    /**
     * Check for low balances and send notifications
     */
    private function checkAndNotifyLowBalances($line, $safe): void
    {
        if ($line && $line->current_balance < 500) {
            $notificationMessage = "Warning: Line " . $line->mobile_number . " balance is low ( " . $line->current_balance . " EGP). Please top up.";
            $this->notifyRelevantUsers($notificationMessage, route('lines.edit', $line->id), $line->branch_id);
        }

        if ($safe && $safe->current_balance < 500) {
            $notificationMessage = "Warning: Safe " . $safe->name . " balance is low ( " . $safe->current_balance . " EGP) in branch " . $safe->branch->name . ". Please deposit.";
            $this->notifyRelevantUsers($notificationMessage, route('safes.edit', $safe->id), $safe->branch_id);
        }
    }

    /**
     * Notify relevant users about balance issues
     */
    private function notifyRelevantUsers(string $message, string $url, ?int $branchId = null): void
    {
        $admins = \App\Domain\Entities\User::role('admin')->get();
        $supervisors = \App\Domain\Entities\User::role('general_supervisor')->get();
        $recipients = $admins->merge($supervisors)->unique('id');

        if ($recipients->count() > 0) {
            Notification::send($recipients, new AdminNotification($message, $url));
        }
    }
} 