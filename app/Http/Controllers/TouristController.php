<?php

namespace App\Http\Controllers;

use App\Models\Tourist;
use Illuminate\Http\Request;

class TouristController extends Controller
{
    public function deleteTourist(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'id' => 'required|integer|exists:tourists,id',
        ]);

        // Retrieve the ID of the tourist to be deleted from the request
        $touristId = $request->input('id');

        // Find the tourist by ID
        $tourist = Tourist::findOrFail($touristId);

        // Delete the tourist
        $tourist->delete();

        // Return a response indicating success
        return response()->json(['message' => 'Tourist deleted successfully.'], 200);
    }
    public function getAllTourists()
    {
        // Fetch all tourists
        $tourists = Tourist::all();

        // Return response with tourists data
        return response()->json(['tourists' => $tourists], 200);
    }
}
