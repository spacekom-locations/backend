<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{

    /**
     * @method getSupervisorsPermissions gets all permissions corresponded to supervisors
     * @return array of all supervisors permissions
     */
    public static function getSupervisorsPermissions(): array
    {
        $permissions = [
            'supervisors::add',
            'supervisors::remove',
            'supervisors::retrieve',
            'supervisors::update',
            'supervisors::view_role',
            'supervisors::set_role',
            'supervisors::revoke_role',
            'supervisors::view_permissions',
        ];

        return $permissions;
    }

    /**
     * @method getRolesPermissions gets all permissions corresponded to roles
     * @return array of all roles permissions
     */
    public static function getRolesPermissions(): array
    {
        $permissions = [
            'roles::add',
            'roles::remove',
            'roles::retrieve',
            'roles::update',
            'roles::view_permissions',
            'roles::set_permissions',
        ];

        return $permissions;
    }

   
    /**
     * @method getSystemPermissions gets all valid permissions
     * @return array of all valid permissions
     */
    public static function getSystemPermissions(): array
    {
        $global = [
            'permissions::retrieve',
        ];
        $permissions = [
            $global,
            static::getSupervisorsPermissions(),
            static::getRolesPermissions(),
        ];

        $systemPermission = [];
        foreach ($permissions as $permission) {
            $systemPermission = array_merge($systemPermission, $permission);
        }

        return $systemPermission;
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $allPermissions = PermissionSeeder::getSystemPermissions();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $role = Role::find(RoleSeeder::rootSupervisor['id']);

        foreach ($allPermissions as $permissionName) {
            Permission::create(['name' => $permissionName, 'guard_name' => 'supervisors']);
            $role->givePermissionTo($permissionName);
        }
    }
}
