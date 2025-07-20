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

        // Check working hours for non-admin users
        $user = Auth::user();
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
        if (
            !$workingHour ||
            $currentTime < $workingHour->start_time ||
            $currentTime > $workingHour->end_time
        ) {
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
            ]);

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
        ]);
    }
}
