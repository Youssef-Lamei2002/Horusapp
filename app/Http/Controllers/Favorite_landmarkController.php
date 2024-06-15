<?php

namespace App\Http\Controllers;
use App\Models\FavouriteLandmark;
use App\Models\Landmark;
use App\Models\Landmark_Img;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Favorite_landmarkController extends Controller
{
    public function favourite(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            'tourist_id' => 'required|exists:tourists,id',
            'landmark_id' => 'required|exists:landmarks,id',
        ]);

        // Check if the favorite already exists
        $existingFavorite = FavouriteLandmark::where('tourist_id', $request->tourist_id)
                                             ->where('landmark_id', $request->landmark_id)
                                             ->first();

        if ($existingFavorite) {
            return response()->json(['message' => 'Favorite already exists'], 200);
        }

        // Create a new favorite
        $favorite = FavouriteLandmark::create([
            'tourist_id' => $request->tourist_id,
            'landmark_id' => $request->landmark_id,
        ]);

        if ($favorite) {
            return response()->json(['message' => 'Favorite created successfully'], 200);
        } else {
            return response()->json(['message' => 'Failed to create favorite'], 200);
        }
    }
    public function read_favorite_landmarks(Request $request)
    {
        // Retrieve tourist_id from the request
        $tourist_id = $request->input('tourist_id');
        
        // Validate that tourist_id is provided
        if (!$tourist_id) {
            return response()->json(['message' => 'Tourist ID is required.'], 200);
        }
        
        // Retrieve all favorite landmark IDs of the tourist
        $favoriteLandmarks = FavouriteLandmark::where('tourist_id', $tourist_id)->get();
        
        if ($favoriteLandmarks->isEmpty()) {
            return response()->json(['message' => 'No favorite landmarks found for the specified tourist.'], 200);
        }
        
        $landmarkIds = $favoriteLandmarks->pluck('landmark_id')->toArray();
        
        // Retrieve landmarks that are favorites of the tourist
        $landmarks = Landmark::whereIn('id', $landmarkIds)->get();
        
        if ($landmarks->isEmpty()) {
            return response()->json(['message' => 'No favorite landmarks found for the specified tourist.'], 200);
        }
        
        // Prepare response with landmarks, their images, and their FavouriteLandmark IDs
        $landmarksWithImages = [];
        
        foreach ($landmarks as $landmark) {
            $images = Landmark_Img::where('landmark_id', $landmark->id)->get();
            
            $landmarkData = $landmark->toArray();
            
            $landmarkData['images'] = $images->map(function ($image) {
                $image->img = url('storage/' . str_replace('public/', '', $image->img));
                return $image;
            })->toArray();
            
            // Find the FavouriteLandmark ID for the current landmark
            $favouriteLandmarkId = $favoriteLandmarks->first(function ($value, $key) use ($landmark) {
                return $value->landmark_id === $landmark->id;
            })->id;
            
            // Add FavouriteLandmark ID to the landmark data
            $landmarkData['favourite_landmark_id'] = $favouriteLandmarkId;
            
            $landmarksWithImages[] = $landmarkData;
        }
        
        // Return the response as JSON
        return response()->json(['landmarks' => $landmarksWithImages]);
    }
    
    public function remove_favourite(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'favourite_landmark_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 200);
        }

        // Retrieve the favourite landmark record by ID
        $favouriteLandmark = FavouriteLandmark::find($request->input('favourite_landmark_id'));

        if (!$favouriteLandmark) {
            return response()->json(['message' => 'Favourite landmark not found.'], 200);
        }

        // Delete the favourite landmark record
        $favouriteLandmark->delete();

        // Return success response
        return response()->json(['message' => 'Favourite landmark removed successfully.']);
    }

}
