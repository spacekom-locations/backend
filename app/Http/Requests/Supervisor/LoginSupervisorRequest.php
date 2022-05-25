<?php

namespace App\Http\Requests\Supervisor;

use Illuminate\Foundation\Http\FormRequest;

class LoginSupervisorRequest extends FormRequest
{
    public const loginValidationRules = [
        'user_name' => 'required',
        'password' => 'required'
    ];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {

        // ever one has the right to login
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return static::loginValidationRules;
    }
}
