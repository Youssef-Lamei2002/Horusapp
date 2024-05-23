<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Hotel_updateRequest extends FormRequest
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
            'name' => 'required|unique:hotels,name,',
            'description' => 'required',
            'rating' => 'required|numeric|min:0|max:5',
            'location' => 'required', 
            'city_id' => 'required|exists:cities,id',
            'region' => 'required',
            'imgs' => 'required|array',
            'imgs.*' => 'image',
            'booking_link'=>'array', 
            'booking_img_id'=>'array|exists:hotel__booking__imgs,id',
        ];
    }
}
