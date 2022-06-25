<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Locations\DeleteLocationImageRequest;
use App\Http\Requests\Locations\StoreLocationImageRequest;
use App\Http\Requests\Users\LoginRequest;
use App\Http\Requests\Users\SingUpRequest;
use App\Http\Requests\Users\UpdateUser;
use App\Models\Location;
use App\Models\User;
use App\Services\Locations\LocationService;
use App\Services\Users\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class UsersController extends Controller
{
    /**
     * Register new user
     */
    public function signUp(SingUpRequest $request)
    {
        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');

        $isSignedUp = UserService::singUp($name, $email, $password);

        if ($isSignedUp) {
            return $this->sendData(
                null,
                [__('users.created')],
                Response::HTTP_CREATED
            );
        }
        return $this->sendError(__('users.creation_failed'));
    }

    /**
     * login create personal access token for new session
     */
    public function login(LoginRequest $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');
        $userAgent = $request->server('HTTP_USER_AGENT');
        $ip = $request->ip();

        $tokenName = [
            'ip' => $ip,
            'agent' => $userAgent
        ];

        $loginData = UserService::login($email, $password, json_encode($tokenName));

        if ($loginData) {
            $successMessages = [
                __('users.login_successful')
            ];
            return $this->sendData($loginData, $successMessages);
        }

        $failMessages = [
            __('users.login_failed')
        ];

        return $this->sendError($failMessages, Response::HTTP_BAD_REQUEST);
    }


    public function logout(Request $request)
    {
        $user = auth('users')->user();
        $tokens = 'current';

        if ($request->has('tokens')) {
            $tokens = is_numeric($request->input('tokens')) ? intval($request->input('tokens')) : $request->input('tokens');
        }
        UserService::logout($user, $tokens);
        $messages = [__('users.logout_successful')];
        return $this->sendData([], $messages);
    }

    public function show(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->sendError(__('misc.not_found'), Response::HTTP_NOT_FOUND);
        }

        return $this->sendData($user);
    }

    public function update(UpdateUser $request)
    {
        $user = auth('users')->user();
        $userData = [
            'name' => null,
            'email' => null,
            'password' => null,
            'user_name' => null,
            'avatar' => null,
            'company' => null,
            'category' => null,
            'bio' => null,
            'phone_number' => null,
            'notes' => null
        ];
        if ($request->has('name')) $userData['name'] = $request->input('name');
        if ($request->has('email')) $userData['email'] = $request->input('email');
        if ($request->has('password')) $userData['password'] = $request->input('password');
        if ($request->has('user_name')) $userData['user_name'] = $request->input('user_name');
        if ($request->has('company')) $userData['company'] = $request->input('company');
        if ($request->has('category')) $userData['category'] = $request->input('category');
        if ($request->has('bio')) $userData['bio'] = $request->input('bio');
        if ($request->has('phone_number')) $userData['phone_number'] = $request->input('phone_number');
        if ($request->has('notes')) $userData['notes'] = $request->input('notes');

        if ($request->hasFile('avatar')) {
            $name = uniqid() . '.' . $request->avatar->extension();
            $request->avatar->storeAs('public/images/users/avatar', $name);
            $imageURL = url("storage/images/users/avatar/" . $name);
            $userData['avatar'] = $imageURL;

            Storage::delete('public/images/users/avatar/' .  basename($user->avatar));
        };

        $newUser = UserService::update($user->id, $userData);
        return $this->sendData($newUser);
    }

    // public function createPersonalAccessToken(Request $request)
    // {
    //     $user = auth('users')->user();
    //     $token = $user->createToken('API'); // public const API_TOKEN_NAME = "API";
    //     $loginData = [
    //         "token" => $token->plainTextToken,
    //         "user" => $user
    //     ];
    //     $messages = ['new personal access token created'];
    //     return $this->sendData($loginData, $messages);
    // }


    public function removePersonalAccessToken(Request $request, int $id)
    {
        $user = auth('users')->user();
        $token = $user->tokens()->where('id', intval($id))->delete();
        return $this->sendData(['token' => $token], ['success']);
    }

    public function getAllPersonalAccessToken()
    {
        $user = auth('users')->user();
        $tokens = $user->tokens;
        $tokensData = [];
        foreach ($tokens as $token) {
            $tokensData[] = [
                'created_at' => $token['created_at'],
                'id' => $token['id'],
                'last_used_at' => $token['last_used_at'],
                'device' => json_decode($token['name'])
            ];
        }
        return $this->sendData(['tokens' => $tokensData]);
    }

    public function indexLocations()
    {
        $locations = LocationService::indexWithFilters([
            'user_id' => auth('users')->user()->id
        ]);
        return $this->sendData($locations);
    }

    public function showLocation(string $id)
    {
        $location = Location::with('user')->where('id', $id)->where('user_id', auth('users')->user()->id)->first();
        if (!$location) {
            return $this->sendError(['location not found']);
        }

        return $this->sendData($location);
    }

    public function addLocationImages(StoreLocationImageRequest $request, string $id)
    {
        $location = Location::where('id', $id)->where('user_id', auth('users')->user()->id)->first();
        if (!$location) {
            return $this->sendError(['location not found'], Response::HTTP_NOT_FOUND);
        }

        $images = $request->file('image');
        $location = LocationService::uploadImages($location, $images);
        return $this->sendData($location);
    }

    public function removeLocationImage(DeleteLocationImageRequest $request, string $id)
    {
        $location = Location::where('id', $id)->where('user_id', auth('users')->user()->id)->first();
        if (!$location) {
            return $this->sendError(['location not found']);
        }
        $location = LocationService::removeImage($location, $request->image);
        return $this->sendData($location);
    }
}
