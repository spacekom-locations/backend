<?php

namespace App\Http\Controllers\Supervisors;

use App\Exceptions\Supervisor\InActiveSupervisorException;
use App\Exceptions\Supervisor\SupervisorNotFoundException;
use App\Exceptions\Supervisor\SupervisorWrongPasswordException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Supervisor\GetSupervisorAllPermissionsRequest;
use App\Http\Requests\Supervisor\GetSupervisorAllRolesRequest;
use App\Http\Requests\Supervisor\GetSystemPermissionsRequest;
use App\Http\Requests\Supervisor\IndexSupervisorsRequest;
use App\Http\Requests\Supervisor\LoginSupervisorRequest;
use App\Http\Requests\Supervisor\RemoveSupervisorRequest;
use App\Http\Requests\Supervisor\SetSupervisorRoleRequest;
use App\Http\Requests\Supervisor\ShowSupervisorRequest;
use App\Http\Requests\Supervisor\StoreSupervisorRequest;
use App\Http\Requests\Supervisor\UpdateSupervisorRequest;
use App\Models\Supervisor;
use App\Services\Supervisors\SupervisorService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\Permission\Models\Permission;

class SupervisorsController extends Controller
{
    private $supervisorService;

    public function getSystemPermissions(GetSystemPermissionsRequest $request)
    {
        $data = [
            'permissions' => Permission::where('guard_name', 'supervisors')->get()->pluck('name')
        ];
        return $this->sendData($data);
    }

    public function __construct()
    {
        $this->supervisorService = new SupervisorService();
    }
    /**
     * @method attempt to login from supervisor service
     */
    public function login(LoginSupervisorRequest $request)
    {
        $user_name = $request->input('user_name');
        $password = $request->input('password');
        $userAgent = $request->server('HTTP_USER_AGENT');
        $ip = $request->ip();


        try {
            $tokenName = [
                'ip' => $ip,
                'agent' => $userAgent
            ];
            $loginData = $this->supervisorService->login($user_name, $password, json_encode($tokenName));
        } catch (SupervisorNotFoundException | SupervisorWrongPasswordException $e) {
            $errors = [$e->getMessage()];
            return $this->sendError($errors, Response::HTTP_UNAUTHORIZED);
        } catch (InActiveSupervisorException $e) {
            $errors = [$e->getMessage()];
            return $this->sendError($errors, Response::HTTP_LOCKED);
        }


        $data = $loginData;
        $messages = ['login Successful', 'new token created for the current device'];
        return $this->sendData($data, $messages);
    }

    /**
     * @method logout revokes the current access token for the supervisor 
     */
    public function logout(Request $request)
    {
        $user = auth('supervisors')->user();
        $tokens = 'current';

        if ($request->has('tokens')) {
            $tokens = is_numeric($request->input('tokens')) ? intval($request->input('tokens')) : $request->input('tokens');
        }
        $this->supervisorService->logout($user, $tokens);
        $messages = [__('supervisors.logout_successful')];
        return $this->sendData([], $messages);
    }

    /**
     * @method store creates and stores new supervisor
     */
    public function store(StoreSupervisorRequest $request)
    {
        $name = $request->input('name');
        $user_name = $request->input('user_name');
        $password = $request->input('password');
        $notes = $request->input('notes');

        $supervisor = $this->supervisorService->create($name, $user_name, $password, $notes);

        $data = $supervisor;
        $messages = [
            __('supervisors.supervisor_created')
        ];
        return $this->sendData($data, $messages, Response::HTTP_CREATED);
    }

    /**
     * Get All Supervisors in the database
     * @method index
     * 
     */
    public function index(IndexSupervisorsRequest $request)
    {
        $withRoles = false;
        if ($request->has('with_roles')) {
            $withRoles = boolval($request->input('with_roles'));
        }
        $data = $this->supervisorService->getAllSupervisors($withRoles);
        return $this->sendData($data);
    }

    /**
     * get supervisor by id
     * @method show
     */
    public function show(ShowSupervisorRequest $request, $id)
    {

        $withRoles = boolval($request->input('with_roles'));

        try {
            if (intval($id) > 0) {
                $supervisor = $this->supervisorService->getSupervisorById($id, $withRoles);
            } elseif (is_string($id)) {
                $supervisor = $this->supervisorService->getSupervisorByUserName($id, $withRoles);
            } else return $this->sendError([], Response::HTTP_NOT_ACCEPTABLE);
        } catch (SupervisorNotFoundException $e) {
            $errors = [$e->getMessage()];
            return $this->sendError($errors, Response::HTTP_NOT_FOUND);
        }

        return $this->sendData($supervisor);
    }

    /**
     * Change supervisor account status to active
     */
    public function activate(Request $request, int $id)
    {
        $supervisor = Supervisor::find($id);
        if (!$supervisor) {
            $errors = [__('supervisors.not_found', ['user_name' => $id])];
            return $this->sendError($errors, Response::HTTP_NOT_FOUND);
        }
        $this->supervisorService->activateAccount($supervisor);
        $messages = [__('supervisors.activated_successfully')];
        $data = $supervisor;
        return $this->sendData($data, $messages);
    }

    /**
     * Change supervisor account status to suspended
     */
    public function suspend(Request $request, int $id)
    {

        $supervisor = Supervisor::find($id);
        if (!$supervisor) {
            $errors = [__('supervisors.not_found', ['user_name' => $id])];
            return $this->sendError($errors, Response::HTTP_NOT_FOUND);
        }
        $this->supervisorService->suspendAccount($supervisor);
        $messages = [__('supervisors.suspended_successfully')];
        $data = $supervisor;
        return $this->sendData($data, $messages);
    }

    /**
     * Update Supervisor data
     * 
     */
    public function update(UpdateSupervisorRequest $request, int $id)
    {

        $supervisor = Supervisor::find($id);
        if (!$supervisor) {
            $errors = [__('supervisors.not_found', ['user_name' => $id])];
            return $this->sendError($errors, Response::HTTP_NOT_FOUND);
        }

        #supervisor data for mass assignment
        $data = [];

        if ($request->has('name')) {
            $data['name'] = trim($request->input('name'));
        }
        if ($request->has('user_name')) {
            $data['user_name'] = mb_strtolower(trim($request->input('user_name')));
        }
        if ($request->has('notes')) {
            $data['notes'] = trim($request->input('notes'));
        }

        if ($request->has('password')) {
            $data['password'] = $request->input('password');
        }

        $supervisor = $this->supervisorService->update($supervisor, $data);

        $messages = [__('supervisors.updated_successfully')];
        $data = [
            'supervisor' => $supervisor
        ];
        return $this->sendData($data, $messages);
    }

    /**
     * Soft Deletes the supervisor
     */
    public function destroy(RemoveSupervisorRequest $request, int $id)
    {
        try {
            $permanentRemove = false;
            if ($request->has('permanent') && boolval($request->input('permanent')) == true) {
                $permanentRemove = true;
            }

            $supervisor = $this->supervisorService->removeById($id, $permanentRemove);
        } catch (SupervisorNotFoundException $e) {
            $errors = [$e->getMessage()];
            return $this->sendError($errors, Response::HTTP_NOT_FOUND);
        }
        if ($permanentRemove) {
            $messages = [__('supervisors.permanent_deleted_successfully')];
        } else {
            $messages = [__('supervisors.soft_deleted_successfully')];
        }
        $data = [
            'supervisor' => $supervisor
        ];
        return $this->sendData($data, $messages);
    }

    /**
     * Gets supervisor All Roles
     */
    public function roles(GetSupervisorAllRolesRequest $request, int $id)
    {
        $supervisor = Supervisor::find($id);
        if (!$supervisor) {
            $errors = [__('supervisors.not_found', ['user_name' => $id])];
            return $this->sendError($errors, Response::HTTP_NOT_FOUND);
        }
        $roles = $this->supervisorService->getRoles($supervisor);
        $messages = [];
        $data = $roles;
        return $this->sendData($data, $messages);
    }

    /**
     * Sets roles for the given supervisor
     */
    public function setRole(SetSupervisorRoleRequest $request, int $id)
    {
        $sync = false;
        if ($request->has('sync')) {
            $sync = boolval($request->input('sync'));
        }
        $supervisor = Supervisor::find($id);
        if (!$supervisor) {
            $errors = [__('supervisors.not_found', ['user_name' => $id])];
            return $this->sendError($errors, Response::HTTP_NOT_FOUND);
        }
        $roles = $this->supervisorService->setRoles($supervisor, $request->input('roles'), $sync);
        $data =  $roles;

        return $this->sendData($data);
    }

    /**
     * Sets roles for the given supervisor
     */
    public function revokeRoles(SetSupervisorRoleRequest $request, int $id)
    {
        $supervisor = Supervisor::find($id);
        if (!$supervisor) {
            $errors = [__('supervisors.not_found', ['user_name' => $id])];
            return $this->sendError($errors, Response::HTTP_NOT_FOUND);
        }
        $roles = $this->supervisorService->revokeRoles($supervisor, $request->input('roles'));
        $data = [
            'roles' => $roles
        ];

        return $this->sendData($data);
    }

    /**
     * get all permissions
     */
    public function getPermissions(GetSupervisorAllPermissionsRequest $request, int $id)
    {
        $supervisor = Supervisor::find($id);
        if (!$supervisor) {
            $errors = [__('supervisors.not_found', ['user_name' => $id])];
            return $this->sendError($errors, Response::HTTP_NOT_FOUND);
        }
        $permissions = $this->supervisorService->getPermissions($supervisor);

        return $this->sendData($permissions);
    }

    public function resetPassword(int $id)
    {
        $supervisor = Supervisor::find($id);
        if (!$supervisor) {
            $errors = [__('supervisors.not_found', ['user_name' => $id])];
            return $this->sendError($errors, Response::HTTP_NOT_FOUND);
        }
        $newPassword = $this->supervisorService->resetPassword($supervisor);

        return $this->sendData($newPassword);
    }
}
