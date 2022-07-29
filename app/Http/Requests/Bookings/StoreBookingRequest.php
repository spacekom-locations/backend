<?php

namespace App\Http\Requests\Bookings;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // every user can make a request for bookings
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'location_id' => 'required|exists:locations,id',
            'dates' => 'required|json',
            'addons' => 'required|json',
            'project_name' => 'required',
            'company_name' => 'required',
            'crew_size' => 'required',
            'activity' => 'required',
            'sub_activity' => 'required',
            'pricing' => 'required|json',
            'total_price' => 'required|numeric',
            'system_fees' => 'json',
            'custom_fees' => 'json'
        ];
    }
}
