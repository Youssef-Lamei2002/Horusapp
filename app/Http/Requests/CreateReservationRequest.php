<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return True;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'tourist_id' => 'required|exists:tourists,id',
            'tourguide_id' => 'required|exists:tourguides,id',
            'landmark_id' => 'required|exists:landmarks,id',
            'hours' => 'required|min:1',
            'price_of_hour' => '',
            'isAccepted' => '',
            'isFinished' => '',
            'starting_time' => 'required|date_format:H:i',
            'finished_time' => 'required|date_format:H:i|after:starting_time',
            'day' => 'required|date',
        ];
    }
}
