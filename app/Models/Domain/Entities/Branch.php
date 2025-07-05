<?php

namespace App\Models\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Branch extends Model
{
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'location',
        'branch_code',
    ];

    /**
     * Get the users for the branch.
     */
    public function users(): HasMany
    {
        return $this->hasMany(\App\Domain\Entities\User::class);
    }

    /**
     * Get the safe for the branch.
     */
    public function safe(): HasOne
    {
        return $this->hasOne(Safe::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }
}
