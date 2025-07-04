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

    public function render()
    {
        $this->loadNotifications();
        return view('livewire.admin-notifications-box');
    }
}
