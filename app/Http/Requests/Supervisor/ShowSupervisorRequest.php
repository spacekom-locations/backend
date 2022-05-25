<?php

namespace App\Http\Requests\Supervisor;

use App\Models\Supervisor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class ShowSupervisorRequest extends FormRequest
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

        // current supervisor can view his own data 
        if (Route::getCurrentRoute()->getName() == "supervisors.getById"){   
            $id = intval(Route::getCurrentRoute()->parameters()['id']);
            $supervisor = Supervisor::find($id);
            if ($supervisor) {
                if (auth('supervisors')->user()->id == $id) {
                    return true;
                }
            }
        }

        // check if the current supervisor can view other supervisors data
        return Auth('supervisors')->user()->can('supervisors::retrieve') and $canViewRoles;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'per_page' => 'numeric|min:1'
        ];
    }
}
