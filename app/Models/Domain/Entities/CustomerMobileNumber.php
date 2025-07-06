<?php

namespace App\Models\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerMobileNumber extends Model
{
    protected $fillable = [
        'customer_id',
        'mobile_number',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    // Removed boot method for duplicate check; now handled in Livewire validation
} 