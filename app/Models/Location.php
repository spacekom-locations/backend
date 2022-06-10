<?php

namespace App\Models;

use App\Traits\TraitUuid;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use HasFactory, TraitUuid, SoftDeletes, Filterable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'country',
        'state',
        'city',
        'street',
        'apt_suite',
        'zip_code',
        'lat',
        'lng',
        'kind',
        'types',
        'bedrooms_count',
        'bathrooms_count',
        'size',
        'lot_size',
        'main_floor_number',
        'parking_capacity',
        'motor_home_parking_types',
        'access_availability_types',
        'has_parking_structure',
        'amenities',
        'styles',
        'features',
        'architectural_styles',
        'bathrooms_types',
        'ceiling_types',
        'doors_types',
        'exterior_types',
        'floor_types',
        'interior_types',
        'kitchen_features',
        'activities_types',
        'walls_types',
        'water_features',
        'windows_types',
        'description',
        'crews_rules',
        'allowed_activities',
        'maximum_attendees_number',
        'minimum_rental_hours',
        'currency_code',
        'pricing',
        'availability_calendar',
        'images',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'deleted_at'
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
        'has_parking_structure' => 'bool',
        'types' => 'array',
        'motor_home_parking_types' => 'array',
        'access_availability_types' => 'array',
        'amenities' => 'array',
        'styles' => 'array',
        'features' => 'array',
        'architectural_styles' => 'array',
        'bathrooms_types' => 'array',
        'ceiling_types' => 'array',
        'doors_types' => 'array',
        'exterior_types' => 'array',
        'floor_types' => 'array',
        'interior_types' => 'array',
        'kitchen_features' => 'array',
        'activities_types' => 'array',
        'walls_types' => 'array',
        'water_features' => 'array',
        'windows_types' => 'array',
        'crews_rules' => 'array',
        'allowed_activities' => 'array',
        'pricing' => 'array',
        'images' => 'array',
        'availability_calendar' => 'array',
        'minimum_rental_hours' => 'integer'
    ];

    public const KIND_RESIDENTIAL = 'RESIDENTIAL';
    public const KIND_COMMERCIAL = 'COMMERCIAL';
    public const KIND_STUDIO = 'STUDIO';
    public const KIND_TRANSPORTATION = 'TRANSPORTATION';
    public static function getValidKinds()
    {
        return [
            static::KIND_RESIDENTIAL,
            static::KIND_COMMERCIAL,
            static::KIND_STUDIO,
            static::KIND_TRANSPORTATION,
        ];
    }

    public const MOTOR_HOME_PARKING_TYPE_ON_PROPERTY = 'ON_PROPERTY';
    public const MOTOR_HOME_PARKING_TYPE_ON_STREET = 'STREET';
    public static function getValidMotorHomeParkingTypes()
    {
        return [
            static::MOTOR_HOME_PARKING_TYPE_ON_PROPERTY,
            static::MOTOR_HOME_PARKING_TYPE_ON_STREET,
        ];
    }

    public const ACCESS_AVAILABILITY_TYPE_ELEVATOR = 'ELEVATOR';
    public const ACCESS_AVAILABILITY_TYPE_FREIGHT_ELEVATOR = 'FREIGHT_ELEVATOR';
    public const ACCESS_AVAILABILITY_TYPE_STAIRS = 'STAIRS';
    public const ACCESS_AVAILABILITY_TYPE_STREET_LEVEL = 'STREET_LEVEL';
    public const ACCESS_AVAILABILITY_TYPE_WHEEL_CHAIR_HANDICAP_ACCESS = 'WHEEL_CHAIR_HANDICAP_ACCESS';
    public static function getValidAccessAvailabilityTypes()
    {
        return [
            static::ACCESS_AVAILABILITY_TYPE_ELEVATOR,
            static::ACCESS_AVAILABILITY_TYPE_FREIGHT_ELEVATOR,
            static::ACCESS_AVAILABILITY_TYPE_STAIRS,
            static::ACCESS_AVAILABILITY_TYPE_STREET_LEVEL,
            static::ACCESS_AVAILABILITY_TYPE_WHEEL_CHAIR_HANDICAP_ACCESS,
        ];
    }

    public const AMENITY_AIR_CONDITIONING = 'AIR_CONDITIONING';
    public const AMENITY_HAIR_MAKEUP_AREA = 'HAIR_MAKEUP_AREA';
    public const AMENITY_WIFI = 'WIFI';
    public static function getValidAmenities()
    {
        return [
            static::AMENITY_AIR_CONDITIONING,
            static::AMENITY_HAIR_MAKEUP_AREA,
            static::AMENITY_WIFI,
        ];
    }

    public const STATUS_UNPUBLISHED = 'UNPUBLISHED';
    public const STATUS_PUBLISHED = 'PUBLISHED';
    public const STATUS_IN_REVIEW = 'IN_REVIEW';
    public const STATUS_SUSPENDED = 'SUSPENDED';
    public static function getValidStatus()
    {
        return [
            static::STATUS_UNPUBLISHED,
            static::STATUS_IN_REVIEW,
            static::STATUS_PUBLISHED,
            static::STATUS_SUSPENDED,
        ];
    }
    public static function getDefaultStatus()
    {
        return static::STATUS_UNPUBLISHED;
    }

    public static function getDefaultPricing()
    {
        $initialProductionAmount = 100;
        $initialEventAmount = 150;
        $initialMeetingAmount = 75;
        $ranges = [
            ['name' => '1 - 5', 'additional_amount_percentage' => 0,],
            ['name' => '6 - 15', 'additional_amount_percentage' => 11,],
            ['name' => '16 - 30', 'additional_amount_percentage' => 33,],
            ['name' => '31 - 45', 'additional_amount_percentage' => 66,],
            ['name' => '46 - 60', 'additional_amount_percentage' => 133,],
            ['name' => '60+', 'additional_amount_percentage' => 166,],
        ];
        $pricingRanges = [];
        foreach ($ranges as $range) {

            $productionAmount = $initialProductionAmount +
                ($initialProductionAmount * $range['additional_amount_percentage']) / 100;
            $eventAmount = $initialEventAmount +
                ($initialEventAmount * $range['additional_amount_percentage']) / 100;
            $meetingAmount = $initialMeetingAmount +
                ($initialMeetingAmount * $range['additional_amount_percentage']) / 100;

            $pricingRanges[] = [
                'name' => $range['name'],
                'is_disabled' => false,
                'prices' => [
                    'PRODUCTION' => floor($productionAmount),
                    'EVENT' => floor($eventAmount),
                    'MEETING' => floor($meetingAmount)
                ]
            ];
        }

        return [
            'is_automatic' => true,
            'ranges' => $pricingRanges,
        ];
    }

    public static function getDefaultAvailabilityCalendar()
    {
        return [
            'blockedWeekDays' => [],
            'forcedBlockedDays' => [],
            'forcedAvailableDays' => [],
        ];
    }
}
