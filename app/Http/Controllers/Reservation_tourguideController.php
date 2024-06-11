<?php

namespace App\Http\Controllers;

use App\Models\Reservation_tourguide;
use App\Models\Tourguide;
use Illuminate\Http\Request;

class Reservation_tourguideController extends Controller
{
    public function createReservation(Request $request)
    {
        // Validate the request data if necessary
        $request->validate([
            'tourist_id' => 'required|exists:tourists,id',
            'tourguide_id' => 'required|exists:tourguides,id',
            'landmark_id' => 'required|exists:landmarks,id',
            'hours' => 'required|numeric',
            'starting_time' => 'required|date_format:H:i:s',
            'finished_time' => 'required|date_format:H:i:s',
            'day' => 'required|date_format:Y-m-d',
        ]);
    
        // Fetch the necessary information from the request
        $touristId = $request->input('tourist_id');
        $tourguideId = $request->input('tourguide_id');
        $landmarkId = $request->input('landmark_id');
        $hours = $request->input('hours');
        $startingTime = $request->input('starting_time');
        $finishedTime = $request->input('finished_time');
        $day = $request->input('day');
    
        // Fetch price per hour from the tourguide table
        $tourguide = Tourguide::findOrFail($tourguideId);
        $pricePerHour = $tourguide->price;
    
        // Create the reservation
        $reservation = new Reservation_tourguide();
        $reservation->tourist_id = $touristId;
        $reservation->tourguide_id = $tourguideId;
        $reservation->landmark_id = $landmarkId;  // Include landmark_id
        $reservation->hours = $hours;
        $reservation->price_of_hour = $pricePerHour;
        $reservation->isAccepted = false;
        $reservation->isFinished = false;
        $reservation->starting_time = $startingTime;
        $reservation->finished_time = $finishedTime;
        $reservation->day = $day;
        $reservation->save();
    
        return response()->json($reservation, 201);
    }
}
