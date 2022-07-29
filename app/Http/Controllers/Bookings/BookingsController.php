<?php

namespace App\Http\Controllers\Bookings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Bookings\StoreBookingRequest;
use App\Models\LocationBookings;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BookingsController extends Controller
{
    public function index()
    {
        $user = auth('users')->user();
        $bookings = LocationBookings::with(['location', 'user'])->whereIn('location_id', function ($query) use ($user) {
            $query->select('id')->from('locations')->where('user_id', $user->id);
        })->get();
        return $this->sendData($bookings);
    }
    public function store(StoreBookingRequest $request)
    {
        $user = auth('users')->user();

        $booking = LocationBookings::where('user_id', $user->id)
            ->where('location_id', $request->input('location_id'))
            ->whereIn('status', [LocationBookings::STATUS_PENDING, LocationBookings::STATUS_APPROVED])->first();
        if ($booking) {
            return $this->sendError(['location already in progress'], Response::HTTP_NOT_ACCEPTABLE);
        }

        $bookingData = [
            'location_id' => $request->input('location_id'),
            'user_id' => $user->id,
            'dates' => $request->input('dates'),
            'addons' => $request->input('addons'),
            'project_name' => $request->input('project_name'),
            'project_description' => $request->input('project_description'),
            'company_name' => $request->input('company_name'),
            'about_renter' => $request->input('about_renter'),
            'system_fees' => $request->input('system_fees'),
            'custom_fees' => $request->input('custom_fees'),
            'crew_size' => $request->input('crew_size'),
            'activity' => $request->input('activity'),
            'sub_activity' => $request->input('sub_activity'),
            'pricing' => $request->input('pricing'),
            'total_price' => $request->input('total_price'),
        ];


        $booking = LocationBookings::create($bookingData);

        return $this->sendData($booking, [], Response::HTTP_CREATED);
    }
}
