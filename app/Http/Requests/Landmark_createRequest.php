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
            'sunday_open' => 'required|date_format:H:i',
            'sunday_close' => 'required|date_format:H:i|after:sunday_open',
            'monday_open' => 'required|date_format:H:i',
            'monday_close' => 'required|date_format:H:i|after:monday_open',
            'tuesday_open' => 'required|date_format:H:i',
            'tuesday_close' => 'required|date_format:H:i|after:tuesday_open',
            'wednesday_open' => 'required|date_format:H:i',
            'wednesday_close' => 'required|date_format:H:i|after:wednesday_open',
            'thursday_open' => 'required|date_format:H:i',
            'thursday_close' => 'required|date_format:H:i|after:thursday_open',
            'friday_open' => 'required|date_format:H:i',
            'friday_close' => 'required|date_format:H:i|after:friday_open',
            'saturday_open' => 'required|date_format:H:i',
            'saturday_close' => 'required|date_format:H:i|after:saturday_open',
            'egyptian_ticket' => 'required|integer',
            'egyptian_student_ticket' => 'required|integer',
            'foreign_ticket' => 'required|integer',
            'foreign_student_ticket' => 'required|integer',
            'booking' => 'nullable|string',
            'city_id' => 'required|exists:cities,id',
            'region' => 'required|string',
            'imgs' => 'required|array',
            'imgs.*' => 'image',
            'needTourguide'=>'required'
        ];
    }
}
