<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Landmark_updateRequest extends FormRequest
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
            'name' => 'required|string',
            'description' => 'required|string',
            'rating' => 'required|numeric|min:0|max:5',
            'location' => 'required|string',
            'tourism_type' => 'required|string',
            'sunday_open' => 'nullable|date_format:H:i',
            'sunday_close' => 'nullable|date_format:H:i|after:sunday_open',
            'monday_open' => 'nullable|date_format:H:i',
            'monday_close' => 'nullable|date_format:H:i|after:monday_open',
            'tuesday_open' => 'nullable|date_format:H:i',
            'tuesday_close' => 'nullable|date_format:H:i|after:tuesday_open',
            'wednesday_open' => 'nullable|date_format:H:i',
            'wednesday_close' => 'nullable|date_format:H:i|after:wednesday_open',
            'thursday_open' => 'nullable|date_format:H:i',
            'thursday_close' => 'nullable|date_format:H:i|after:thursday_open',
            'friday_open' => 'nullable|date_format:H:i',
            'friday_close' => 'nullable|date_format:H:i|after:friday_open',
            'saturday_open' => 'nullable|date_format:H:i',
            'saturday_close' => 'nullable|date_format:H:i|after:saturday_open',
            'egyptian_ticket' => 'required|integer',
            'egyptian_student_ticket' => 'required|integer',
            'foreign_ticket' => 'required|integer',
            'foreign_student_ticket' => 'required|integer',
            'booking' => 'nullable|string',
            'region' => 'required|string',
            'city_id' => 'required|exists:cities,id',
            'landmark_id' => 'required|exists:landmarks,id',
            'imgs' => 'required|array',
            'imgs.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust max file size as needed
            'needTourguide'
        ];
    }
}
