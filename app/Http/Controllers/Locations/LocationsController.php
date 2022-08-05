<?php

namespace App\Http\Controllers\Locations;

use App\Http\Controllers\Controller;
use App\Http\Requests\Locations\StoreLocationRequest;
use App\Http\Requests\Locations\UpdateLocationRequest;
use App\Models\Location;
use App\Models\LocationBookings;
use App\Models\Messenger\Thread;
use App\Services\Locations\LocationService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LocationsController extends Controller
{
    public function store(StoreLocationRequest $request)
    {
        $user = auth('users')->user();

        $location = LocationService::create(
            $user,
            $request->input('country'),
            $request->input('state'),
            $request->input('city'),
            $request->input('street'),
            $request->input('zip_code'),
            $request->input('apt_suite')
        );

        return $this->sendData($location, [], Response::HTTP_CREATED);
    }

    public function update(UpdateLocationRequest $request, string $id)
    {
        $location = Location::find($id);
        if (!$location) {
            return $this->sendError(['this location is not found'], Response::HTTP_NOT_FOUND);
        }
        $location = LocationService::update($location, $request->all());
        return $this->sendData($location);
    }

    public function search(Request $request)
    {
        $activity = $request->input('activity');
        $query = $request->input('query');
        $filters = $request->except('activity', 'query');
        if ($query) {
            $filters['types'] = $query;
            $filters['styles'] = $query;
            $filters['features'] = $query;
        }
        $locations = LocationService::indexWithActivityAndFilters($activity, $filters);
        return $this->sendData($locations);
    }

    public function indexPopular(Request $request)
    {
        return $this->sendData(Location::take(6)->get());
    }

    public function show(string $id)
    {
        $location = Location::with('user')->where('id', $id)->first();
        if (!$location) {
            return $this->sendError(['location not found'], Response::HTTP_NOT_FOUND);
        }
        
        
        $location['has_active_booking'] = false;
        $location['has_active_messages_thread'] = false;
        
        $user = auth('users')->user();
        if ($user) {
            $thread = Thread::where('location_id', $location->id)->whereHas('participants', function ($query) use ($user) {
                return $query->where('user_id', '=', $user->id);
            })->first();

            if ($thread) {
                $location['has_active_messages_thread'] = true;
                $location['thread'] = $thread;
            }

            $booking = LocationBookings::where('user_id', $user->id)
                ->where('location_id', $location->id)
                ->whereIn('status', [LocationBookings::STATUS_PENDING, LocationBookings::STATUS_APPROVED])->first();
            if ($booking) {
                $location['has_active_booking'] = true;
            }
        }

        return $this->sendData($location);
    }

    public function indexByUserId(string $id)
    {
        $locations = LocationService::indexWithFilters([
            'user_id' => $id
        ]);
        return $this->sendData($locations);
    }

    public function indexUserLocations(string $id)
    {
        $locations = LocationService::indexWithFilters([
            'user_id' => $id
        ]);
        return $this->sendData($locations);
    }
}
