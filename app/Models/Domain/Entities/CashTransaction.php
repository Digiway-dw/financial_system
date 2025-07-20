<?php

namespace App\Models\Domain\Entities;

use Illuminate\Database\Eloquent\Model;

class CashTransaction extends Model
{
    protected $table = 'cash_transactions';

    protected $fillable = [
        'customer_name',
        'customer_code',
        'amount',
        'notes',
        'safe_id',
        'transaction_type',
        'status',
        'transaction_date_time',
        'depositor_national_id',
        'depositor_mobile_number',
        'agent_id',
        'destination_branch_id',
        'destination_safe_id',
        'reference_number',
    ];

    public function getCommissionAttribute()
    {
        return 0;
    }

    public function agent()
    {
        return $this->belongsTo(\App\Domain\Entities\User::class, 'agent_id');
    }

    public function safe()
    {
        return $this->belongsTo(\App\Models\Domain\Entities\Safe::class, 'safe_id');
    }

    // Future relationships and methods can be added here
} 