<?php

namespace App\Http\Requests\Supervisor;

use App\Models\Supervisor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class UpdateSupervisorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // current supervisor can update his own data
        $id = intval(Route::getCurrentRoute()->parameters()['id']);
        $supervisor = Supervisor::find($id);
        if ($supervisor) {
            if (auth('supervisors')->user()->id == $id) {
                return true;
            }
        }

        //check if current supervisor can update other supervisors data
        return Auth('supervisors')->user()->can('supervisors::update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // get over duplicated username error
        $userNameRules = 'min:3|unique:supervisors';
        $supervisor = Supervisor::find(Route::getCurrentRoute()->parameter('id'));
        if($supervisor){
            if(request()->has('user_name')){
                if (mb_strtolower(trim(request()->input('user_name'))) == $supervisor->user_name){
                    $userNameRules = 'min:3';
                }
            }
        }

        return [
            'name' => 'min:3',
            'user_name' => $userNameRules,
            'password' => 'min:8|confirmed',
            'notes' => '',
        ];
    }
}
