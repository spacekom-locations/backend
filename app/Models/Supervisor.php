<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Supervisor extends Authenticatable
{
    use HasFactory, HasApiTokens, HasRoles;
    use SoftDeletes;

    /**
     * model constants
     */
    public const STATUS_ACTIVE = 'ACTIVE';
    public const STATUS_SUSPENDED = 'SUSPENDED';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'user_name',
        'password',
        'notes',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'deleted_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Checks whether the supervisor status is active
     * @return bool true if the supervisor status is active false otherwise
     */
    public function isActive():bool
    {
        return $this->status == Supervisor::STATUS_ACTIVE;
    }

    /**
     * Change the supervisor status to suspended
     */
    public function suspendAccount()
    {
        $this->status = Supervisor::STATUS_SUSPENDED;
        $this->save();
    }

    /**
     * Change the supervisor status to active
     */
    public function activateAccount()
    {
        $this->status = Supervisor::STATUS_ACTIVE;
        $this->save();
    }

}
