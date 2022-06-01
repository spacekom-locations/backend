<?php

namespace App\Services\Users;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * @method singUp create new user account
     * @param string $name the name of the user
     * @param string $email the user email
     * @param string $password the user password
     * 
     * @return bool true if account created successfully and false otherwise
     */
    public static function singUp(string $name, string $email, string $password): bool
    {
        // clean up user email
        $email = mb_strtolower(trim($email));

        //hash password
        $hashedPassword = Hash::make($password);

        $userData = [
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword,
        ];

        $createdUser = User::create($userData);
        if ($createdUser) {
            return true;
        }
        return false;
    }

    /**
     * @method authenticate
     * checks if the given credentials is a valid
     * 
     * @param string $email : user email
     * @param string $password : user password
     * 
     * @return User|false: user data if the given credentials is valid, False otherwise 
     */
    public static function authenticate(string $email, string $password)
    {
        // clean up user email
        $email = mb_strtolower(trim($email));

        // get the user model
        $user = User::where('email', $email)->first();

        // Check if the model if found (if not found return false )
        // and the given password matches user hashed password
        if ($user and Hash::check($password, $user->password)) {
            return $user;
        }

        // wrong credentials return false
        return false;
    }

    /**
     * @method login authenticates the user and creates personal access token
     * @param string $email the user email
     * @param string $password the user password
     * @param string $userAgent user connected medium details
     * 
     * @return array|false $personalAccessToken and user data or false if the user is not valid
     */
    public static function login(string $email, string $password, string $userAgent)
    {
        # check user credentials

        $user = static::authenticate($email, $password);

        // if not authenticated user or not a user or not active user account return false
        if (!$user or !($user instanceof User) or !$user->isActive()) {
            return false;
        }

        //create new access token
        $token = $user->createToken($userAgent);

        # return plain text token and user data
        return [
            "token" => $token->plainTextToken,
            "user" => $user
        ];
    }



    /**
     * @method logout revokes access tokens
     * @param user $user to deal with logout for
     * @param int|array|string $tokens to be revoked
     */
    public static function logout(User $user, $tokens = 'current')
    {

        if (!$tokens) {
            $tokens = 'current';
        }

        $tokenType = gettype($tokens);
        // if the given token is int just revoke it
        if ($tokenType == 'integer') {
            return static::revokeAccessTokenById($user, $tokens);
        }

        // if the given tokens is array revoke them
        elseif ($tokenType == 'array') {

            return static::revokeAccessTokens($user, $tokens);
        }

        // else if the given token is a phrase
        $tokens = mb_strtolower($tokens);
        switch ($tokens) {
            case '*':
            case 'all':
                return static::revokeAllAccessTokens($user);
                break;
            case 'other':
            case 'others':
                return static::revokeOtherAccessTokens($user);
                break;
            case 'current':
            default:
                return static::revokeCurrentAccessToken($user);
                break;
        }
    }

    /**
     * Revoke all personal access tokens for the given user
     * @method revokeAllAccessTokens()
     * @param User $user to revoke his personal access tokens
     */
    public static function revokeAllAccessTokens(User $user)
    {
        return $user->tokens()->delete();
    }

    /**
     * Revoke the current used personal access token
     * @method revokeAllAccessTokens()
     * @param User $user to revoke his current used personal access token
     */
    public static function revokeCurrentAccessToken(User $user)
    {
        return $user->currentAccessToken()->delete();
    }

    /**
     * Revoke access token with the id
     * @method revokeAccessTokenById()
     * @param User $user to revoke his personal access token
     * @param int $tokenId
     */
    public static function revokeAccessTokenById(User $user, int $tokenId)
    {
        return $user->tokens()->where('id', $tokenId)->delete();
    }

    /**
     * Revoke bulk access tokens
     * @method revokeAccessTokens()
     * @param User $user to revoke his personal access tokens
     * @param array $tokens array of tokens ids to be revoked
     */
    public static function revokeAccessTokens(User $user, array $tokens)
    {
        foreach ($tokens as $tokenId) {
            $user->tokens()->where('id', $tokenId)->delete();
        }
    }

    /**
     * Revoke all access tokens except for current used personal access token
     * @method revokeOtherAccessTokens()
     * @param User $user to revoke his current used personal access token
     */
    public static function revokeOtherAccessTokens(User $user, $accessTokenId = null)
    {
        if (!$accessTokenId) {
            $accessTokenId = auth('users')->user()->currentAccessToken()->id;
        }
        return $user->tokens()->where('id', '!=', $accessTokenId)->delete();
    }

    /**
     * update user data in the database
     * @method update
     * @param string $id user id
     * @param array $userData the new data of the user
     * @return false|User false if user could not be updated or the new updated user
     */
    public static function update(string $id, array $userData)
    {
        $user = User::find($id);
        if (!$user) return false;

        if ($userData['name']) {
            $user->name = $userData['name'];
        }

        if ($userData['email'] && mb_strtolower(trim($userData['email']) != $user->email)) {
            $user->email = mb_strtolower(trim($userData['email']));
            $user->email_verified_at = null;
        }

        if ($userData['phone_number'] && mb_strtolower(trim($userData['phone_number']) != $user->phone_number)) {
            $user->phone_number = mb_strtolower(trim($userData['phone_number']));
            $user->phone_number_verified_at = null;
        }

        if ($userData['password']) {
            $user->password = Hash::make($userData['password']);
            static::revokeOtherAccessTokens($user);
        }

        if ($userData['user_name'] && mb_strtolower(trim($userData['user_name']) != $user->user_name)) {
            $user->user_name = mb_strtolower(trim($userData['user_name']));
        }

        if ($userData['avatar']) {
            $user->avatar = $userData['avatar'];
        }

        if ($userData['company']) {
            $user->company = $userData['company'];
        }

        if ($userData['category']) {
            $user->category = $userData['category'];
        }

        if ($userData['bio']) {
            $user->bio = $userData['bio'];
        }

        if ($userData['notes']) {
            $user->notes = $userData['notes'];
        }

        $user->save();
        return $user->fresh();
    }
}
