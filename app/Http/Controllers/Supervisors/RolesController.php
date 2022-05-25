<?php

namespace App\Http\Controllers\Supervisors;

use App\Exceptions\Role\RoleNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Roles\CreateNewRoleController;
use App\Http\Requests\Roles\CreateNewRoleRequest;
use App\Http\Requests\Roles\DestroyRoleRequest;
use App\Http\Requests\Roles\GetAllRolesRequest;
use App\Http\Requests\Roles\GetRolePermissions;
use App\Http\Requests\Roles\ShowRoleRequest;
use App\Http\Requests\Roles\UpdateRoleRequest;
use App\Services\Supervisors\RoleService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RolesController extends Controller
{
    /**
     * @var RoleService
     */
    protected $roleService;

    public function __construct()
    {
        $this->roleService = new RoleService();
    }

    /**
     * All roles
     */
    public function index(GetAllRolesRequest $request)
    {
        $withPermissions = false;
        $withSupervisors = false;
        if ($request->has('with_permissions')) {
            $withPermissions = boolval($request->input('with_permissions'));
        }

        if ($request->has('with_supervisors')) {
            $withSupervisors = boolval($request->input('with_supervisors'));
        }

        $roles = $this->roleService->getAllRoles($withPermissions, $withSupervisors);
        $data = $roles;
        return $this->sendData($data);
    }

    /**
     * Show one role
     */
    public function show(ShowRoleRequest $request, int $id)
    {
        $withPermissions = false;
        if ($request->has('with_permissions')) {
            $withPermissions = boolval($request->input('with_permissions'));
        }
        try {
            $role = $this->roleService->getRoleById($id, $withPermissions);
            $data = ["role" => $role];
            return $this->sendData($data);
        } catch (RoleNotFoundException $e) {
            $messages = [$e->getMessage()];
            return $this->sendError($messages, Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Get one role assigned permissions
     */
    public function getRolePermissions(GetRolePermissions $request, int $id)
    {
        try {
            $permissions = $this->roleService->getRolePermissions($id);
            $data = ["permissions" => $permissions];
            return $this->sendData($data);
        } catch (RoleNotFoundException $e) {
            $messages = [$e->getMessage()];
            return $this->sendError($messages, Response::HTTP_NOT_FOUND);
        }
    }


    public function create(CreateNewRoleRequest $request)
    {
        $permissions = [];

        if ($request->has('permissions')) {
            $permissions = $request->input('permissions');
        }

        $role =  $this->roleService->create($request->name, $permissions);

        $data = [
            "role" => $role
        ];

        return $this->sendData($data);
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        $data = [];

        if ($request->has('name')) {
            $data['name'] = $request->input('name');
        }

        $permissions = null;

        if ($request->has('permissions')) {
            $permissions = $request->input('permissions');
        }



        try {
            $role =  $this->roleService->update($id, $data, $permissions);

            $data = [
                "role" => $role
            ];

            return $this->sendData($data);
        } catch (RoleNotFoundException $e) {
            $messages = [$e->getMessage()];
            return $this->sendError($messages, Response::HTTP_NOT_FOUND);
        }
    }

    public function destroy(DestroyRoleRequest $request, int $id)
    {
        try {
            $role =  $this->roleService->remove($id);

            $data = [
                "role" => $role
            ];

            return $this->sendData($data);
        } catch (RoleNotFoundException $e) {
            $messages = [$e->getMessage()];
            return $this->sendError($messages, Response::HTTP_NOT_FOUND);
        }
    }
}
