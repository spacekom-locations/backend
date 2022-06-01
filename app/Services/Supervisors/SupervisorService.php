<?php

namespace App\Services\Supervisors;

use App\Exceptions\Supervisor\InActiveSupervisorException;
use App\Exceptions\Supervisor\SupervisorNotFoundException;
use App\Exceptions\Supervisor\SupervisorWrongPasswordException;
use App\Models\Supervisor;
use Illuminate\Support\Facades\Hash;

class SupervisorService
{

    /**
     * @var Supervisor $supervisor the current supervisor model
     */
    private $supervisor;

    /**
     * @method create adds new supervisor model to the database
     * @param string $name the name of the supervisor
     * @param string $user_name the unique user name
     * @param string $notes about the user
     * 
     * @return array contains the new added supervisor data
     */
    public function create(string $name, string $user_name, string $password = null, $notes = null): array
    {
        $name = trim($name);
        $user_name = trim(mb_strtolower($user_name));
        if ($notes) {
            $notes = trim($notes);
        }

        if (!$password) {
            // Generate new password
            $password = $this->generateRandomPassword();
        }

        $hashedPassword = Hash::make($password);

        // supervisor data 
        $supervisorData = [
            'name' => $name,
            'user_name' => $user_name,
            'password' => $hashedPassword,
            'notes' => $notes
        ];

        $supervisor = new Supervisor($supervisorData);
        $supervisor->save();

        $supervisorData['password'] = $password;
        $supervisorData['id'] = $supervisor->id;

        return $supervisorData;
    }

    /**
     * Gets all registered Supervisors in the database
     * @method getAllSupervisors
     * 
     * @param int perPage number of records per page when using paginator
     * 
     * @return collection of supervisors
     */
    public function getAllSupervisors(bool $withRoles = false, int $perPage = -1)
    {
        // if ($perPage > 0) {
        //     return Supervisor::paginate($perPage);
        // }
        $supervisors = new Supervisor();
        if ($withRoles) {
            $supervisors = $supervisors->with('roles');
        }
        $supervisors = $supervisors->get()->map(function ($item) {
            $item['last_activity'] = $item->tokens()->orderBy('last_used_at', 'DESC')->pluck('last_used_at')->first();
            return $item;
        });
        return $supervisors;
    }

    /**
     * Gets a supervisor from the database by its id
     * @method getSupervisorById
     * 
     * @param int $id the id of the supervisor
     * 
     * @throws SupervisorNotFoundException if the given id is'nt a supervisor $id
     * 
     * @return Supervisor model
     */
    public function getSupervisorById(int $id, $withRoles = false)
    {

        $supervisor = new Supervisor();

        if ($withRoles) {
            $supervisor = $supervisor->with('roles');
        }

        $supervisor = $supervisor->where('id', $id)->first();
        if (!$supervisor) {
            throw new SupervisorNotFoundException(__('supervisors.not_found', ['user_name' => $id]));
        }

        return $supervisor;
    }

    public function getSupervisorByUserName(string $userName, $withRoles)
    {
        $supervisor = new Supervisor();

        if ($withRoles) {
            $supervisor = $supervisor->with('roles');
        }

        $supervisor = $supervisor->where('user_name', $userName)->first();

        if (!$supervisor) {
            throw new SupervisorNotFoundException(__('supervisors.not_found', ['user_name' => $userName]));
        }

        return $supervisor;
    }

    /**
     * @method authenticate
     * checks if the given credentials is a valid
     * 
     * @param string $user_name : supervisor user_name
     * @param string $password : supervisor password
     * 
     * @throws SupervisorNotFoundException if the supervisor model is'nt found in the database
     * 
     * @return false: True if the given credentials is valid, False otherwise 
     */
    public function authenticate(string $user_name, string $password): bool
    {
        // clean up user name
        $user_name = mb_strtolower(trim($user_name));

        // get the user model
        $supervisor = Supervisor::where('user_name', $user_name)->first();

        // Check if the model if found
        if (!$supervisor) {
            $notFoundMessage = __('supervisors.not_found', ['user_name' => $user_name]);
            throw new SupervisorNotFoundException($notFoundMessage);
        }

        // match the password

        if (Hash::check($password, $supervisor->password)) {
            return true;
        }

        // wrong credentials return false
        return false;
    }

    /**
     * @method login authenticates the supervisor and creates personal access token
     * @param string $user_name the supervisor user name
     * @param string $password the supervisor password
     * @param string $userAgent user connected medium details
     * 
     * @return array $personalAccessToken
     */
    public function login(string $user_name, string $password, string $userAgent): array
    {
        # check user credentials

        if ($this->authenticate($user_name, $password)) {
            $this->supervisor = Supervisor::where('user_name', $user_name)->first();
            if (!$this->supervisor->isActive()) {
                throw new InActiveSupervisorException(__('supervisors.in_active_account'));
            }
        } else {
            $wrongPasswordMessage = __('supervisors.wrong_password', ['user_name' => $user_name]);
            throw new SupervisorWrongPasswordException($wrongPasswordMessage);
        };

        # create token

        $token = $this->supervisor->createToken($userAgent);

        # return plain text token

        return [
            "token" => $token->plainTextToken,
            "supervisor" => $this->supervisor
        ];
    }


    /**
     * @method logout revokes the current access token
     * @param Supervisor $supervisor to deal with logout for
     * @param int|array|string $tokens to be revoked
     */
    public function logout(Supervisor $supervisor, $tokens = 'current')
    {

        if (!$tokens) {
            $tokens = 'current';
        }

        $tokenType = gettype($tokens);
        // if the given token is int just revoke it
        if ($tokenType == 'integer') {
            return $this->revokeAccessTokenById($supervisor, $tokens);
        }

        // if the given tokens is array revoke them
        elseif ($tokenType == 'array') {

            return $this->revokeAccessTokens($supervisor, $tokens);
        }

        // else if the given token is a phrase
        $tokens = mb_strtolower($tokens);
        switch ($tokens) {
            case '*':
            case 'all':
                return $this->revokeAllAccessTokens($supervisor);
                break;
            case 'other':
            case 'others':
                return $this->revokeOtherAccessTokens($supervisor);
                break;
            case 'current':
            default:
                return $this->revokeCurrentAccessToken($supervisor);
                break;
        }
    }

    /**
     * Revoke all personal access tokens for the given supervisor
     * @method revokeAllAccessTokens()
     * @param Supervisor $supervisor to revoke his personal access tokens
     */
    public function revokeAllAccessTokens(Supervisor $supervisor)
    {
        return $supervisor->tokens()->delete();
    }

    /**
     * Revoke the current used personal access token
     * @method revokeAllAccessTokens()
     * @param Supervisor $supervisor to revoke his current used personal access token
     */
    public function revokeCurrentAccessToken(Supervisor $supervisor)
    {
        return $supervisor->currentAccessToken()->delete();
    }

    /**
     * Revoke access token with the id
     * @method revokeAccessTokenById()
     * @param Supervisor $supervisor to revoke his personal access token
     * @param int $tokenId
     */
    public function revokeAccessTokenById(Supervisor $supervisor, int $tokenId)
    {
        return $supervisor->tokens()->where('id', $tokenId)->delete();
    }

    /**
     * Revoke bulk access tokens
     * @method revokeAccessTokens()
     * @param Supervisor $supervisor to revoke his personal access tokens
     * @param array $tokens array of tokens ids to be revoked
     */
    public function revokeAccessTokens(Supervisor $supervisor, array $tokens)
    {
        // dd($tokens);
        foreach ($tokens as $tokenId) {
            $supervisor->tokens()->where('id', $tokenId)->delete();
        }
    }

    /**
     * Revoke all access tokens except for current used personal access token
     * @method revokeOtherAccessTokens()
     * @param Supervisor $supervisor to revoke his current used personal access token
     */
    public function revokeOtherAccessTokens(Supervisor $supervisor, $accessTokenId = null)
    {
        if (!$accessTokenId) {
            $accessTokenId = auth('supervisors')->user()->currentAccessToken()->id;
        }
        return $supervisor->tokens()->where('id', '!=', $accessTokenId)->delete();
    }

    /**
     * @method generateRandomString to generate random password
     * @param int length the length of generated password
     */
    public static function generateRandomPassword(int $length = 16)
    {
        $characters = '0123456789#@$&!abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Resets the password for hte account
     */
    public function resetPassword(Supervisor $supervisor)
    {
        $newPassword = $this->generateRandomPassword();
        $hashedPassword = Hash::make($newPassword);
        $supervisor->update(['password' => $hashedPassword]);
        $this->revokeAllAccessTokens($supervisor);

        return $newPassword;
    }

    /**
     * Suspends Supervisor account
     */
    public function suspendAccount(Supervisor $supervisor)
    {
        $supervisor->suspendAccount();
        $this->revokeAllAccessTokens($supervisor);
    }

    /**
     * Activates Supervisor account
     */
    public function activateAccount(Supervisor $supervisor)
    {
        $supervisor->activateAccount();
    }

    /**
     * Update supervisor to  new data
     * @param Supervisor $supervisor to update his data
     * @param array $data the data to be updated
     * 
     * 
     * @return Supervisor the new updated version of the given supervisor
     */
    public function update(Supervisor $supervisor, array $data)
    {
        if (array_key_exists('password', $data)) {
            $data['password'] = Hash::make($data['password']);

            #if the password has changed logout from other devices
            if (auth('supervisors')->user()->id == $supervisor->id) {
                $this->logout($supervisor, 'others');
            } else {
                $this->logout($supervisor, 'all');
            }
        }
        $supervisor->update($data);
        return $supervisor->refresh();
    }

    /**
     * deletes a supervisor by the given id
     * @param int id the supervisor id
     * 
     * @throws SupervisorNotFoundException if the supervisor is'nt found
     */
    public function removeById(int $id, $permanentRemoval = false)
    {
        $supervisor = Supervisor::withTrashed()->find($id);
        if (!$supervisor) {
            throw new SupervisorNotFoundException(__('supervisors.not_found', ['user_name' => $id]));
        }

        $this->logout($supervisor, 'all');

        if ($permanentRemoval) {
            Supervisor::withTrashed()->find($id)->forceDelete();
        } else {
            #soft delete the supervisor
            Supervisor::withTrashed()->find($id)->delete();
        }

        return $supervisor;
    }

    /**
     * Get the supervisor roles
     */
    public function getRoles(Supervisor $supervisor)
    {
        return $supervisor->roles()->get();
    }

    /**
     * Sets roles for the supervisor
     */
    public function setRoles(Supervisor $supervisor, array $roles, bool $sync = false)
    {
        if ($sync) {
            $supervisor->syncRoles($roles);
        } else {
            $supervisor->assignRole($roles);
        }
        return $supervisor->roles;
    }

    /**
     * Sets roles for the supervisor
     */
    public function revokeRoles(Supervisor $supervisor, array $roles)
    {
        foreach ($roles as $role) {
            $supervisor->removeRole($role);
        }
        return $supervisor->roles;
    }

    /**
     * Get the granted permissions for the supervisor
     */
    public function getPermissions(Supervisor $supervisor)
    {
        return $supervisor->roles()->with('permissions')->get()->pluck('permissions')->flatten()->pluck('name')->unique();
    }
}
