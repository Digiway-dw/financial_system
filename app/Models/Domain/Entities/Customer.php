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
        'allow_debt',
        'max_debt_limit',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'is_client' => 'boolean',
        'allow_debt' => 'boolean',
        'max_debt_limit' => 'decimal:2',
    ];

    // Handle balance validation with debt mode support
    public function setBalanceAttribute($value)
    {
        // If debt is not allowed, prevent negative balance
        if ($value < 0 && !$this->allow_debt) {
            throw new \InvalidArgumentException('Customer balance cannot be negative when debt mode is disabled. Attempted to set: ' . $value);
        }
        
        // If debt is allowed but value exceeds debt limit
        if ($this->allow_debt && $this->max_debt_limit && $value < $this->max_debt_limit) {
            throw new \InvalidArgumentException('Customer balance cannot exceed the debt limit of ' . $this->max_debt_limit . '. Attempted to set: ' . $value);
        }
        
        $this->attributes['balance'] = $value;
    }

    /**
     * Check if customer can send a specific amount
     */
    public function canSendAmount($amount)
    {
        $newBalance = $this->balance - $amount;
        
        // If debt is not allowed, check positive balance
        if (!$this->allow_debt) {
            return $this->balance >= $amount;
        }
        
        // If debt is allowed, check against debt limit
        return $this->max_debt_limit === null || $newBalance >= $this->max_debt_limit;
    }

    /**
     * Get available sending limit (how much can be sent)
     */
    public function getAvailableSendingLimit()
    {
        if (!$this->allow_debt) {
            return max(0, $this->balance);
        }
        
        if ($this->max_debt_limit === null) {
            return PHP_FLOAT_MAX; // No limit
        }
        
        return $this->balance - $this->max_debt_limit;
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
