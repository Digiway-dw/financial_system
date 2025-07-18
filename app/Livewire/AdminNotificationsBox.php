<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class AdminNotificationsBox extends Component
{
    public $notifications = [];
    public $tab = 'unread';

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

    public function loadNotifications()
    {
        $user = Auth::user();
        if ($this->tab === 'all') {
            $this->notifications = $user->notifications()->orderBy('created_at', 'desc')->limit(20)->get();
        } elseif ($this->tab === 'read') {
            $this->notifications = $user->readNotifications()->orderBy('created_at', 'desc')->limit(20)->get();
        } else {
            $this->notifications = $user->unreadNotifications()->orderBy('created_at', 'desc')->limit(20)->get();
        }
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
        if ($notification) {
            $type = $notification->data['type'] ?? null;
            $transactionId = $notification->data['transaction_id'] ?? null;
            if ($type === 'withdrawal' && $transactionId) {
                // Approve withdrawal: set status to completed
                $cashTx = \App\Models\Domain\Entities\CashTransaction::find($transactionId);
                if ($cashTx) {
                    $cashTx->status = 'Completed';
                    $cashTx->save();
                    $notification->markAsRead();
                    session()->flash('message', 'Withdrawal approved and notification marked as read.');
                }
            } elseif ($type === 'pending_transaction' && $transactionId) {
                try {
                    app(\App\Application\UseCases\ApproveTransaction::class)->execute($transactionId, $user->id);
                    $notification->markAsRead();
                    session()->flash('message', 'Transaction approved and notification marked as read.');
                } catch (\Exception $e) {
                    session()->flash('error', 'Failed to approve transaction: ' . $e->getMessage());
                }
            }
        }
        $this->loadNotifications();
    }

    public function rejectNotification($notificationId)
    {
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $notificationId)->first();
        if ($notification) {
            $type = $notification->data['type'] ?? null;
            $transactionId = $notification->data['transaction_id'] ?? null;
            if ($type === 'withdrawal' && $transactionId) {
                // Reject withdrawal: set status to rejected
                $cashTx = \App\Models\Domain\Entities\CashTransaction::find($transactionId);
                if ($cashTx) {
                    $cashTx->status = 'Rejected';
                    $cashTx->save();
                    $notification->markAsRead();
                    session()->flash('message', 'Withdrawal rejected and notification marked as read.');
                }
            } elseif ($type === 'pending_transaction' && $transactionId) {
                try {
                    app(\App\Application\UseCases\RejectTransaction::class)->execute($transactionId, $user->id, 'Rejected from notification');
                    $notification->markAsRead();
                    session()->flash('message', 'Transaction rejected and notification marked as read.');
                } catch (\Exception $e) {
                    session()->flash('error', 'Failed to reject transaction: ' . $e->getMessage());
                }
            }
        }
        $this->loadNotifications();
    }

    public function render()
    {
        $this->loadNotifications();
        return view('livewire.admin-notifications-box');
    }
}
