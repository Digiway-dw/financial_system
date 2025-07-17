<?php

namespace App\Livewire\Notifications;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Details extends Component
{
    public $notificationId;
    public $notification;

    public function mount($notificationId)
    {
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $notificationId)->firstOrFail();
        if ($notification->read_at === null) {
            $notification->markAsRead();
        }
        $this->notification = $notification;
    }

    public function render()
    {
        return view('livewire.notifications.details', [
            'notification' => $this->notification,
        ]);
    }
} 