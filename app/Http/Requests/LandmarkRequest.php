<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LandmarkRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $name='required';
        $description='required';
        $rating='required|min:0|max:5';
        $location='required|location';
        $tourism_type='required';
        $sunday='required';
        $monday='required';
        $tuesday='required';
        $wednesday='required';
        $thursday='required';
        $friday='required';
        $saturday='required';
        $egyptian_ticket='required';
        $egyptian_student_ticket='required';
        $foreign_ticket='required';
        $foreign_student_ticket='required';
        $booking='required';
        $city_id='required|exists:cities,id';


        return [$name,$description,$rating,
        $location,$tourism_type,
        $sunday,$monday,$tuesday,
        $wednesday,$thursday,$friday,
        $saturday,$egyptian_ticket,$egyptian_student_ticket,
        $foreign_ticket,$foreign_student_ticket,$booking,$city_id
        ];
    }
}

