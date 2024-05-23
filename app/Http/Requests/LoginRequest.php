<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        if (request()->email_type == 0)
        {
            $email = 'required|email|exists:tourists,email';
        }
        elseif (request()->email_type == 1)
        {
            $email = 'required|email|exists:tourguides,email';
        }
        return [
            'email' => $email,
            'password' => 'required|string|min:8', // password_confirmation
        ];
    }
}
