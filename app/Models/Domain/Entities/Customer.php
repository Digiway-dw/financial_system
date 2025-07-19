<?php

namespace App\Models\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Customer extends Model
{
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'mobile_number',
        'customer_code',
        'gender',
        'is_client',
        'agent_id',
        'branch_id',
        'balance',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'is_client' => 'boolean',
    ];

    // Prevent negative balance
    public function setBalanceAttribute($value)
    {
        if ($value < 0) {
            throw new \InvalidArgumentException('Customer balance cannot be negative. Attempted to set: ' . $value);
        }
        $this->attributes['balance'] = $value;
    }

    /**
     * Get the transactions for the customer.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(\App\Models\Domain\Entities\Transaction::class, 'customer_mobile_number', 'mobile_number');
    }

    /**
     * Get the agent (user) that is linked to the customer.
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\Entities\User::class, 'agent_id');
    }

    /**
     * Get the branch that is linked to the customer.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Domain\Entities\Branch::class, 'branch_id');
    }

    public function mobileNumbers(): HasMany
    {
        return $this->hasMany(\App\Models\Domain\Entities\CustomerMobileNumber::class);
    }

    /**
     * Get the user who created the customer.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\Entities\User::class, 'created_by');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }
}
