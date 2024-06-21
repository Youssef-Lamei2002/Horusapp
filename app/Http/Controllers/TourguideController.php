<?php

namespace App\Http\Controllers;

use App\Models\Tourguide;
use Illuminate\Http\Request;

class TourguideController extends Controller
{
    public function deleteTourguide(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'id' => 'required|integer|exists:tourguides,id',
        ]);

        // Retrieve the ID of the tourguide to be deleted from the request
        $tourguideId = $request->input('id');

        // Find the tourguide by ID
        $tourguide = Tourguide::findOrFail($tourguideId);

        // Delete the tourguide
        $tourguide->delete();

        // Return a response indicating success
        return response()->json(['message' => 'Tourguide deleted successfully.'], 200);
    }


    public function getAllTourguides()
    {
        // Fetch all tour guides
        $tourguides = Tourguide::all();

        // Return response with tour guides data
        return response()->json(['tourguides' => $tourguides], 200);
    }
}
