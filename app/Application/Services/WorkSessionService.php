<?php

namespace App\Application\Services;

use App\Models\Domain\Entities\WorkSession;
use App\Domain\Entities\User;
use App\Exports\WorkSessionsExport;
use App\Models\SessionSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class WorkSessionService
{
    /**
     * Create a new work session when a user logs in.
     *
     * @param User $user
     * @param Request $request
     * @return WorkSession
     */
    public function startSession(User $user, Request $request): WorkSession
    {
        // First, check if there's an active session for this user and end it
        $this->endAnyActiveSessions($user);

        return WorkSession::create([
            'user_id' => $user->id,
            'login_at' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    /**
     * End all active sessions for a user.
     *
     * @param User $user
     * @return void
     */
    private function endAnyActiveSessions(User $user): void
    {
        $activeSessions = WorkSession::where('user_id', $user->id)
            ->whereNull('logout_at')
            ->get();

        foreach ($activeSessions as $session) {
            $session->logout_at = now();
            $session->calculateDuration();
            $session->save();
        }
    }

    /**
     * End a work session when a user logs out.
     *
     * @param User $user
     * @return WorkSession|null
     */
    public function endSession(User $user): ?WorkSession
    {
        $session = WorkSession::where('user_id', $user->id)
            ->whereNull('logout_at')
            ->latest('login_at')
            ->first();

        if ($session) {
            $session->logout_at = now();
            $session->calculateDuration();
            $session->save();

            return $session;
        }

        return null;
    }

    /**
     * Get active session for a user.
     *
     * @param User $user
     * @return WorkSession|null
     */
    public function getActiveSession(User $user): ?WorkSession
    {
        return WorkSession::where('user_id', $user->id)
            ->whereNull('logout_at')
            ->latest('login_at')
            ->first();
    }

    /**
     * Get all work sessions for a user.
     *
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserSessions(User $user)
    {
        return WorkSession::where('user_id', $user->id)
            ->orderBy('login_at', 'desc')
            ->get();
    }

    /**
     * Export work sessions to Excel.
     *
     * @param Collection $sessions
     * @return BinaryFileResponse
     */
    public function exportToExcel(Collection $sessions): BinaryFileResponse
    {
        return Excel::download(new WorkSessionsExport($sessions), 'work_sessions_' . now()->format('Y-m-d_H-i-s') . '.xlsx');
    }

    /**
     * Export work sessions to CSV.
     *
     * @param Collection $sessions
     * @return BinaryFileResponse
     */
    public function exportToCsv(Collection $sessions): BinaryFileResponse
    {
        return Excel::download(new WorkSessionsExport($sessions), 'work_sessions_' . now()->format('Y-m-d_H-i-s') . '.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    /**
     * Check for inactive sessions based on the configured session lifetime
     * and end them if they exceed the timeout.
     *
     * @return int Number of sessions that were ended
     */
    public function checkInactiveSessions(): int
    {
        $sessionLifetime = SessionSetting::getSessionLifetime();
        $inactiveThreshold = now()->subMinutes($sessionLifetime);

        $inactiveSessions = WorkSession::whereNull('logout_at')
            ->where('login_at', '<', $inactiveThreshold)
            ->get();

        $count = 0;
        foreach ($inactiveSessions as $session) {
            $session->logout_at = now();
            $session->calculateDuration();
            $session->save();
            $count++;
        }

        return $count;
    }

    /**
     * Get the current session lifetime setting
     *
     * @return int Minutes
     */
    public function getSessionLifetime(): int
    {
        return SessionSetting::getSessionLifetime();
    }

    /**
     * Update the session lifetime setting
     *
     * @param int $minutes
     * @return bool
     */
    public function updateSessionLifetime(int $minutes): bool
    {
        return SessionSetting::updateSessionLifetime($minutes);
    }
}
