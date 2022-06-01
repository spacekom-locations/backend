<?php

namespace App\Http\Requests\Users;

use App\Models\User;
use App\Rules\MatchOldPassword;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateUser extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        //if connected client is user account return true
        if (Auth::guard('users')->check()) return true;
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $currentPasswordRules = [];
        $request = request();

        if ($request->has('password')) {
            $currentPasswordRules = ["required", new MatchOldPassword];
        }
        
        return [
            "name" => 'min:3',
            "email" => 'unique:users|email',
            "user_name" => 'unique:users',
            "phone_number" => 'unique:users',
            "avatar" => "image|mimes:jpeg,png,jpg,gif|dimensions:ratio=1:1",
            "category" => "in:" . join(',', User::validCategories()),
            "password" => "min:8|confirmed",
            "current_password" => $currentPasswordRules
        ];
    }
}
