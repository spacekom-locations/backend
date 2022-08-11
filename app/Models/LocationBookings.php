<?php

namespace App\Models;

use App\Models\Messenger\Thread;
use App\Traits\TraitUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LocationBookings extends Model
{
    use HasFactory, TraitUuid, SoftDeletes;

    protected $table = 'location_bookings';

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }

    public function threads()
    {
        return $this->hasMany(Thread::class, 'location_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(user::class, 'user_id', 'id');
    }

    protected $fillable = [
        'location_id',
        'user_id',
        'dates',
        'addons',
        'project_name',
        'project_description',
        'company_name',
        'about_renter',
        'system_fees',
        'custom_fees',
        'crew_size',
        'activity',
        'sub_activity',
        'is_custom_rate',
        'pricing',
        'total_price',
        'cancellation_reason',
    ];

    protected $casts = [
        'dates' => 'array',
        'addons' => 'array',
        'system_fees' => 'array',
        'custom_fees' => 'array',
        'pricing' => 'array',
    ];

    public const STATUS_PENDING = 'PENDING';
    public const STATUS_APPROVED = 'APPROVED';
    public const STATUS_DECLINED = 'DECLINED';
    public const STATUS_COMPLETED = 'COMPLETED';
    public const STATUS_CANCELED = 'CANCELED';
    public const STATUS_PAID = 'PAID';

    public static function getValidStatuses()
    {
        return [
            static::STATUS_PENDING,
            static::STATUS_APPROVED,
            static::STATUS_DECLINED,
            static::STATUS_COMPLETED,
            static::STATUS_CANCELED,
            static::STATUS_PAID,
        ];
    }

    public static function getDefaultStatus()
    {
        return static::STATUS_PENDING;
    }
}
