<?php

namespace App\Http\Controllers\Bookings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Bookings\ApproveBookingRequest;
use App\Http\Requests\Bookings\DeclineBookingRequest;
use App\Http\Requests\Bookings\IndexBookingsRequest;
use App\Http\Requests\Bookings\StoreBookingRequest;
use App\Models\Location;
use App\Models\LocationBookings;
use Cmgmyr\Messenger\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BookingsController extends Controller
{
    public function index(IndexBookingsRequest $request)
    {
        $user = auth('users')->user();
        $isHost = filter_var($request->header('is-host'), FILTER_VALIDATE_BOOLEAN);

        if ($isHost) {
            $bookings = LocationBookings::with(['location', 'user'])->whereIn('location_id', function ($query) use ($user) {
                $query->select('id')->from('locations')->where('user_id', $user->id);
            })->get();
        } else {
            $bookings = LocationBookings::with(['location', 'user'])->where('user_id', $user->id)->get();
        }

        $bookingsLength = count($bookings);
        for ($i = 0; $i < $bookingsLength; $i++) {
            $location = $bookings[$i]->location;
            $thread = Thread::where('location_id', $location->id)->whereHas('participants', function ($query) use ($user) {
                return $query->where('user_id', '=', $user->id);
            })->first();

            if ($thread) {
                $location['has_active_messages_thread'] = true;
                $location['thread'] = $thread;
            }
        }
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

    public function approve(ApproveBookingRequest $request, string $id)
    {
        $booking = LocationBookings::with('location', 'user')
            ->where('id', $id)
            ->where('status', LocationBookings::STATUS_PENDING)
            ->first();

        if (!$booking) {
            return $this->sendError('booking not found', Response::HTTP_NOT_FOUND);
        }

        $user = auth('users')->user();

        if ($user->id != $booking->location->user_id) {
            return $this->sendError('forbidden', Response::HTTP_FORBIDDEN);
        }

        $booking->status = LocationBookings::STATUS_APPROVED;
        $booking->save();
        return $this->sendData('booking approved');
    }

    public function decline(DeclineBookingRequest $request, string $id)
    {
        $booking = LocationBookings::with('location', 'user')
            ->where('id', $id)
            ->where('status', LocationBookings::STATUS_PENDING)
            ->first();

        if (!$booking) {
            return $this->sendError('booking not found', Response::HTTP_NOT_FOUND);
        }

        $user = auth('users')->user();

        if ($user->id != $booking->location->user_id) {
            return $this->sendError('forbidden', Response::HTTP_FORBIDDEN);
        }

        $booking->status = LocationBookings::STATUS_APPROVED;
        $booking->save();
        return $this->sendData('booking declined');
    }
}
