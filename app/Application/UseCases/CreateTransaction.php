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

        // Validate amount: must be a positive number
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be a positive number.');
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
                $this->notifyRelevantUsers($notificationMessage, route('transactions.edit', $createdTransaction->reference_number ?? null), $safe->branch_id);
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
                throw new \Exception('لا يمكن ان يتم انشاء المعاملة بمبلغ اكبر من المتاح للخط اليومي');
            }
            if ($amount > ($line->monthly_remaining ?? 0)) {
                throw new \Exception('لا يمكن ان يتم انشاء المعاملة بمبلغ اكبر من المتاح للخط الشهري');
            }
        }

        // --- Monthly Starting Balance Logic (Only for Receive transactions) ---
        if (in_array($transactionType, ['Receive', 'Deposit'])) {
            $today = now()->toDateString();
            $currentMonth = now()->format('Y-m');
            
            // Check if we need to reset monthly usage (new month or first time)
            $shouldResetMonthly = false;
            if ($line->last_monthly_reset === null) {
                $shouldResetMonthly = true;
            } else {
                $lastResetMonth = \Carbon\Carbon::parse($line->last_monthly_reset)->format('Y-m');
                if ($lastResetMonth !== $currentMonth) {
                    $shouldResetMonthly = true;
                }
            }
            
            if ($shouldResetMonthly) {
                $this->lineRepository->update($lineId, [
                    'starting_balance' => $line->current_balance,
                    'monthly_usage' => 0,
                    'monthly_remaining' => ($line->monthly_limit ?? 0) - $line->current_balance,
                    'last_monthly_reset' => $today,
                ]);
                $line->refresh();
            }
        }
        // --- End Monthly Starting Balance Logic ---

        // --- Daily Starting Balance Logic (Only for Receive transactions) ---
        if (in_array($transactionType, ['Receive', 'Deposit'])) {
            $today = now()->toDateString();
            
            // Check if we need to reset daily usage (new day or first time)
            $shouldResetDaily = false;
            if ($line->last_daily_reset === null) {
                $shouldResetDaily = true;
            } else {
                if ($line->last_daily_reset !== $today) {
                    $shouldResetDaily = true;
                }
            }
            
            if ($shouldResetDaily) {
                $this->lineRepository->update($lineId, [
                    'daily_starting_balance' => $line->current_balance,
                    'daily_usage' => 0,
                    'daily_remaining' => ($line->daily_limit ?? 0) - $line->current_balance,
                    'last_daily_reset' => $today,
                ]);
                $line->refresh();
            }
        }
        // --- End Daily Starting Balance Logic ---

        // Check daily limit
        // This would ideally involve checking aggregated daily transactions for this line.
        // For simplicity, let's assume the daily limit is checked against the current transaction amount directly.
        if ($amount > $line->daily_limit) {
            throw new \Exception('مبلغ المعاملة يتجاوز الحد اليومي للخط.');
        }

        // Check monthly limit
        // Similar to daily limit, this would require aggregation.
        if ($amount > $line->monthly_limit) { // This is a simplistic check
            // Notify admin if monthly threshold is crossed
            $admins = \App\Domain\Entities\User::role('admin')->get();
            $message = "Monthly limit of line " . $line->mobile_number . " has been crossed.";
            Notification::send($admins, new AdminNotification($message, route('lines.edit', $line->id)));
            throw new \Exception('مبلغ المعاملة يتجاوز الحد الشهري لهذا الخط.');
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
                // Check if customer has an active wallet (is_client = true)
                if (!$customer->is_client) {
                    throw new \Exception('لا يمكن خصم المبلغ من محفظة العميل. العميل ليس لديه محفظة نشطة.');
                }
                
                // Check if customer can send this amount considering debt limits
                $clientDeduction = $amount + $finalCommission - $deduction;
                if (!$customer->canSendAmount($clientDeduction)) {
                    $availableLimit = $customer->getAvailableSendingLimit();
                    throw new \Exception('المبلغ يتجاوز الحد المسموح. الحد المتاح: ' . number_format($availableLimit, 2) . ' جنيه.');
                }
                
                // Decrease client balance by (amount + final commission - deduction)
                $customer->balance -= $clientDeduction;
                $this->customerRepository->save($customer);
                // Refresh customer object to ensure we have the latest data
                $customer = $this->customerRepository->findByMobileNumber($customer->mobile_number);
                // Decrease line balance by amount + 1 EGP fee for send transactions
                $lineDeduction = $amount;
                if ($transactionType === 'Transfer') {
                    $lineDeduction += 1; // Add 1 EGP fee for send transactions
                }
                if (($line->current_balance - $lineDeduction) < 0) {
                    throw new \Exception('Insufficient balance in line for this transaction. Available: ' . number_format($line->current_balance, 2) . ' EGP, Required: ' . number_format($lineDeduction, 2) . ' EGP');
                }
                $this->lineRepository->update($lineId, ['current_balance' => $line->current_balance - $lineDeduction]);
                $line->refresh();
                if ($line->current_balance < 500) {
                    $notificationMessage = "Warning: Line " . $line->mobile_number . " balance is low ( " . $line->current_balance . " EGP). Please top up.";
                    $this->notifyRelevantUsers($notificationMessage, route('lines.edit', $line->id), $line->branch_id);
                }
                // Do NOT change safe balance
            } else {
                // Default: Deduct from line, increase safe
                $lineDeduction = $amount;
                if ($transactionType === 'Transfer') {
                    $lineDeduction += 1; // Add 1 EGP fee for send transactions
                }
                if (($line->current_balance - $lineDeduction) < 0) {
                    throw new \Exception('لا يوجد رصيد كافٍ في الخط لهذه المعاملة. المتاح: ' . number_format($line->current_balance, 2) . ' EGP, المطلوب: ' . number_format($lineDeduction, 2) . ' EGP');
                }
                $this->lineRepository->update($lineId, ['current_balance' => $line->current_balance - $lineDeduction]);
                $line->refresh();
                if ($line->current_balance < 500) {
                    $notificationMessage = "تحذير: " . $line->mobile_number . " رصيده منخفض ( " . $line->current_balance . " EGP). يرجى الإيداع.";
                    $this->notifyRelevantUsers($notificationMessage, route('lines.edit', $line->id), $line->branch_id);
                }
                // For Transfer (Send) transactions, safe balance increases by (Amount + Commission)
                $safe = $this->safeRepository->findById($safeId);
                if (!$safe) {
                    throw new \Exception('لم يتم العثور على الخزنة.');
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
                throw new \Exception('لم يتم العثور على الخزنة.');
            }
            if (($safe->current_balance - $amount) < 0) {
                throw new \Exception('لا يوجد رصيد كافٍ في الخزنة لهذه المعاملة. المتاح: ' . number_format($safe->current_balance, 2) . ' EGP, المطلوب: ' . number_format($amount, 2) . ' EGP');
            }
            $this->safeRepository->update($safeId, ['current_balance' => $safe->current_balance - $amount]);
            $safe->refresh();
            if ($safe->current_balance < 500) {
                $notificationMessage = "تحذير: " . $safe->name . " رصيدها منخفض ( " . $safe->current_balance . " EGP) في الفرع " . $safe->branch->name . ". يرجى الإيداع.";
                $this->notifyRelevantUsers($notificationMessage, route('safes.edit', $safe->id), $safe->branch_id);
            }
        }

        if ($shouldApplyBalances && $paymentMethod === 'client wallet') {
            // Check if customer has an active wallet (is_client = true)
            if (!$customer->is_client) {
                throw new \Exception('لا يمكن خصم المبلغ من محفظة العميل. العميل ليس لديه محفظة نشطة.');
            }
            
            if ($transactionType === 'Withdrawal') {
                // Check if customer can send this amount considering debt limits
                if (!$customer->canSendAmount($amount)) {
                    $availableLimit = $customer->getAvailableSendingLimit();
                    throw new \Exception('المبلغ يتجاوز الحد المسموح. الحد المتاح: ' . number_format($availableLimit, 2) . ' جنيه.');
                }
                $customer->balance -= $amount;
                $this->customerRepository->save($customer);
            } elseif ($transactionType === 'Deposit' || $transactionType === 'Receive') {
                // For deposit/receive transactions, reduce debt first, then add to balance
                if ($customer->allow_debt && $customer->balance < 0) {
                    // Customer has debt - reduce debt first
                    $debtAmount = abs($customer->balance);
                    $debtReduction = min($amount, $debtAmount);
                    $remainingAmount = $amount - $debtReduction;
                    
                    // Apply debt reduction and any remaining amount as positive balance
                    $customer->balance = -($debtAmount - $debtReduction) + $remainingAmount;
                } else {
                    // No debt or debt mode disabled - add full amount to balance
                    $customer->balance += $amount;
                }
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
                'monthly_remaining' => ($line->monthly_remaining ?? (($line->monthly_limit ?? 0) - ($line->current_balance ?? 0))) - $amount,
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
        //     \Notification::send($admins, new \App\Notifications\AdminNotification($message, route('transactions.edit', $createdTransaction->reference_number, false)));
        //     \Notification::send($supervisors, new \App\Notifications\AdminNotification($message, route('transactions.edit', $createdTransaction->reference_number, false)));
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

    /**
     * Execute a line transfer transaction
     */
    public function executeLineTransfer(
        int $fromLineId,
        int $toLineId,
        float $amount,
        float $discount,
        int $agentId,
        string $status = 'pending',
        ?string $notes = null,
        ?string $referenceNumber = null
    ): Transaction {
        // Validate inputs
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be a positive number.');
        }
        
        if ($fromLineId === $toLineId) {
            throw new \InvalidArgumentException('Cannot transfer from a line to itself.');
        }

        // Get the lines
        $fromLine = $this->lineRepository->findById($fromLineId);
        $toLine = $this->lineRepository->findById($toLineId);
        
        if (!$fromLine || !$toLine) {
            throw new \Exception('One or both lines not found.');
        }

            // Calculate commission: 1 EGP for each 100 EGP (or part thereof)
            $amountInt = (int) ceil($amount); // Always treat as integer
            $baseFee = (int) ceil($amountInt / 100);
            $discountInt = (int) $discount;
            $finalCommission = max(0, $baseFee - $discountInt); // Commission can't be negative
            $totalDeducted = $amountInt + $finalCommission;

        // Check from line balance
        if ($fromLine->current_balance < $totalDeducted) {
            throw new \Exception(
                'Insufficient balance in source line. Available: ' . 
                number_format($fromLine->current_balance, 2) . 
                ' EGP, Required: ' . number_format($totalDeducted, 2) . ' EGP'
            );
        }

        // Get agent info
        $agent = \App\Domain\Entities\User::find($agentId);
        if (!$agent) {
            throw new \Exception('Agent not found.');
        }

        // Generate reference number if not provided
        if (!$referenceNumber) {
            $branchName = $agent->branch ? $agent->branch->name : 'Unknown';
            $referenceNumber = $this->generateReferenceNumber($branchName);
        }

        // Create the transaction
        $transaction = new Transaction([
            'transaction_type' => 'line_transfer',
            'from_line_id' => $fromLineId,
            'to_line_id' => $toLineId,
                'amount' => $amountInt,
                'commission' => $finalCommission,
                'extra_fee' => $discountInt, // Store discount as extra_fee for record keeping
                'total_deducted' => $totalDeducted,
            'status' => $status,
            'agent_id' => $agentId,
            'transaction_date_time' => now(),
            'reference_number' => $referenceNumber,
            'notes' => $notes,
            'customer_name' => 'Line Transfer: ' . $fromLine->mobile_number . ' → ' . $toLine->mobile_number,
            'payment_method' => 'line_balance',
            'branch_id' => $fromLine->branch_id,
        ]);

        $savedTransaction = $this->transactionRepository->save($transaction);

        // If status is completed, apply balance changes immediately
        if ($status === 'completed') {
            $this->applyLineTransferBalances($fromLine, $toLine, $totalDeducted, $amount);
        }

        return $savedTransaction;
    }

    /**
     * Apply balance changes for a line transfer
     */
    private function applyLineTransferBalances($fromLine, $toLine, float $totalDeducted, float $amount): void
    {
        // Deduct from source line
        $this->lineRepository->update($fromLine->id, [
            'current_balance' => $fromLine->current_balance - $totalDeducted
        ]);

        // Add to destination line
        $this->lineRepository->update($toLine->id, [
            'current_balance' => $toLine->current_balance + $amount
        ]);

        // NO notifications for line transfers - only low balance warnings are disabled for line transfers
    }
}
