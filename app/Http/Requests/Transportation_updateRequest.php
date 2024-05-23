<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Transportation_updateRequest extends FormRequest
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
            'name' => 'required|unique:transportations,name,',
            'description' => 'required',
            'sunday' => 'required',
            'monday' => 'required',
            'tuesday' => 'required',
            'wednesday' => 'required',
            'thursday' => 'required',
            'friday' => 'required',
            'saturday' => 'required',
            'lines_img'=>'required|image',
            'transportation_img'=>'required|image',
            'prices'=>'required',
            'city_id'=>'required'
        ];
    }
}
