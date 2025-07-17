<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StartupSafeBalance extends Model
{
    protected $fillable = [
        'branch_id',
        'balance',
        'date',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Domain\Entities\Branch::class, 'branch_id');
    }
}