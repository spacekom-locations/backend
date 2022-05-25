<?php

namespace App\Http\Requests\Supervisor;

use Illuminate\Foundation\Http\FormRequest;

class IndexSupervisorsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // if the request ask to view roles check the permission for it
        $canViewRoles = true;
        if (request()->has('with_roles')) {
            if (boolval(request()->input('with_roles')) == true) {
                $canViewRoles = auth('supervisors')->user()->can('supervisors::view_role');
            }
        }
        $canViewSupervisors = auth('supervisors')->user()->can(['supervisors::retrieve']);
        return $canViewSupervisors and $canViewRoles;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'with_roles' => 'bool'
        ];
    }
}
