<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StripeRequest extends FormRequest
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
        return [
            'card_name' => 'required|string|min:9|max:50',
            'card_number' => 'required|string|size:16',
            'exp_month' => 'required|string|date_format:m',
            'exp_year' => 'required|string|date_format:Y',
            'cvc' => 'required|string|min:3|max:4',
        ];
    }
}
