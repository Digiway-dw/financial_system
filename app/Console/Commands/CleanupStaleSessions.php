<?php

namespace App\Console\Commands;

use App\Application\Services\WorkSessionService;
use App\Domain\Entities\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanupStaleSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sessions:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up stale user sessions and work sessions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting cleanup of stale sessions...');
        
        // Get the session timeout in minutes (5 minutes)
        $sessionTimeout = 5;
        $cutoffTime = Carbon::now()->subMinutes($sessionTimeout);
        
        // 1. First, clean up Laravel sessions table
        try {
            $staleSessions = DB::table('sessions')
                ->where('last_activity', '<', $cutoffTime->timestamp)
                ->get();
            
            $this->info("Found {$staleSessions->count()} stale Laravel sessions");
            
            foreach ($staleSessions as $session) {
                // Extract user ID from session payload if possible
                $userId = null;
                try {
                    $payload = unserialize(base64_decode($session->payload));
                    if (isset($payload['auth']['user']['id'])) {
                        $userId = $payload['auth']['user']['id'];
                    }
                } catch (\Exception $e) {
                    Log::warning("Failed to unserialize session payload: " . $e->getMessage());
                }
                
                // Log which session is being deleted
                if ($userId) {
                    $this->info("Deleting session for user ID: {$userId}, last active: " . 
                        Carbon::createFromTimestamp($session->last_activity)->format('Y-m-d H:i:s'));
                    
                    // End work session for this user
                    $user = User::find($userId);
                    if ($user) {
                        app(WorkSessionService::class)->endSession($user);
                        $this->info("Ended work session for user ID: {$userId}");
                    }
                } else {
                    $this->info("Deleting anonymous session, last active: " . 
                        Carbon::createFromTimestamp($session->last_activity)->format('Y-m-d H:i:s'));
                }
                
                // Delete the session
                DB::table('sessions')->where('id', $session->id)->delete();
            }
            
            $this->info('Laravel session cleanup completed');
        } catch (\Exception $e) {
            $this->error('Error cleaning up Laravel sessions: ' . $e->getMessage());
            Log::error('Error cleaning up Laravel sessions: ' . $e->getMessage());
        }
        
        // 2. Clean up any orphaned work sessions (where logout_at is null but should be closed)
        try {
            $workSessionService = app(WorkSessionService::class);
            $openWorkSessions = \App\Models\Domain\Entities\WorkSession::whereNull('logout_at')
                ->where('login_at', '<', $cutoffTime)
                ->get();
                
            $this->info("Found {$openWorkSessions->count()} stale work sessions");
            
            foreach ($openWorkSessions as $workSession) {
                $this->info("Closing work session for user ID: {$workSession->user_id}, " . 
                    "started at: {$workSession->login_at}");
                
                $workSession->logout_at = now();
                $workSession->calculateDuration();
                $workSession->save();
            }
            
            $this->info('Work session cleanup completed');
        } catch (\Exception $e) {
            $this->error('Error cleaning up work sessions: ' . $e->getMessage());
            Log::error('Error cleaning up work sessions: ' . $e->getMessage());
        }
        
        $this->info('All cleanup tasks completed successfully');
        return 0;
    }
}
