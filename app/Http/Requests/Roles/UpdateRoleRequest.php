<?php

namespace App\Http\Requests\Roles;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;

class UpdateRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth('supervisors')->user()->can('roles::update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // $userNameRules = 'min:3|unique:supervisors';
        // $supervisor = Supervisor::find(Route::getCurrentRoute()->parameter('id'));
        // if($supervisor){
        //     if(request()->has('user_name')){
        //         if (mb_strtolower(trim(request()->input('user_name'))) == $supervisor->user_name){
        //             $userNameRules = 'min:3';
        //         }
        //     }
        // }

        $nameRules = 'unique:roles,name';
        $role = Role::find(Route::getCurrentRoute()->parameter('id'));

        if($role and request()->has('name')){
            if($role->name == request()->input('name')){
                $nameRules = '';
            }
        }

        return [
            'name' => $nameRules,
            'permissions' => 'array'
        ];
    }
}
