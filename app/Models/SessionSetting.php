<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionSetting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value',
        'description',
    ];

    /**
     * Get the session lifetime in minutes
     *
     * @return int
     */
    public static function getSessionLifetime(): int
    {
        $setting = self::where('key', '=', 'session_lifetime')->first();
        return $setting ? (int) $setting->value : 120; // Default to 120 minutes (2 hours)
    }

    /**
     * Update the session lifetime
     *
     * @param int $minutes
     * @return bool
     */
    public static function updateSessionLifetime(int $minutes): bool
    {
        if ($minutes < 1) {
            return false;
        }

        $setting = self::where('key', '=', 'session_lifetime')->first();
        if ($setting) {
            $setting->value = (string) $minutes;
            return $setting->save();
        }

        return false;
    }
}
