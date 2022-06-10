<?php

namespace App\Http\Requests\Locations;

use App\Models\Location;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class DeleteLocationImageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // if there is a location and its owned by current user then allow removal
        $location = Location::find(Route::current()->parameter('id'));
        if ($location && $location->user_id == auth('users')->user()->id) {
            return true;
        }
        //otherwise don't allow removal
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
            'image' => 'required|url'
        ];
    }
}
