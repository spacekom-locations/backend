<?php

namespace App\Services\Locations;

use App\Models\Location;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class LocationService
{
    public static function create(User $user, string $country, string $state, string $city, string $street, string $zipCode, string $aptSuite = null)
    {
        $newLocation = new Location();
        $newLocation->user_id = $user->id;
        $newLocation->images = [];
        $newLocation->save();
        $newLocation->update([
            'country' => $country,
            'state' => $state,
            'city' => $city,
            'street' => $street,
            'zip_code' => $zipCode,
            'apt_suite' => $aptSuite,
            'types' => [],
            'motor_home_parking_types' => [],
            'access_availability_types' => [],
            'amenities' => [],
            'styles' => [],
            'features' => [],
            'architectural_styles' => [],
            'bathrooms_types' => [],
            'ceiling_types' => [],
            'doors_types' => [],
            'exterior_types' => [],
            'floor_types' => [],
            'interior_types' => [],
            'kitchen_features' => [],
            'activities_types' => [],
            'walls_types' => [],
            'water_features' => [],
            'windows_types' => [],
            'crews_rules' => [],
            'availability_calendar' => Location::getDefaultAvailabilityCalendar(),
            'allowed_activities' => [],
            'pricing' => Location::getDefaultPricing(),
        ]);
        return $newLocation->fresh();
    }

    public static function update(Location $location, array $updateData)
    {
        $location->update($updateData);
        return $location->fresh();
    }

    public static function uploadImages(Location $location, $images)
    {

        $locationImages = array_merge([], $location->images);
        foreach ($images as $image) {
            $name = uniqid() . '.' . $image->extension();
            $image->storeAs('public/images/locations', $name);
            $imageURL = url("storage/images/locations/" . $name);
            $locationImages[] = $imageURL;
        }

        $location->images = $locationImages;
        $location->save();

        return $location->fresh();
    }

    public static function removeImage(Location $location, string $image)
    {
        if (!in_array($image, $location->images)) {
            return $location;
        }

        $images = $location->images;

        array_splice($images, array_search($image, $images), 1);

        Storage::delete('public/images/locations/' .  basename($image));

        $location->images = $images;
        $location->save();

        return $location->fresh();
    }

    public static function indexWithFilters($filters)
    {
        return Location::filter($filters)->get();
    }

    public static function indexWithActivityAndFilters($activity, $filters)
    {
        $locations = Location::filter($filters);
        return $locations->where('allowed_activities', 'LIKE', "%$activity%")->get();
    }
}
