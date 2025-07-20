<?php

namespace App\Domain\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Models\Domain\Entities\Branch;
use App\Models\Domain\Entities\Transaction;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Collection;
use App\Models\WorkingHour;

/**
 * @method bool hasRole(string|array|\Spatie\Permission\Contracts\Role $roles, string $guard = null)
 * @method bool hasAnyRole(string|array|\Spatie\Permission\Contracts\Role $roles, string $guard = null)
 * @method bool hasAllRoles(string|array|\Spatie\Permission\Contracts\Role $roles, string $guard = null)
 * @method bool hasPermissionTo(string|Permission $permission, string $guard = null)
 * @method bool hasAnyPermission(string|array|Permission|Collection $permissions, string $guard = null)
 * @method bool hasAllPermissions(string|array|Permission|Collection $permissions, string $guard = null)
 * @method bool hasDirectPermission(string|Permission $permission, string $guard = null)
 * @method $this assignRole(string|array|Role|Collection $roles)
 * @method $this removeRole(string|array|Role|Collection $roles)
 * @method $this syncRoles(string|array|Role|Collection $roles)
 * @method $this givePermissionTo(string|array|Permission|Collection $permissions)
 * @method $this revokePermissionTo(string|array|Permission|Collection $permissions)
 * @method $this syncPermissions(string|array|Permission|Collection $permissions)
 * @method Collection getAllPermissions()
 * @method Collection getPermissionsViaRoles()
 * @method Collection getDirectPermissions()
 * @method Collection getRoleNames()
 * @method Collection getPermissionNames()
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, LogsActivity, HasRoles, SoftDeletes;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'branch_id',
        'phone_number',
        'national_number',
        'salary',
        'address',
        'land_number',
        'relative_phone_number',
        'notes',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the branch that the user belongs to.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the transactions performed by the user.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'agent_id');
    }

    /**
     * Get the login histories for the user.
     */
    public function loginHistories(): HasMany
    {
        return $this->hasMany(\App\Models\Domain\Entities\LoginHistory::class);
    }

    /**
     * Get the work sessions for the user.
     */
    public function workSessions(): HasMany
    {
        return $this->hasMany(\App\Models\Domain\Entities\WorkSession::class);
    }

    /**
     * Get the working hours for the user.
     */
    public function workingHours(): HasMany
    {
        return $this->hasMany(WorkingHour::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }
}
