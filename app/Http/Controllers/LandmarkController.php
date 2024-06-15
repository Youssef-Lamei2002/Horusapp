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

class LandmarkController extends Controller
{
    public function create_landmark(Landmark_createRequest $request)
    {
        $landmark=Landmark::create($request->all());
        foreach ($request->file('imgs') as $image){
            $img = $image->store('public/imgs/landmark');
            Landmark_Img::create(['img'=>$img,'landmark_id'=>$landmark->id]);
        }
        return response()->json(['message' => 'Successfully Created']);
    }

    public function read_landmark(Request $request)
    {
        $city_id = $request->query('city_id');
    
        // Validate that city_id is provided
        if (!$city_id) {
            return response()->json(['message' => 'City ID is required.'], 400);
        }
    
        // Retrieve all landmarks with the given city_id
        $landmarks = Landmark::where('city_id', $city_id)->get();
        if ($landmarks->isEmpty()) {
            return response()->json(['message' => 'No landmarks found for the specified city.'], 404);
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
                $image->img = url('storage/' . str_replace('public/', '', $image->img));
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
            return response()->json(['error' => 'Landmark not found'], 404);
        }
    
        // Update the landmark with the new details
        $landmark->update($request->except(['imgs', 'landmark_id']));
    
        // Check if new images are uploaded
        if ($request->hasFile('imgs')) {
            // Delete the old images if they exist
            $oldImages = Landmark_Img::where('landmark_id', $landmark->id)->get();
            foreach ($oldImages as $oldImage) {
                // Delete the file from storage
                Storage::delete($oldImage->img);
                // Delete the record from the database
                $oldImage->delete();
            }
    
            // Upload and save the new images
            foreach ($request->file('imgs') as $image) {
                $img = $image->store('public/imgs/landmark');
                Landmark_Img::create(['img' => $img, 'landmark_id' => $landmark->id]);
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
            return response()->json(['error' => 'Landmark not found'], 404);
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
            return response()->json(['message' => 'Image not found'], 404);
        }

        return response()->file($path);
    }
    
    
}
