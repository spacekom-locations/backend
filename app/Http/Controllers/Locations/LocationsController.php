<?php

namespace App\Http\Controllers\Locations;

use App\Http\Controllers\Controller;
use App\Http\Requests\Locations\StoreLocationRequest;
use App\Http\Requests\Locations\UpdateLocationRequest;
use App\Models\Location;
use App\Services\Locations\LocationService;
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
    
}
