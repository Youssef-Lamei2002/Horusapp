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
        $rules = [
            'name' => 'required|min:3|max:255',
            'password' => 'required|confirmed|string|min:8', // password_confirmation
            'gender' => 'required|in:0,1',
            'phone_number' => 'required',

        ];

        if ($this->input('email_type') == 0) {
            $rules['email'] = 'required|email|unique:tourists,email';
            $rules['profile_pic'] = 'nullable|image|mimes:jpeg,png,jpg';
            $rules['nationality'] ='required';
        } elseif ($this->input('email_type') == 1) {
            $rules['email'] = 'required|email|unique:tourguides,email';
            $rules['ssn'] = 'required|min:14|max:14|unique:tourguides,ssn';
            $rules['profile_pic'] = 'required|image|mimes:jpeg,png,jpg';
            $rules['languages'] = 'required|array';
            $rules['languages.*'] = 'required|string|exists:languages,id';
            $rules['rate'] ='nullable';
            $rules['price'] ='required';
            $rules['isBlocked'] = '';
            $rules['isApproved'] = '';
            
        }

        return $rules;
    }
}