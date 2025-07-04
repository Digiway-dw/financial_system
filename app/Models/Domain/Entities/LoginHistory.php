<?php

namespace App\Models\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginHistory extends Model
{
    protected $table = 'login_histories';

    protected $fillable = [
        'user_id',
        'login_at',
        'logout_at',
        'session_duration',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
} 