<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\BaseFormRequest;

class ResetPasswordRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => ['required', 'email', 'exists:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'token' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'email.exists' => 'Account does not exist.'
        ];
    }
}
