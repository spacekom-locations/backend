<?php

namespace App\Http\Requests\Supervisor;

use App\Models\Supervisor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class GetSupervisorAllPermissionsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // supervisor can view his own permissions

        $id = intval(Route::getCurrentRoute()->parameters()['id']);
        $supervisor = Supervisor::find($id);
        if($supervisor) {
            if(auth('supervisors')->user()->id == $id){
                return true;
            }
        }
        
        // for other supervisors accounts (not current supervisor)
        // check if the current supervisor has the permission to view it
        return Auth('supervisors')->user()->can('supervisors::view_permissions');
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
