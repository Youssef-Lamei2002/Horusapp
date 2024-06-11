<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Reservation_tourguideRequest extends FormRequest
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
            'tourist_id' => 'required|exists:tourists,id',
            'tourguide_id' => 'required|exists:tourguides,id',
            'price_hour' => 'required',
            'hours' => 'required',
            'commission' => 'required',
            'isAccepted' => 'required|boolean',
            'starting_time' => 'required|date_format:Y-m-d H:i:s',
            'finished_time' => 'required|date_format:Y-m-d H:i:s|after:starting_time',
            'day' => 'required',
            'landmark_id'=>'required|exists:landmarks,id'
        ];
    }
}



