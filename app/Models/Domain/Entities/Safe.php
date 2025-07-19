<?php

namespace App\Models\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Safe extends Model
{
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'current_balance',
        'branch_id',
        'type',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'current_balance' => 'decimal:2',
    ];

    // Prevent negative balance
    public function setCurrentBalanceAttribute($value)
    {
        if ($value < 0) {
            throw new \InvalidArgumentException('Safe balance cannot be negative. Attempted to set: ' . $value);
        }
        $this->attributes['current_balance'] = $value;
    }

    /**
     * Get the branch that owns the safe.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the transactions for the safe.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function isClientSafe(): bool
    {
        return $this->type === 'client';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }
}
