<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => ['sometimes', 'string', 'email', 'unique:users,email'],
            'username' => ['sometimes', 'string', 'min:4', 'max:16', 'alpha_dash', 'unique:users,username'],
            'name' => ['sometimes', 'string', 'min:1', 'max:255'],
        ];
    }
}
