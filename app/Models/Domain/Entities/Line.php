<?php

namespace App\Models\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Line extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'mobile_number',
        'current_balance',
        'daily_limit',
        'monthly_limit',
        'network',
        'user_id',
    ];

    /**
     * Get the user that owns the line.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\Entities\User::class);
    }

    /**
     * Get the transactions for the line.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
