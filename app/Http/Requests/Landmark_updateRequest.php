<?php

namespace App\Http\Requests;

use App\Models\Landmark_Img;
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
            'sunday' => 'required|string',
            'monday' => 'required|string',
            'tuesday' => 'required|string',
            'wednesday' => 'required|string',
            'thursday' => 'required|string',
            'friday' => 'required|string',
            'saturday' => 'required|string',
            'egyptian_ticket' => 'required|string',
            'egyptian_student_ticket' => 'required|string',
            'foreign_ticket' => 'required|string',
            'foreign_student_ticket' => 'required|string',
            'booking' => 'nullable|string',
            'region' => 'required|string',
            'city_id' => 'required|exists:cities,id',
            'landmark_id' => 'required|exists:landmarks,id',
            'imgs' => 'required|array',
            'imgs.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust max file size as needed
        ];
    }
}
