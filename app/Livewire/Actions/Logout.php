<?php

namespace App\Livewire\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Logout
{
    /**
     * Log the current user out of the application.
     */
    public function __invoke(): void
    {
        $user = Auth::user();
        $loginHistoryId = session('login_history_id');
        if ($user && $loginHistoryId) {
            $loginHistory = \App\Models\Domain\Entities\LoginHistory::find($loginHistoryId);
            if ($loginHistory && !$loginHistory->logout_at) {
                $logoutAt = now();
                $loginAt = $loginHistory->login_at;
                $duration = $logoutAt->diffInSeconds($loginAt);
                $loginHistory->update([
                    'logout_at' => $logoutAt,
                    'session_duration' => $duration,
                ]);
            }
            session()->forget('login_history_id');
        }
        Auth::guard('web')->logout();

        Session::invalidate();
        Session::regenerateToken();
    }
}
