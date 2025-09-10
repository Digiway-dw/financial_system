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
        'receiver_mobile_number',
        'line_id',
        'from_line_id',
        'to_line_id',
        'customer_code',
        'amount',
        'commission',
        'deduction',
        'extra_fee',
        'total_deducted',
        'discount_notes',
        'notes',
        'transaction_type',
        'agent_id',
        'transaction_date_time',
        'status',
        'safe_id',
        'is_absolute_withdrawal',
        'payment_method',
        'notes', // add notes to fillable
        'branch_id',
        'reference_number',
    ];

    protected $casts = [
        'transaction_date_time' => 'datetime',
        'amount' => 'decimal:2',
        'commission' => 'decimal:2',
        'deduction' => 'decimal:2',
        'extra_fee' => 'decimal:2',
        'total_deducted' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $fillable = $model->getFillable();
            foreach (array_keys($model->attributes) as $key) {
                if (!in_array($key, $fillable)) {
                    unset($model->{$key});
                }
            }
        });
    }

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
     * Get the from line for line transfers.
     */
    public function fromLine(): BelongsTo
    {
        return $this->belongsTo(Line::class, 'from_line_id');
    }

    /**
     * Get the to line for line transfers.
     */
    public function toLine(): BelongsTo
    {
        return $this->belongsTo(Line::class, 'to_line_id');
    }

    /**
     * Get the safe used for the transaction.
     */
    public function safe(): BelongsTo
    {
        return $this->belongsTo(Safe::class);
    }

    /**
     * Get the branch for the transaction.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Domain\Entities\Branch::class, 'branch_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    /**
     * Scope for filtering by mobile number (customer or receiver)
     */
    public function scopeFilterByMobile($query, $mobileNumber)
    {
        if (!$mobileNumber) {
            return $query;
        }

        $cleanMobile = preg_replace('/\D/', '', $mobileNumber); // Remove non-digits

        return $query->where(function ($q) use ($cleanMobile) {
            $q->whereRaw('REGEXP_REPLACE(customer_mobile_number, "[^0-9]", "") LIKE ?', ["%{$cleanMobile}%"])
                ->orWhereRaw('REGEXP_REPLACE(receiver_mobile_number, "[^0-9]", "") LIKE ?', ["%{$cleanMobile}%"]);
        });
    }

    /**
     * Scope for filtering by reference number
     */
    public function scopeFilterByReference($query, $referenceNumber)
    {
        if (!$referenceNumber) {
            return $query;
        }

        return $query->whereRaw('LOWER(reference_number) LIKE ?', ['%' . strtolower(trim($referenceNumber)) . '%']);
    }

    /**
     * Scope for filtering by amount range
     */
    public function scopeAmountBetween($query, $minAmount = null, $maxAmount = null)
    {
        if ($minAmount !== null) {
            $query->where('amount', '>=', $minAmount);
        }
        if ($maxAmount !== null) {
            $query->where('amount', '<=', $maxAmount);
        }
        return $query;
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeDateBetween($query, $startDate = null, $endDate = null)
    {
        if ($startDate) {
            $query->whereDate('transaction_date_time', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('transaction_date_time', '<=', $endDate);
        }
        return $query;
    }

    /**
     * Scope for filtering by branch(es)
     */
    public function scopeBranches($query, $branchIds)
    {
        if (empty($branchIds)) {
            return $query;
        }

        if (is_array($branchIds)) {
            return $query->whereIn('branch_id', $branchIds);
        }

        return $query->where('branch_id', $branchIds);
    }

    /**
     * Scope for filtering by agent
     */
    public function scopeAgent($query, $agentId)
    {
        if (!$agentId) {
            return $query;
        }

        return $query->where('agent_id', $agentId);
    }

    /**
     * Get a descriptive transaction name based on the transaction type and context
     */
    public function getDescriptiveTransactionNameAttribute()
    {
        $type = $this->transaction_type;

        switch ($type) {
            case 'send':
                return 'إرسال أموال';
            case 'receive':
                return 'استلام أموال';
            case 'transfer':
                return 'تحويل أموال';
            case 'line_transfer':
                return 'تحويل خط';
            case 'Deposit':
                return 'إيداع أموال';
            case 'Withdrawal':
                return 'سحب أموال';
            case 'Adjustment':
                // Check if this is a customer balance adjustment
                if ($this->customer_name && $this->customer_name !== 'ADMIN BALANCE ADJUSTMENT') {
                    // This is a customer balance adjustment
                    if ($this->notes && str_contains($this->notes, 'increase')) {
                        return 'إيداع رصيد العميل';
                    } elseif ($this->notes && str_contains($this->notes, 'decrease')) {
                        return 'سحب رصيد العميل';
                    } else {
                        return 'تعديل رصيد العميل';
                    }
                } else {
                    // This is a safe balance adjustment
                    if ($this->notes && str_contains($this->notes, 'increase')) {
                        return 'إيداع رصيد الخزنة';
                    } elseif ($this->notes && str_contains($this->notes, 'decrease')) {
                        return 'سحب رصيد الخزنة';
                    } else {
                        return 'تعديل رصيد الخزنة';
                    }
                }
            default:
                return ucfirst($type);
        }
    }
}
