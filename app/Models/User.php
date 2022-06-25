<?php

namespace App\Models;

use App\Traits\TraitUuid;
use Cmgmyr\Messenger\Traits\Messagable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, TraitUuid, SoftDeletes, Messagable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_name',
        'avatar',
        'company',
        'category',
        'bio',
        'phone_number',
        'notes'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'deleted_at',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'phone_number_verified_at' => 'datetime',
    ];

    /**
     * Checks whether the supervisor status is active
     * @return bool true if the supervisor status is active false otherwise
     */
    public function isActive(): bool
    {
        return $this->status == static::STATUS_ACTIVE;
    }

    /**
     * Change the supervisor status to suspended
     */
    public function suspendAccount()
    {
        $this->status = static::STATUS_SUSPENDED;
        $this->save();
    }

    /**
     * Change the supervisor status to active
     */
    public function activateAccount()
    {
        $this->status = static::STATUS_ACTIVE;
        $this->save();
    }

    /**
     * model constants
     */
    public const STATUS_ACTIVE = 'ACTIVE';
    public const STATUS_SUSPENDED = 'SUSPENDED';

    /**
     * user account valid statuses
     */
    public static function validStatuses(): array
    {
        return [
            static::STATUS_ACTIVE,
            static::STATUS_SUSPENDED
        ];
    }

    /**
     * default user status
     */
    public static function defaultStatus(): string
    {
        return static::STATUS_ACTIVE;
    }

    /**
     * user valid categories
     */
    public static function validCategories(): array
    {
        return [
            'PRODUCER_PRODUCTION_MANAGER',
            'BRAND_AFFILIATED',
            'INDEPENDENT_CONTENT_CREATOR',
            'STUDENT',
            'EVENT_PLANER',
            'PHOTOGRAPHER',
            'VIDEOGRAPHER',
            'DIRECTOR',
            'WEDDING_PLANNER',
            'ARTIST',
            'BRIDE',
            'BUSINESS_OWNER_AFFILIATED'
        ];
    }
}
