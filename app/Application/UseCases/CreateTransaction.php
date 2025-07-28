<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\LineRepository;
use App\Domain\Interfaces\SafeRepository;
use App\Domain\Interfaces\TransactionRepository;
use App\Domain\Interfaces\CustomerRepository;
use App\Models\Domain\Entities\Transaction;
use App\Notifications\AdminNotification;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

class CreateTransaction
{
    private TransactionRepository $transactionRepository;
    private LineRepository $lineRepository;
    private SafeRepository $safeRepository;
    private CustomerRepository $customerRepository;

    public function __construct(TransactionRepository $transactionRepository, LineRepository $lineRepository, SafeRepository $safeRepository, CustomerRepository $customerRepository)
    {
        $this->transactionRepository = $transactionRepository;
        $this->lineRepository = $lineRepository;
        $this->safeRepository = $safeRepository;
        $this->customerRepository = $customerRepository;
    }

    public function execute(
        string $customerName,
        string $customerMobileNumber,
        ?string $customerCode,
        float $amount,
        float $commission,
        float $deduction,
        string $transactionType,
        int $agentId,
        int $lineId,
        int $safeId,
        bool $isAbsoluteWithdrawal = false,
        string $paymentMethod = 'branch safe',
        string $gender = 'Male',
        bool $isClient = false,
        ?string $receiverMobileNumber = null,
        ?string $discountNotes = null,
        ?string $notes = null
    ): Transaction {
        // Check if branch is active before proceeding
        $line = $this->lineRepository->findById($lineId);
        if ($line && $line->branch_id) {
            \App\Helpers\BranchStatusHelper::validateBranchActive($line->branch_id);
        }

        // Validate amount: integer only, multiples of 5
        if (!is_int($amount) && !($amount == (int)$amount)) {
            throw new \InvalidArgumentException('Amount must be an integer.');
        }
        if ($amount % 5 !== 0) {
            throw new \InvalidArgumentException('Amount must be a multiple of 5.');
        }

        // Fetch the agent to check role for status setting and get branch info
        $agent = \App\Domain\Entities\User::find($agentId);
        if (!$agent) {
            throw new \Exception('Agent not found.');
        }

        // Check if customer exists, otherwise create a new one
        $customer = null;
        if (!empty($customerCode)) {
            $customer = $this->customerRepository->findByCustomerCode($customerCode);
        }

        if (!$customer && !empty($customerMobileNumber)) {
            $customer = $this->customerRepository->findByMobileNumber($customerMobileNumber);
        }

        // Only auto-generate customer code and set wallet inactive for Receive transactions
        if (!$customer) {
            // Set wallet status based on transaction type
            if ($transactionType === 'Receive') {
                $isClient = true; // Receive transactions should create customers with wallets
            } elseif ($transactionType === 'Send') {
                $isClient = false; // Send transactions create customers without wallets by default
            }
            // Auto-generate customer code for Receive (already handled above)
            $customer = new \App\Models\Domain\Entities\Customer([
                'name' => $customerName,
                'mobile_number' => $customerMobileNumber,
                'customer_code' => $customerCode, // Will be auto-generated for Receive
                'gender' => $gender,
                'is_client' => $isClient,
                'agent_id' => $agentId,
                'branch_id' => $agent->branch_id,
            ]);
            $customer = $this->customerRepository->save($customer);
        } else {
            // Update existing customer data if necessary (e.g., if name or code changed during transaction)
            $customer->name = $customerName;
            $customer->mobile_number = $customerMobileNumber; // Ensure consistency
            if (!empty($customerCode)) {
                $customer->customer_code = $customerCode;
            }
            $customer->gender = $gender;
            // Preserve existing wallet status for existing customers
            // NEVER override existing customer's wallet status - only set it for new customers
            // Existing customers should keep their current wallet status regardless of transaction type
            $this->customerRepository->save($customer);
        }

        // Fetch the agent to check role for status setting
        $agent = \App\Domain\Entities\User::find($agentId);
        if (!$agent) {
            throw new \Exception('Agent not found.');
        }

        // Use the commission passed from the component, do NOT apply deduction
        $finalCommission = $commission;
        if ($finalCommission < 0) {
            $finalCommission = 0; // Commission cannot be negative
        }

        // Determine transaction status based on agent role and if it's an absolute withdrawal
        // Absolute withdrawals by Admin, General Supervisor, or Branch Manager do not require approval and are 'Completed'
        if (($agent->hasRole('admin') || $agent->hasRole('general_supervisor') || $agent->hasRole('branch_manager')) && $isAbsoluteWithdrawal) {
            $status = 'Completed';
        } elseif ($agent->hasRole('general_supervisor')) {
            // General supervisors: all transactions are completed immediately (no approval needed)
            $status = 'Completed';
        } else {
            // For discount transactions, do not set to Pending, just notify
            if ($deduction > 0) {
                $status = 'Completed';
            } else {
                $status = 'Completed';
            }
        }

        // Additional check for withdrawals from Client Safes by non-admin/supervisor
        $safe = $this->safeRepository->findById($safeId);
        if (!$safe) {
            throw new \Exception('Safe not found.');
        }

        if ($transactionType === 'Withdrawal' && $safe->type === 'client') {
            if (!($agent->hasRole('admin') || $agent->hasRole('general_supervisor'))) {
                $status = 'Pending'; // Override status to Pending for client safe withdrawals needing approval
                $notificationMessage = "A withdrawal of " . $amount . " EGP from Client Safe '" . $safe->name . "' by " . $agent->name . " requires your approval.";
                // Pass transaction ID if available after creation
                $this->notifyRelevantUsers($notificationMessage, route('transactions.edit', $createdTransaction->id ?? null), $safe->branch_id);
            }
        }

        // Fetch the line to check limits and balance
        $line = $this->lineRepository->findById($lineId);

        if (!$line) {
            throw new \Exception('Line not found.');
        }

        // Prevent Receive transaction if amount exceeds daily or monthly remaining
        if ($transactionType === 'Receive') {
            if ($amount > ($line->daily_remaining ?? 0)) {
                throw new \Exception('Transaction amount exceeds daily remaining for this line.');
            }
            if ($amount > ($line->monthly_remaining ?? 0)) {
                throw new \Exception('Transaction amount exceeds monthly remaining for this line.');
            }
        }

        // --- Monthly Starting Balance Logic ---
        $now = now();
        $currentMonth = $now->format('Y-m');
        $lastSetMonth = $line->updated_at ? $line->updated_at->format('Y-m') : null;
        if ($lastSetMonth !== $currentMonth) {
            // Set starting_balance to the current balance at the start of the month
            $this->lineRepository->update($lineId, [
                'starting_balance' => $line->current_balance,
                'monthly_usage' => 0,
                'monthly_remaining' => ($line->monthly_limit ?? 0) - $line->current_balance,
            ]);
            $line->refresh();
        }
        // --- End Monthly Starting Balance Logic ---

        // --- Daily Starting Balance Logic ---
        $today = now()->format('Y-m-d');
        $lastSetDay = $line->updated_at ? $line->updated_at->format('Y-m-d') : null;
        if ($lastSetDay !== $today) {
            // Set daily_starting_balance to the current balance at the start of the day
            $this->lineRepository->update($lineId, [
                'daily_starting_balance' => $line->current_balance,
                'daily_usage' => 0,
                'daily_remaining' => ($line->daily_limit ?? 0) - $line->current_balance,
            ]);
            $line->refresh();
        }
        // --- End Daily Starting Balance Logic ---

        // Check daily limit
        // This would ideally involve checking aggregated daily transactions for this line.
        // For simplicity, let's assume the daily limit is checked against the current transaction amount directly.
        if ($amount > $line->daily_limit) {
            throw new \Exception('Transaction amount exceeds daily limit for this line.');
        }

        // Check monthly limit
        // Similar to daily limit, this would require aggregation.
        if ($amount > $line->monthly_limit) { // This is a simplistic check
            // Notify admin if monthly threshold is crossed
            $admins = \App\Domain\Entities\User::role('admin')->get();
            $message = "Monthly limit of line " . $line->mobile_number . " has been crossed.";
            Notification::send($admins, new AdminNotification($message, route('lines.edit', $line->id)));
            throw new \Exception('Transaction amount exceeds monthly limit for this line.');
        }

        // --- New Daily/Monthly Receive Limit Logic ---
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();
        $todayStart = Carbon::today();
        $todayEnd = $todayStart->copy()->endOfDay();

        // Monthly Receive Limit: (monthly_receive_limit - starting_balance)
        if (in_array($transactionType, ['Deposit', 'Receive'])) {
            $monthlyReceived = $this->transactionRepository->getTotalReceivedForLine($lineId, $monthStart, $monthEnd);
            $monthlyLimit = $line->monthly_limit;
            $startingBalance = $line->starting_balance ?? 0;
            $maxAllowedMonthly = ($monthlyLimit !== null) ? ($monthlyLimit - $startingBalance) : null;
            if ($maxAllowedMonthly !== null && ($monthlyReceived + $amount) > $maxAllowedMonthly) {
                // Freeze the line for the rest of the month
                $this->lineRepository->update($lineId, ['status' => 'frozen']);
                throw new \Exception('Transaction exceeds the allowed monthly receive limit for this line. The line has been frozen until the start of next month.');
            }
        }

        // Daily Receive Limit: (daily_limit - daily_starting_balance)
        if (in_array($transactionType, ['Deposit', 'Receive'])) {
            $dailyReceived = $this->transactionRepository->getTotalReceivedForLine($lineId, $todayStart, $todayEnd);
            $dailyLimit = $line->daily_limit;
            $dailyStartingBalance = $line->daily_starting_balance ?? 0;
            $maxAllowedDaily = ($dailyLimit !== null) ? ($dailyLimit - $dailyStartingBalance) : null;
            if ($maxAllowedDaily !== null && ($dailyReceived + $amount) > $maxAllowedDaily) {
                // Freeze the line
                $this->lineRepository->update($lineId, ['status' => 'frozen']);
                throw new \Exception('Transaction exceeds the allowed daily receive limit for this line. The line has been frozen until the end of the day.');
            }
        }
        // --- End New Limit Logic ---

        // Only apply balance changes if status is not 'Pending'
        $shouldApplyBalances = !in_array($status, ['Pending', 'pending']);

        // Check current balance for transfer type only
        if ($shouldApplyBalances && $transactionType === 'Transfer') {
            if ($paymentMethod === 'client wallet') {
                // Decrease client balance by (amount + final commission - deduction)
                $clientDeduction = $amount + $finalCommission - $deduction;
                if (($customer->balance - $clientDeduction) < 0) {
                    throw new \Exception('Insufficient balance in client wallet for this transaction. Available: ' . number_format($customer->balance, 2) . ' EGP, Required: ' . number_format($clientDeduction, 2) . ' EGP');
                }
                $customer->balance -= $clientDeduction;
                $this->customerRepository->save($customer);
                // Refresh customer object to ensure we have the latest data
                $customer = $this->customerRepository->findByMobileNumber($customer->mobile_number);
                // Decrease line balance by amount
                if (($line->current_balance - $amount) < 0) {
                    throw new \Exception('Insufficient balance in line for this transaction. Available: ' . number_format($line->current_balance, 2) . ' EGP, Required: ' . number_format($amount, 2) . ' EGP');
                }
                $this->lineRepository->update($lineId, ['current_balance' => $line->current_balance - $amount]);
                $line->refresh();
                if ($line->current_balance < 500) {
                    $notificationMessage = "Warning: Line " . $line->mobile_number . " balance is low ( " . $line->current_balance . " EGP). Please top up.";
                    $this->notifyRelevantUsers($notificationMessage, route('lines.edit', $line->id), $line->branch_id);
                }
                // Do NOT change safe balance
            } else {
                // Default: Deduct from line, increase safe
                if (($line->current_balance - $amount) < 0) {
                    throw new \Exception('Insufficient balance in line for this transaction. Available: ' . number_format($line->current_balance, 2) . ' EGP, Required: ' . number_format($amount, 2) . ' EGP');
                }
                $this->lineRepository->update($lineId, ['current_balance' => $line->current_balance - $amount]);
                $line->refresh();
                if ($line->current_balance < 500) {
                    $notificationMessage = "Warning: Line " . $line->mobile_number . " balance is low ( " . $line->current_balance . " EGP). Please top up.";
                    $this->notifyRelevantUsers($notificationMessage, route('lines.edit', $line->id), $line->branch_id);
                }
                // For Transfer (Send) transactions, safe balance increases by (Amount + Commission)
                $safe = $this->safeRepository->findById($safeId);
                if (!$safe) {
                    throw new \Exception('Safe not found.');
                }
                // Update: safeIncrease should be amount + (finalCommission - deduction)
                $safeIncrease = $amount + ($finalCommission - $deduction);
                $this->safeRepository->update($safeId, ['current_balance' => $safe->current_balance + $safeIncrease]);
                $safe->refresh();
            }
        }

        if ($shouldApplyBalances && $transactionType === 'Withdrawal' && !$isAbsoluteWithdrawal && $paymentMethod === 'branch safe') {
            $safe = $this->safeRepository->findById($safeId);
            if (!$safe) {
                throw new \Exception('Safe not found.');
            }
            if (($safe->current_balance - $amount) < 0) {
                throw new \Exception('Insufficient balance in safe for this transaction. Available: ' . number_format($safe->current_balance, 2) . ' EGP, Required: ' . number_format($amount, 2) . ' EGP');
            }
            $this->safeRepository->update($safeId, ['current_balance' => $safe->current_balance - $amount]);
            $safe->refresh();
            if ($safe->current_balance < 500) {
                $notificationMessage = "Warning: Safe " . $safe->name . " balance is low ( " . $safe->current_balance . " EGP) in branch " . $safe->branch->name . ". Please deposit.";
                $this->notifyRelevantUsers($notificationMessage, route('safes.edit', $safe->id), $safe->branch_id);
            }
        }

        if ($shouldApplyBalances && $paymentMethod === 'client wallet') {
            if ($transactionType === 'Withdrawal') {
                if (($customer->balance - $amount) < 0) {
                    throw new \Exception('Insufficient balance in client wallet for this transaction. Available: ' . number_format($customer->balance, 2) . ' EGP, Required: ' . number_format($amount, 2) . ' EGP');
                }
                $customer->balance -= $amount;
                $this->customerRepository->save($customer);
            } elseif ($transactionType === 'Deposit' || $transactionType === 'Receive') {
                $customer->balance += $amount;
                $this->customerRepository->save($customer);
            }
            // Note: Transfer transactions are handled in the Transfer-specific logic above
            $customer->refresh();
        }

        // Only update usage and remaining for Receive transactions
        if ($shouldApplyBalances && $transactionType === 'Receive') {
            $this->lineRepository->update($lineId, [
                'current_balance' => $line->current_balance + $amount,
                'daily_usage' => ($line->daily_usage ?? 0) + $amount,
                'monthly_usage' => ($line->monthly_usage ?? 0) + $amount,
                'daily_remaining' => ($line->daily_remaining ?? (($line->daily_limit ?? 0) - ($line->current_balance ?? 0))) - $amount,
                'monthly_remaining' => ($line->monthly_remaining ?? $line->monthly_limit) - $amount,
            ]);
            $line->refresh();

            // For Receive transactions, safe balance decreases by (Amount - (Commission - Discount))
            $safeDeduction = $amount - ($finalCommission - $deduction);
            if (($safe->current_balance - $safeDeduction) < 0) {
                throw new \Exception('Insufficient balance in safe for this receive transaction. Available: ' . number_format($safe->current_balance, 0) . ' EGP, Required: ' . number_format($safeDeduction, 0) . ' EGP');
            }
            $this->safeRepository->update($safeId, ['current_balance' => $safe->current_balance - $safeDeduction]);
            $safe->refresh();

            // Check for low safe balance warning
            if ($safe->current_balance < 500) {
                $notificationMessage = "Warning: Safe " . $safe->name . " balance is low ( " . $safe->current_balance . " EGP) in branch " . $safe->branch->name . ". Please deposit.";
                $admins = \App\Domain\Entities\User::role('admin')->get();
                Notification::send($admins, new AdminNotification($notificationMessage, route('safes.index')));
            }
        }

        // Generate reference number using branch name and unique number
        $branchName = $line->branch->name ?? 'Unknown';
        $referenceNumber = generate_reference_number($branchName);

        $attributes = [
            'customer_name' => $customerName,
            'customer_mobile_number' => $customerMobileNumber,
            'receiver_mobile_number' => $receiverMobileNumber,
            'customer_code' => $customerCode,
            'amount' => $amount,
            'commission' => $finalCommission,
            'deduction' => $deduction,
            'discount_notes' => $discountNotes,
            'notes' => $notes,
            'transaction_type' => $transactionType,
            'agent_id' => $agentId,
            'status' => $status,
            'transaction_date_time' => now(),
            'line_id' => $lineId,
            'safe_id' => $safeId,
            'is_absolute_withdrawal' => $isAbsoluteWithdrawal,
            'payment_method' => $paymentMethod,
            'reference_number' => $referenceNumber,
            'branch_id' => $line->branch_id,
        ];

        $createdTransaction = $this->transactionRepository->create($attributes);

        // Send notifications to Admin and Supervisor for deductions
        $admins = \App\Domain\Entities\User::role('admin')->get();
        $supervisors = \App\Domain\Entities\User::role('general_supervisor')->get();
        // if ($deduction > 0) {
        //     $message = "A new transaction with a deduction of " . $deduction . " EGP has been created by " . $agent->name . ".";
        //     \Notification::send($admins, new \App\Notifications\AdminNotification($message, route('transactions.edit', $createdTransaction->id, false)));
        //     \Notification::send($supervisors, new \App\Notifications\AdminNotification($message, route('transactions.edit', $createdTransaction->id, false)));
        // }

        return $createdTransaction;
    }

    private function generateUniqueReferenceNumber($agent): string
    {
        // Get the branch code from the agent's branch
        $branchCode = $agent->branch ? $agent->branch->branch_code : 'DEFAULT';

        // Generate date part (YYYYMMDD)
        $datePart = date('Ymd');

        // Find the highest existing sequence number for today and this branch
        $pattern = $branchCode . '-' . $datePart . '-%';
        $lastTransaction = \App\Models\Domain\Entities\Transaction::where('reference_number', 'like', $pattern)
            ->orderBy('reference_number', 'desc')
            ->first();

        $nextSequence = 1;
        if ($lastTransaction) {
            // Extract the sequence number from the last reference number
            $lastSequence = intval(substr($lastTransaction->reference_number, -6));
            $nextSequence = $lastSequence + 1;
        }

        // Generate the reference number: BRANCHCODE-YYYYMMDD-XXXXXX
        $referenceNumber = $branchCode . '-' . $datePart . '-' . str_pad($nextSequence, 6, '0', STR_PAD_LEFT);

        // Double-check uniqueness (in case of race conditions)
        $attempts = 0;
        while (\App\Models\Domain\Entities\Transaction::where('reference_number', $referenceNumber)->exists() && $attempts < 10) {
            $nextSequence++;
            $referenceNumber = $branchCode . '-' . $datePart . '-' . str_pad($nextSequence, 6, '0', STR_PAD_LEFT);
            $attempts++;
        }

        if ($attempts >= 10) {
            throw new \Exception('Unable to generate unique reference number after 10 attempts');
        }

        return $referenceNumber;
    }

    private function notifyRelevantUsers(string $message, string $url, ?int $branchId = null): void
    {
        // Get relevant users (admins for now, can be expanded to branch managers/supervisors)
        $recipients = \App\Domain\Entities\User::role('admin')
            ->when($branchId, function ($query) use ($branchId) {
                return $query->orWhere('branch_id', $branchId);
            })
            ->get();

        // Get general supervisors
        $generalSupervisors = \App\Domain\Entities\User::role('general_supervisor')->get();

        // Merge collections
        $recipients = $recipients->merge($generalSupervisors)->unique();

        if ($recipients->isNotEmpty()) {
            Notification::send($recipients, new AdminNotification($message, $url));
        }
    }
}
