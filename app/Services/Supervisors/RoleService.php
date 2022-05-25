<?php

namespace App\Services\Supervisors;

use App\Exceptions\Role\RoleNotFoundException;
use Spatie\Permission\Models\Role;

class RoleService
{
    /**
     * @method retrieve all roles from database
     * @param bool $withPermissions if true get the roles with its permissions
     * @param bool $user_name the unique user name
     * @param string $notes about the user
     * 
     * @return array contains the new added supervisor data
     */
    public function getAllRoles(bool $withPermissions = false, bool $withSupervisors = false)
    {
        $roles = Role::where('guard_name', 'supervisors');
        if ($withPermissions) {
            $roles = $roles->with('permissions');
        }
        if($withSupervisors){
            $roles = $roles->with('users');
        }

        $roles = $roles->get()->map(function ($item) use ($withSupervisors){
            if($withSupervisors){
                $item['supervisors'] = $item['users'];
                unset($item['users']);
            }
            return $item;
        });
        
        return $roles;
    }

    public function getRoleById(int $id, bool $withPermissions = false)
    {
        if ($withPermissions) {
            $role = Role::where('guard_name', 'supervisors')->with('permissions')->find($id);
        } else {
            $role = Role::where('guard_name', 'supervisors')->find($id);
        }

        if (!$role) {
            throw new RoleNotFoundException(__('roles.not_found', ['id' => $id]));
        }

        return $role;
    }

    public function getRolePermissions(int $id)
    {
        $role = Role::where('guard_name', 'supervisors')->find($id);
        if (!$role) {
            throw new RoleNotFoundException(__('roles.not_found', ['id' => $id]));
        }
        return Role::where('guard_name', 'supervisors')->with('permissions')->where('id', $id)->get()->pluck('permissions')->flatten()->pluck('name');
    }

    public function create(string $name, array $permissions = [])
    {
        $role = Role::create([
            'name' => $name,
            'guard_name' => 'supervisors'
        ]);

        $role->syncPermissions($permissions);

        return $role;
    }

    public function update(int $id, array $data, array $permissions = null)
    {
        $role = Role::where('guard_name', 'supervisors')->find($id);
        if (!$role) {
            throw new RoleNotFoundException(__('roles.not_found', ['id' => $id]));
        }

        if ($permissions) {
            $role->syncPermissions($permissions);
        }
        $role->update($data);

        return $role;
    }

    public function remove(int $id)
    {
        $role = Role::where('guard_name', 'supervisors')->find($id);
        if (!$role) {
            throw new RoleNotFoundException(__('roles.not_found', ['id' => $id]));
        }

        Role::where('guard_name', 'supervisors')->find($id)->delete();

        return $role;
    }

}
