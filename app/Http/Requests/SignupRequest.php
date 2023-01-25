<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SignupRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => ['required', 'string', 'email', 'unique:users,email'],
            'username' => ['required', 'string', 'min:4', 'max:16', 'alpha_dash', 'unique:users,username'],
            'name' => ['required', 'string', 'min:1', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
        ];
    }
}
