<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class NotificationBell extends Component
{
    public $unreadCount = 0;

    public function mount()
    {
        $this->loadCount();
    }

    public function loadCount()
    {
        $user = Auth::user();
        $this->unreadCount = $user->unreadNotifications()->count();
    }

    public function render()
    {
        $this->loadCount();
        return view('livewire.notification-bell');
    }
}
