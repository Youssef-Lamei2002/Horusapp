<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use PharIo\Manifest\Email;

class RegisterRequest extends FormRequest
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
            $email = 'required|email|unique:tourists,email';
            $ssn   = 'nullable';
            $profile_pic = 'nullable|image|mimes:jpeg,png,jpg';

        }
        elseif (request()->email_type == 1)
        {
            $email = 'required|email|unique:tourguides,email';
            $ssn   = 'required|min:14|max:14|unique:tourguides,ssn';
            $profile_pic = 'required|image|mimes:jpeg,png,jpg';
        }
        return [
            'name' => 'required|min:3|max:255',
            'email' => $email,
            'password' => 'required|confirmed|string|min:8', // password_confirmation
            'gender' => 'required|in:0,1',
            'nationality' =>'required',
            'phone_number'=>'required',
            'profile_pic'  => $profile_pic,
            'languages'=>'required|array',
            'languages.*'=>'required|string|exists:languages,id',
            'ssn'=>$ssn,
        ];
    }
}