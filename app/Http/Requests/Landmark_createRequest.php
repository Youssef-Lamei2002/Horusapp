<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Landmark_createRequest extends FormRequest
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
            'name' => 'required|unique:landmarks,name,' . $this->landmark_id,
            'description' => 'required',
            'rating' => 'required|numeric|min:0|max:5',
            'location' => 'required', 
            'tourism_type' => 'required',
            'sunday' => 'required',
            'monday' => 'required',
            'tuesday' => 'required',
            'wednesday' => 'required',
            'thursday' => 'required',
            'friday' => 'required',
            'saturday' => 'required',
            'egyptian_ticket' => 'required',
            'egyptian_student_ticket' => 'required',
            'foreign_ticket' => 'required',
            'foreign_student_ticket' => 'required',
            'booking' => '',
            'city_id' => 'required|exists:cities,id',
            'region' => 'required',
            'imgs' =>'required|array',
            'imgs.*'=>'image',
        ];

    }
}
