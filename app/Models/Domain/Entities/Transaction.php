<?php

namespace App\Models\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Transaction extends Model
{
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_name',
        'customer_mobile_number',
        'line_id',
        'customer_code',
        'amount',
        'commission',
        'deduction',
        'transaction_type',
        'agent_id',
        'transaction_date_time',
        'status',
        'safe_id',
        'is_absolute_withdrawal',
        'payment_method',
    ];

    /**
     * Get the agent who performed the transaction.
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\Entities\User::class, 'agent_id');
    }

    /**
     * Get the line used for the transaction.
     */
    public function line(): BelongsTo
    {
        return $this->belongsTo(Line::class);
    }

    /**
     * Get the safe used for the transaction.
     */
    public function safe(): BelongsTo
    {
        return $this->belongsTo(Safe::class);
    }

    /**
     * Get the branch associated with the transaction.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }
}
