<?php

namespace App\Livewire\Forms;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Models\WorkingHour;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AdminNotification;

class LoginForm extends Form
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    #[Validate('boolean')]
    public bool $remember = false;

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only(['email', 'password']), $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'form.email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());

        // Get the authenticated user
        $user = Auth::user();
        
        // Check branch status for users with assigned branches
        if ($user->hasRole(['agent', 'branch_manager', 'trainee']) && $user->branch_id) {
            $this->validateBranchStatus($user);
        }

        // Check working hours for non-admin users
        if (! $user->hasRole('admin')) {
            $this->validateWorkingHours($user);
        }

        // Record login history (except admin)
        if (! $user->hasRole('admin')) {
            $loginHistory = \App\Models\Domain\Entities\LoginHistory::create([
                'user_id' => $user->id,
                'login_at' => now(),
            ]);
            session(['login_history_id' => $loginHistory->id]);
        }
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'form.email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }

    /**
     * Validate if user is within their working hours
     *
     * @param \App\Domain\Entities\User $user
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateWorkingHours($user): void
    {
        // Bypass working hours check if user can work any time
        if ($user->ignore_work_hours) {
            return;
        }

        // Get current server time
        $now = Carbon::now();
        $currentDayOfWeek = strtolower($now->englishDayOfWeek);
        $currentTime = $now->format('H:i:s');

        // Check if user has working hours defined for current day
        $workingHour = WorkingHour::where('user_id', $user->id)
            ->where('day_of_week', $currentDayOfWeek)
            ->where('is_enabled', true)
            ->first();

        // If no working hours defined for today or user is outside working hours
        $isOutsideWorkingHours = false;
        
        if (!$workingHour) {
            $isOutsideWorkingHours = true;
        } else {
            // Parse times properly using Carbon for accurate comparison with error handling
            try {
                // Try different time formats to handle various database formats
                $currentTimeCarbon = $this->parseTimeString($currentTime);
                $startTimeCarbon = $this->parseTimeString($workingHour->start_time);
                $endTimeCarbon = $this->parseTimeString($workingHour->end_time);
                
                if (!$currentTimeCarbon || !$startTimeCarbon || !$endTimeCarbon) {
                    throw new \Exception('Failed to parse one or more time values');
                }
            } catch (\Exception $e) {
                Log::error('Time parsing error in working hours validation', [
                    'error' => $e->getMessage(),
                    'current_time' => $currentTime,
                    'start_time' => $workingHour->start_time,
                    'end_time' => $workingHour->end_time,
                    'user_id' => $user->id,
                ]);
                
                // If time parsing fails, allow login to avoid blocking users
                Log::warning('Working hours validation skipped due to time parsing error - allowing login');
                return;
            }
            
            // Check if current time is outside the working hours range
            if ($startTimeCarbon->lte($endTimeCarbon)) {
                // Normal case: start time is before or equal to end time (e.g., 9:00 AM to 5:00 PM)
                $isOutsideWorkingHours = $currentTimeCarbon->lt($startTimeCarbon) || $currentTimeCarbon->gt($endTimeCarbon);
            } else {
                // Overnight case: start time is after end time (e.g., 10:00 PM to 6:00 AM)
                $isOutsideWorkingHours = $currentTimeCarbon->lt($startTimeCarbon) && $currentTimeCarbon->gt($endTimeCarbon);
            }
        }
        
        if ($isOutsideWorkingHours) {
            // Log the violation attempt
            Log::warning("User {$user->id} ({$user->email}) attempted to login outside working hours", [
                'user_id' => $user->id,
                'day' => $currentDayOfWeek,
                'time' => $currentTime,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'working_hour_id' => $workingHour?->id,
                'start_time' => $workingHour?->start_time,
                'end_time' => $workingHour?->end_time,
                'debug_info' => [
                    'current_time_parsed' => isset($currentTimeCarbon) ? $currentTimeCarbon->format('H:i:s') : null,
                    'start_time_parsed' => isset($startTimeCarbon) ? $startTimeCarbon->format('H:i:s') : null,
                    'end_time_parsed' => isset($endTimeCarbon) ? $endTimeCarbon->format('H:i:s') : null,
                    'is_outside_hours' => $isOutsideWorkingHours,
                ],
            ]);

            // Send notification to admin users
            $this->notifyAdminOfViolation($user, $workingHour, $currentDayOfWeek, $currentTime);

            // Set static flag to suppress login/logout notifications
            \App\Helpers\NotificationSuppression::$suppressLoginLogout = true;

            // Logout the user immediately
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            // Prepare error message
            $errorMessage = 'You are not allowed to login during this time.';
            
            if ($workingHour) {
                $startTime = Carbon::parse($workingHour->start_time)->format('g:i A');
                $endTime = Carbon::parse($workingHour->end_time)->format('g:i A');
                $dayName = ucfirst($currentDayOfWeek);
                $errorMessage = "You can only login on {$dayName} between {$startTime} and {$endTime}.";
            } else {
                $errorMessage = "You don't have working hours set for " . ucfirst($currentDayOfWeek) . ". Please contact your administrator.";
            }

            // Throw validation exception to prevent login
            throw ValidationException::withMessages([
                'form.email' => $errorMessage,
            ]);
        }

        // Log successful working hours validation
        Log::info("User {$user->id} ({$user->email}) login validated within working hours", [
            'user_id' => $user->id,
            'day' => $currentDayOfWeek,
            'time' => $currentTime,
            'working_hour_id' => $workingHour->id,
            'start_time' => $workingHour->start_time,
            'end_time' => $workingHour->end_time,
            'debug_info' => [
                'current_time_parsed' => $this->parseTimeString($currentTime)?->format('H:i:s') ?? $currentTime,
                'start_time_parsed' => $this->parseTimeString($workingHour->start_time)?->format('H:i:s') ?? $workingHour->start_time,
                'end_time_parsed' => $this->parseTimeString($workingHour->end_time)?->format('H:i:s') ?? $workingHour->end_time,
            ],
        ]);
    }

    /**
     * Validate if user's assigned branch is active
     *
     * @param \App\Domain\Entities\User $user
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateBranchStatus($user): void
    {
        // Check if user has an assigned branch
        if (!$user->branch_id) {
            return; // No branch assigned, allow login
        }

        // Load the branch with its status
        $branch = \App\Models\Domain\Entities\Branch::find($user->branch_id);
        
        if (!$branch) {
            // Branch not found, log error but allow login
            Log::error('User assigned to non-existent branch', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'branch_id' => $user->branch_id,
            ]);
            return;
        }

        // Check if branch is inactive
        if (!$branch->is_active) {
            // Log the branch status violation
            Log::warning('User attempted login with inactive branch', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_name' => $user->name,
                'branch_id' => $branch->id,
                'branch_name' => $branch->name,
                'branch_status' => $branch->is_active,
                'ip_address' => request()->ip(),
                'timestamp' => now()->toDateTimeString(),
            ]);

            // Send notification to admin users about inactive branch login attempt
            $this->notifyAdminOfInactiveBranchAttempt($user, $branch);

            // Logout the user immediately
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            // Prepare error message
            $errorMessage = "لا يمكن تسجيل الدخول. الفرع المخصص لك غير نشط. يرجى الاتصال بالإدارة.";

            // Throw validation exception to prevent login
            throw ValidationException::withMessages([
                'form.email' => $errorMessage,
            ]);
        }

        // Log successful branch validation
        Log::info("User {$user->id} ({$user->email}) login validated with active branch", [
            'user_id' => $user->id,
            'branch_id' => $branch->id,
            'branch_name' => $branch->name,
            'branch_status' => $branch->is_active,
        ]);
    }

    /**
     * Parse time string with multiple format attempts
     *
     * @param string $timeString
     * @return \Carbon\Carbon|null
     */
    private function parseTimeString(string $timeString): ?Carbon
    {
        // Remove any trailing whitespace or unexpected characters
        $timeString = trim($timeString);
        
        // List of possible time formats to try
        $formats = [
            'H:i:s',        // 24-hour format with seconds (e.g., "14:30:00")
            'H:i',          // 24-hour format without seconds (e.g., "14:30")
            'h:i:s A',      // 12-hour format with seconds (e.g., "2:30:00 PM")
            'h:i A',        // 12-hour format without seconds (e.g., "2:30 PM")
            'g:i:s A',      // 12-hour format with seconds, no leading zero (e.g., "2:30:00 PM")
            'g:i A',        // 12-hour format without seconds, no leading zero (e.g., "2:30 PM")
        ];
        
        foreach ($formats as $format) {
            try {
                $carbon = Carbon::createFromFormat($format, $timeString);
                if ($carbon !== false) {
                    return $carbon;
                }
            } catch (\Exception $e) {
                // Continue to next format
                continue;
            }
        }
        
        // If all formats fail, try to parse as a general time string
        try {
            return Carbon::parse($timeString);
        } catch (\Exception $e) {
            Log::error('Failed to parse time string with all methods', [
                'time_string' => $timeString,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Send notification to admin users about working hours violation
     *
     * @param \App\Domain\Entities\User $user
     * @param \App\Models\WorkingHour|null $workingHour
     * @param string $currentDayOfWeek
     * @param string $currentTime
     */
    private function notifyAdminOfViolation($user, $workingHour, string $currentDayOfWeek, string $currentTime): void
    {
        $userId = $user->id;
        $cacheKey = "workhours_violation_notification_sent_{$userId}_{$currentDayOfWeek}";
        if (cache()->has($cacheKey)) {
            return; // Already sent recently
        }
        cache()->put($cacheKey, true, 10); // 10 seconds debounce
        try {
            // Get current time for notification
            $now = Carbon::now();
            // Prepare notification message
            $message = "🚨 Working Hours Violation Alert\n\n";
            $message .= "User: {$user->name} (ID: {$user->id})\n";
            $message .= "Email: {$user->email}\n";
            $message .= "Attempted login outside allowed working hours\n\n";
            $message .= "📅 Day: " . ucfirst($currentDayOfWeek) . "\n";
            $message .= "🕐 Time: {$currentTime}\n";
            $message .= "📍 IP Address: " . request()->ip() . "\n";
            if ($workingHour) {
                $startTime = $this->parseTimeString($workingHour->start_time)?->format('g:i A') ?? $workingHour->start_time;
                $endTime = $this->parseTimeString($workingHour->end_time)?->format('g:i A') ?? $workingHour->end_time;
                $message .= "⏰ Allowed Hours: {$startTime} - {$endTime}\n";
            } else {
                $message .= "❌ No working hours defined for " . ucfirst($currentDayOfWeek) . "\n";
            }
            // Add branch information if available
            if ($user->branch) {
                $message .= "🏢 Branch: {$user->branch->name}\n";
            }
            $message .= "\n📊 Timestamp: {$now->format('Y-m-d H:i:s')}";
            // Get all admin users
            $admins = \App\Domain\Entities\User::role('admin')->get();
            if ($admins->count() > 0) {
                // Send notification to all admins
                Notification::send($admins, new AdminNotification(
                    $message,
                    url('/notifications') // URL to view notifications
                ));
                Log::info('Admin notification sent for working hours violation', [
                    'user_id' => $user->id,
                    'admin_count' => $admins->count(),
                    'violation_time' => $currentTime,
                    'day' => $currentDayOfWeek,
                ]);
            } else {
                Log::warning('No admin users found to notify about working hours violation', [
                    'user_id' => $user->id,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send admin notification for working hours violation', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Send notification to admin users about inactive branch login attempt
     *
     * @param \App\Domain\Entities\User $user
     * @param \App\Models\Domain\Entities\Branch $branch
     */
    private function notifyAdminOfInactiveBranchAttempt($user, $branch): void
    {
        $userId = $user->id;
        $cacheKey = "inactive_branch_notification_sent_{$userId}";
        if (cache()->has($cacheKey)) {
            return; // Already sent recently
        }
        cache()->put($cacheKey, true, 60); // 1 minute debounce
        
        try {
            // Get current time for notification
            $now = Carbon::now();
            
            // Prepare notification message
            $message = "🚨 Inactive Branch Login Attempt Alert\n\n";
            $message .= "User: {$user->name} (ID: {$user->id})\n";
            $message .= "Email: {$user->email}\n";
            $message .= "Role: " . $user->roles->first()->name ?? 'Unknown' . "\n";
            $message .= "Attempted login with inactive branch\n\n";
            $message .= "🏢 Branch: {$branch->name} (ID: {$branch->id})\n";
            $message .= "📍 IP Address: " . request()->ip() . "\n";
            $message .= "📊 Timestamp: {$now->format('Y-m-d H:i:s')}\n\n";
            $message .= "⚠️ Action Required: Please activate the branch or reassign the user to an active branch.";
            
            // Get all admin users
            $admins = \App\Domain\Entities\User::role('admin')->get();
            if ($admins->count() > 0) {
                // Send notification to all admins
                Notification::send($admins, new AdminNotification(
                    $message,
                    url('/admin/branches') // URL to manage branches
                ));
                Log::info('Admin notification sent for inactive branch login attempt', [
                    'user_id' => $user->id,
                    'branch_id' => $branch->id,
                    'admin_count' => $admins->count(),
                ]);
            } else {
                Log::warning('No admin users found to notify about inactive branch login attempt', [
                    'user_id' => $user->id,
                    'branch_id' => $branch->id,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send admin notification for inactive branch login attempt', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'branch_id' => $branch->id,
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
