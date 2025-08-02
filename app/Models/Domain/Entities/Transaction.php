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
        'customer_code',
        'amount',
        'commission',
        'deduction',
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
            case 'Deposit':
                return 'إيداع أموال';
            case 'Withdrawal':
                return 'سحب أموال';
            default:
                return ucfirst($type);
        }
    }
}
