<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class AdminNotificationsBox extends Component
{
    public $notifications = [];
    public $tab = 'unread';
    public $typeFilter = 'all';
    public $fromDate = null;
    public $toDate = null;

    public function getListeners()
    {
        return [
            'refreshNotifications' => '$refresh',
        ];
    }

    public function mount()
    {
        $this->tab = 'unread';
        $this->loadNotifications();
    }

    public function setTab($tab)
    {
        $this->tab = $tab;
        $this->loadNotifications();
    }

    public function setTypeFilter($type)
    {
        $this->typeFilter = $type;
        $this->loadNotifications();
    }

    public function setFromDate($date)
    {
        $this->fromDate = $date;
        $this->loadNotifications();
    }

    public function setToDate($date)
    {
        $this->toDate = $date;
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $user = Auth::user();
        if ($this->tab === 'all') {
            $query = $user->notifications()->orderBy('created_at', 'desc');
        } elseif ($this->tab === 'read') {
            $query = $user->readNotifications()->orderBy('created_at', 'desc');
        } else {
            $query = $user->unreadNotifications()->orderBy('created_at', 'desc');
        }

        // Type filter
        if ($this->typeFilter !== 'all') {
            $query = $query->where(function($q) {
                if ($this->typeFilter === 'send') {
                    $q->where('data->type', 'send')->orWhere('data->category', 'send');
                } elseif ($this->typeFilter === 'receive') {
                    $q->where('data->type', 'receive')->orWhere('data->category', 'receive');
                } elseif ($this->typeFilter === 'cash') {
                    $q->where('data->type', 'cash')->orWhere('data->category', 'cash');
                } elseif ($this->typeFilter === 'others') {
                    $q->where(function($subq) {
                        $subq->whereNotIn('data->type', ['send', 'receive', 'cash'])
                             ->whereNotIn('data->category', ['send', 'receive', 'cash']);
                    });
                }
            });
        }

        // Date filter
        if ($this->fromDate) {
            $query = $query->whereDate('created_at', '>=', $this->fromDate);
        }
        if ($this->toDate) {
            $query = $query->whereDate('created_at', '<=', $this->toDate);
        }

        $this->notifications = $query->limit(20)->get();
    }

    public function markAsRead($notificationId)
    {
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $notificationId)->first();
        if ($notification) {
            $notification->markAsRead();
        }
        $this->loadNotifications();
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();
        $this->loadNotifications();
    }

    public function approveNotification($notificationId)
    {
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $notificationId)->first();
        
        if (!$notification) {
            session()->flash('error', 'Notification not found.');
            return;
        }
        
        $type = $notification->data['type'] ?? null;
        $transactionId = $notification->data['transaction_id'] ?? null;
        
        // Debug logging
        \Illuminate\Support\Facades\Log::info('AdminNotificationsBox approveNotification', [
            'notification_id' => $notificationId,
            'type' => $type,
            'transaction_id' => $transactionId,
            'user_id' => $user->id,
            'user_roles' => $user->getRoleNames(),
        ]);
        
        if ($type === 'withdrawal' && $transactionId) {
            $cashTransaction = \App\Models\Domain\Entities\CashTransaction::find($transactionId);
            
            if (!$cashTransaction) {
                session()->flash('error', 'Cash transaction not found for ID: ' . $transactionId);
                return;
            }
            
            if ($cashTransaction->status !== 'pending') {
                session()->flash('error', 'Transaction is not pending. Current status: ' . $cashTransaction->status);
                return;
            }
            
            if ($cashTransaction->transaction_type !== 'Withdrawal') {
                session()->flash('error', 'Transaction is not a withdrawal. Type: ' . $cashTransaction->transaction_type);
                return;
            }
            
            $safe = \App\Models\Domain\Entities\Safe::find($cashTransaction->safe_id);
            $amount = abs($cashTransaction->amount);

            // Branch withdrawal logic
            if ($cashTransaction->destination_branch_id && $cashTransaction->destination_safe_id) {
                // Deduct from selected branch's safe (destination_safe_id)
                $sourceSafe = \App\Models\Domain\Entities\Safe::find($cashTransaction->destination_safe_id);
                if ($sourceSafe) {
                    if (($sourceSafe->current_balance - $amount) < 0) {
                        session()->flash('error', 'Insufficient balance in source branch safe. Available: ' . number_format($sourceSafe->current_balance, 2) . ' EGP, Required: ' . number_format($amount, 2) . ' EGP');
                        return;
                    }
                    $sourceSafe->current_balance -= $amount;
                    $sourceSafe->save();
                }
                // Add to agent's branch safe (safe_id)
                $destSafe = \App\Models\Domain\Entities\Safe::find($cashTransaction->safe_id);
                if ($destSafe) {
                    $destSafe->current_balance += $amount;
                    $destSafe->save();
                }
            } else {
                // Regular withdrawal logic
                // Check if this is a client wallet withdrawal (identified by the notes)
                if (str_contains($cashTransaction->notes, 'CLIENT_WALLET_WITHDRAWAL')) {
                    // For client wallet withdrawals, the customer balance was already deducted
                    // when the transaction was created, so no safe balance deduction needed
                    // Just update the status to completed
                } elseif ($cashTransaction->customer_code) {
                    // For non-client wallet withdrawals, deduct from client wallet if applicable
                    $client = \App\Models\Domain\Entities\Customer::where('customer_code', $cashTransaction->customer_code)->first();
                    if ($client) {
                        if (($client->balance - $amount) < 0) {
                            session()->flash('error', 'Insufficient client balance. Available: ' . number_format($client->balance, 2) . ' EGP, Required: ' . number_format($amount, 2) . ' EGP');
                            return;
                        }
                        $client->balance -= $amount;
                        $client->save();
                    }

                    // Also deduct from safe for regular customer withdrawals
                    if ($safe) {
                        if (($safe->current_balance - $amount) < 0) {
                            session()->flash('error', 'Insufficient safe balance. Available: ' . number_format($safe->current_balance, 2) . ' EGP, Required: ' . number_format($amount, 2) . ' EGP');
                            return;
                        }
                        $safe->current_balance -= $amount;
                        $safe->save();
                    }
                } elseif ($cashTransaction->destination_branch_id && !$cashTransaction->customer_code && str_contains($cashTransaction->customer_name, 'Expense:')) {
                    // Expense withdrawal
                    if ($safe) {
                        if (($safe->current_balance - $amount) < 0) {
                            session()->flash('error', 'Insufficient safe balance for expense withdrawal. Available: ' . number_format($safe->current_balance, 2) . ' EGP, Required: ' . number_format($amount, 2) . ' EGP');
                            return;
                        }
                        $safe->current_balance -= $amount;
                        $safe->save();
                    }
                } else {
                    // Deduct from safe for other types of withdrawals (direct, user, admin)
                    if ($safe) {
                        if (($safe->current_balance - $amount) < 0) {
                            session()->flash('error', 'Insufficient safe balance. Available: ' . number_format($safe->current_balance, 2) . ' EGP, Required: ' . number_format($amount, 2) . ' EGP');
                            return;
                        }
                        $safe->current_balance -= $amount;
                        $safe->save();
                    }
                }
            }
            // Update status to completed
            $cashTransaction->status = 'completed';
            $cashTransaction->save();
            session()->flash('message', 'Cash withdrawal approved and balances updated!');
            $notification->markAsRead();
            $this->loadNotifications();
            $this->dispatch('$refresh');
            return;
        }
        session()->flash('error', 'Failed to approve transaction: Invalid notification type or missing transaction ID.');
    }

    public function rejectNotification($notificationId)
    {
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $notificationId)->first();
        
        if (!$notification) {
            session()->flash('error', 'Notification not found.');
            return;
        }
        
        $type = $notification->data['type'] ?? null;
        $transactionId = $notification->data['transaction_id'] ?? null;
        
        // Debug logging
        \Illuminate\Support\Facades\Log::info('AdminNotificationsBox rejectNotification', [
            'notification_id' => $notificationId,
            'type' => $type,
            'transaction_id' => $transactionId,
            'user_id' => $user->id,
            'user_roles' => $user->getRoleNames(),
        ]);
        
        if ($type === 'withdrawal' && $transactionId) {
            $cashTransaction = \App\Models\Domain\Entities\CashTransaction::find($transactionId);
            if (!$cashTransaction) {
                session()->flash('error', 'Cash transaction not found for ID: ' . $transactionId);
                return;
            }
            
            $isAdminOrSupervisor = $user->hasRole('admin') || $user->hasRole('general_supervisor');
            if (!$isAdminOrSupervisor) {
                session()->flash('error', 'You can only reject pending cash withdrawals as admin or supervisor.');
                return;
            }
            
            if ($cashTransaction->status !== 'pending') {
                session()->flash('error', 'Transaction is not pending. Current status: ' . $cashTransaction->status);
                return;
            }
            
            if ($cashTransaction->transaction_type !== 'Withdrawal') {
                session()->flash('error', 'Transaction is not a withdrawal. Type: ' . $cashTransaction->transaction_type);
                return;
            }
            
            $cashTransaction->status = 'rejected';
            $cashTransaction->rejected_by = $user->id;
            $cashTransaction->rejected_at = now();
            $cashTransaction->rejection_reason = 'Rejected by ' . $user->name;
            $cashTransaction->save();
            session()->flash('message', 'Cash withdrawal rejected successfully.');
            $notification->markAsRead();
            $this->loadNotifications();
            $this->dispatch('$refresh');
            return;
        }
        session()->flash('error', 'Failed to reject transaction: Invalid notification type or missing transaction ID.');
    }

    public function viewNotification($notificationId)
    {
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $notificationId)->first();
        if ($notification) {
            $type = $notification->data['type'] ?? null;
            if ($type === 'withdrawal') {
                $notification->markAsRead();
                return redirect()->route('notifications.show', $notificationId);
            }
            $transactionId = $notification->data['transaction_id'] ?? null;
            if ($type === 'pending_transaction' && $transactionId) {
                $notification->markAsRead();
                return redirect()->route('transactions.waiting-approval', ['transactionId' => $transactionId]);
            }
        }
        if ($notification) {
            $notification->markAsRead();
        }
        $this->loadNotifications();
    }

    public function render()
    {
        $this->loadNotifications();
        return view('livewire.admin-notifications-box');
    }
}
