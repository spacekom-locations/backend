<?php

namespace App\Http\Requests\Roles;

use Illuminate\Foundation\Http\FormRequest;

class GetAllRolesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {

        /* 
            the request issuer (supervisor) must have three permissions
            1. roles::retrieve (can view roles)
            2. roles::view_permissions (can view role permissions)
            3. supervisors::view_role ( can view a supervisor roles)
        */ 
        
        $canViewPermissions = true;
        $canViewSupervisors = true;
        $canRetrieveRoles = auth('supervisors')->user()->can('roles::retrieve');

        if (request()->has('with_permissions')) {
            if (boolval(request()->input('with_permissions')) == true) {
                $canViewPermissions = auth('supervisors')->user()->can('roles::view_permissions');
            }
        }

        if (request()->has('with_supervisors')) {
            if (boolval(request()->input('with_supervisors')) == true) {
                $canViewSupervisors = auth('supervisors')->user()->can('supervisors::view_role');
            }
        }

        return $canRetrieveRoles and $canViewPermissions and $canViewSupervisors;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'with_permissions' => 'bool',
            'with_supervisors' => 'bool'
        ];
    }
}
