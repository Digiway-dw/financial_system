<?php

namespace App\Models\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Line extends Model
{
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'mobile_number',
        'network',
        'current_balance',
        'daily_limit',
        'monthly_limit',
        'starting_balance',
        'daily_starting_balance',
        'status',
        'branch_id',
    ];

    protected $casts = [
        'current_balance' => 'decimal:2',
        'daily_limit' => 'decimal:2',
        'monthly_limit' => 'decimal:2',
        'starting_balance' => 'decimal:2',
        'daily_starting_balance' => 'decimal:2',
    ];

    // Prevent negative balance
    public function setCurrentBalanceAttribute($value)
    {
        if ($value < 0) {
            throw new \InvalidArgumentException('Line balance cannot be negative. Attempted to set: ' . $value);
        }
        $this->attributes['current_balance'] = $value;
    }

    /**
     * Get the branch that owns the line.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Domain\Entities\Branch::class);
    }

    /**
     * Get the transactions for the line.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }
}
