<?php

namespace App\Models\Domain\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Database\Factories\WorkSessionFactory;
use App\Domain\Entities\User;

class WorkSession extends Model
{
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return WorkSessionFactory::new();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'login_at',
        'logout_at',
        'duration_minutes',
        'ip_address',
        'user_agent',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'login_at' => 'datetime',
        'logout_at' => 'datetime',
    ];

    /**
     * Get the user that owns the work session.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate and set the duration_minutes attribute.
     */
    public function calculateDuration(): void
    {
        if ($this->login_at && $this->logout_at) {
            // Calculate the absolute difference in minutes between login and logout
            if ($this->logout_at->lt($this->login_at)) {
                // If logout time is before login time (which shouldn't happen normally),
                // set a minimum duration (1 minute) or default to zero
                $this->duration_minutes = 0;
            } else {
                $this->duration_minutes = $this->login_at->diffInMinutes($this->logout_at);
            }
            $this->save();
        }
    }
}
