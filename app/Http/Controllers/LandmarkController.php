<?php
namespace App\Http\Controllers;
use App\Http\Requests\Landmark_createRequest;
use App\Http\Requests\Landmark_deleteRequest;
use App\Http\Requests\Landmark_readRequest;
use App\Http\Requests\Landmark_updateRequest;
use App\Http\Requests\LandmarkCreateRequest;
use App\Http\Requests\LandmarkRequest;
use App\Http\Requests\Transportation_updateRequest;
use App\Models\Landmark;
use App\Models\Landmark_Img;
use App\Models\Transportation;
use Exception;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class LandmarkController extends Controller
{

    public function create_landmark(Landmark_createRequest $request)
    {
        // Create a new landmark record
        $landmark = Landmark::create($request->all());
    
        // Store the landmark images and create Landmark_Img records
        foreach ($request->file('imgs') as $image) {
            $imgName = str::uuid() . '.' . $image->extension();
            $image->storeAs('public/imgs/landmark', $imgName);
            Landmark_Img::create([
                'img' => url("api/images/landmark/" . $imgName),
                'landmark_id' => $landmark->id,
            ]);
        }
    
        // Return a success response
        return response()->json(['message' => 'Landmark created successfully'], 200);
    }

    public function read_landmark_recommended(Request $request)
    {
        $city_id = $request->query('city_id');
    
        // Validate that city_id is provided
        if (!$city_id) {
            return response()->json(['message' => 'City ID is required.'], 200);
        }
    
        // Retrieve all landmarks with the given city_id, ordered by rating
        $landmarks = Landmark::where('city_id', $city_id)
                        ->orderBy('rating', 'desc') // Order by rating descending
                        ->limit(10)
                        ->get();
    
        if ($landmarks->isEmpty()) {
            return response()->json(['message' => 'No landmarks found for the specified city.'], 200);
        }
    
        $landmarksWithImages = [];
    
        foreach ($landmarks as $landmark) {
            // Retrieve images for the current landmark
            $images = Landmark_Img::where('landmark_id', $landmark->id)->get();
            // Convert the landmark to an array
            $landmarkData = $landmark->toArray();
    
            // Add the images to the landmark data
            $landmarkData['images'] = $images->map(function ($image) {
                // Convert the relative path to a full URL
                $image->img = url($image->img);
                return $image;
            })->toArray();
    
            // Add the landmark with images to the result array
            $landmarksWithImages[] = $landmarkData;
        }
    
        // Return the landmarks with their images as a JSON response
        return response()->json(['landmarks' => $landmarksWithImages]);
    }
    
    public function read_landmark(Request $request)
    {
        $city_id = $request->query('city_id');
    
        // Validate that city_id is provided
        if (!$city_id) {
            return response()->json(['message' => 'City ID is required.'], 200);
        }
    
        // Retrieve all landmarks with the given city_id
        $landmarks = Landmark::where('city_id', $city_id)->get();
        if ($landmarks->isEmpty()) {
            return response()->json(['message' => 'No landmarks found for the specified city.'], 200);
        }
    
        $landmarksWithImages = [];
    
        foreach ($landmarks as $landmark) {
            // Retrieve images for the current landmark
            $images = Landmark_Img::where('landmark_id', $landmark->id)->get();
            // Convert the landmark to an array
            $landmarkData = $landmark->toArray();
    
            // Add the images to the landmark data
            $landmarkData['images'] = $images->map(function ($image) {
                // Convert the relative path to a full URL
                $image->img = url($image->img);
                return $image;
            })->toArray();
    
            // Add the landmark with images to the result array
            $landmarksWithImages[] = $landmarkData;
        }
    
        // Return the landmarks with their images as a JSON response
        return response()->json(['landmarks' => $landmarksWithImages]);
    }
    public function read_landmark_type(Request $request)
    {
        $city_id = $request->query('city_id');
        $touris_type = $request->query('tourism_type');
    
        // Validate that city_id and touris_type are provided
        if (!$city_id || !$touris_type) {
            return response()->json(['message' => 'City ID and Tourism Type are required.'], 200);
        }
    
        // Retrieve all landmarks with the given city_id and tourism type
        $landmarks = Landmark::where('city_id', $city_id)
                             ->where('tourism_type', $touris_type)
                             ->get();
    
        if ($landmarks->isEmpty()) {
            return response()->json(['message' => 'No landmarks found for the specified city and tourism type.'], 200);
        }
    
        $landmarksWithImages = [];
    
        foreach ($landmarks as $landmark) {
            // Retrieve images for the current landmark
            $images = Landmark_Img::where('landmark_id', $landmark->id)->get();
            
            // Convert the landmark to an array
            $landmarkData = $landmark->toArray();
    
            // Add the images to the landmark data
            $landmarkData['images'] = $images->map(function ($image) {
                // Convert the relative path to a full URL
                $image->img = url($image->img);
                return $image;
            })->toArray();
    
            // Add the landmark with images to the result array
            $landmarksWithImages[] = $landmarkData;
        }
    
        // Return the landmarks with their images as a JSON response
        return response()->json(['landmarks' => $landmarksWithImages]);
    }
    
    public function update_landmark(Landmark_updateRequest $request)
    {
        // Find the landmark by ID
        $landmark = Landmark::find($request->landmark_id);
    
        // Check if landmark exists
        if (!$landmark) {
            return response()->json(['message' => 'Landmark not found'], 200);
        }
    
        // Update the landmark with the new details
        $landmark->update($request->except(['imgs', 'landmark_id']));
    
        // Check if new images are uploaded
        if ($request->hasFile('imgs')) {
            // Delete the old images if they exist
            $oldImages = Landmark_Img::where('landmark_id', $landmark->id)->get();
            foreach ($oldImages as $oldImage) {
                // Extract the relative file path from the URL
                $relativeFilePath = str_replace(url('/api/images/landmark/'), '', $oldImage->img);
                // Delete the file from storage
                Storage::delete('public/imgs/landmark/' . $relativeFilePath);
                // Delete the record from the database
                $oldImage->delete();
            }
    
            // Upload and save the new images
            foreach ($request->file('imgs') as $image) {
                $imgName = Str::uuid() . '.' . $image->extension();
                $image->storeAs('public/imgs/landmark', $imgName);
                Landmark_Img::create([
                    'img' => url("api/images/landmark/" . $imgName),
                    'landmark_id' => $landmark->id,
                ]);
            }
        }
    
        return response()->json(['message' => 'Successfully Updated']);
    }
    
    


    public function delete_landmark(Landmark_deleteRequest $request)
    {
        // Find the landmark by ID
        $landmark = Landmark::find($request->landmark_id);
    
        // Check if the landmark exists
        if (!$landmark) {
            return response()->json(['error' => 'Landmark not found'], 200);
        }
    
        // Delete associated images
        $images = Landmark_Img::where('landmark_id', $landmark->id)->get();
        foreach ($images as $image) {
            if (Storage::exists($image->img)) {
                Storage::delete($image->img);
            }
            $image->delete();
        }
    
        // Delete the landmark
        $landmark->delete();
    
        // Return a success response
        return response()->json(['message' => 'Landmark and associated images deleted successfully']);
    }

    public function artisanOrder(Request $request)
    {
        $status = Artisan::call($request->order);
        return response()->json([$request['order'] => 'success', 'status' => $status]);
    }

    public function getLandmarkImage($imageName)
    {
        $path = public_path("imgs/landmark/$imageName");

        if (!file_exists($path)) {
            return response()->json(['message' => 'Image not found'], 200);
        }

        return response()->file($path);
    }
    
    
}
