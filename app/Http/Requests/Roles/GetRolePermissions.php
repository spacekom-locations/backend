<?php

namespace App\Http\Requests\Roles;

use Illuminate\Foundation\Http\FormRequest;

class GetRolePermissions extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
         /* 
            the request issuer (supervisor) must have the following permissions
            1. roles::retrieve (can view roles)
            2. roles::view_permissions (can view role permissions)
        */ 
        $canViewPermissions = true;
        $canRetrieveRoles = auth('supervisors')->user()->can('roles::retrieve');

        if(request()->has('with_permissions')){
            if(boolval(request()->input('with_permissions')) == true){
                $canViewPermissions = auth('supervisors')->user()->can('roles::view_permissions');
            }
        }

        return $canRetrieveRoles and $canViewPermissions;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
