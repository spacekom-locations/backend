<?php

namespace App\Http\Requests\Locations;

use App\Models\Location;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class UpdateLocationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // if there is a location and its owned by current user then allow update
        $location = Location::find(Route::current()->parameter('id'));
        if ($location && $location->user_id == auth('users')->user()->id) {
            return true;
        }

        //otherwise don't allow update
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            // 'country' => '',
            // 'state' => '',
            // 'city' => '',
            // 'street' => '',
            // 'zip_code' => '',
            // 'apt_suite' => '',
            // 'types' => 'array',
            // 'motor_home_parking_types' => 'array',
            // 'access_availability_types' => [],
            // 'amenities' => [],
            // 'styles' => [],
            // 'features' => [],
            // 'architectural_styles' => [],
            // 'bathrooms_types' => [],
            // 'ceiling_types' => [],
            // 'doors_types' => [],
            // 'exterior_types' => [],
            // 'floor_types' => [],
            // 'interior_types' => [],
            // 'kitchen_features' => [],
            // 'activities_types' => [],
            // 'walls_types' => [],
            // 'water_features' => [],
            // 'windows_types' => [],
            // 'crews_rules' => [],
            // 'allowed_activities' => [],
            // 'pricing' => '',
            // 'images' => [],
        ];
    }
}
