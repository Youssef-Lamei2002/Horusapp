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
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LandmarkController extends Controller
{
    public function create_landmark(Landmark_createRequest $request)
    {
        $landmark=Landmark::create($request->all());
        foreach ($request->file('imgs') as $image){
            $img = $image->store('imgs/landmark');
            Landmark_Img::create(['img'=>$img,'landmark_id'=>$landmark->id]);
        }
        return response()->json(['message' => 'Successfully Created']);
    }




    public function read_landmark()
    {
        $landmarks = Landmark::all();
        $landmarksWithImages = [];
    
        foreach ($landmarks as $landmark) {
            $images = Landmark_Img::where('landmark_id', $landmark->id)->get();
            $landmarkData = $landmark->toArray();
            $landmarkData['images'] = $images->toArray();
            $landmarksWithImages[] = $landmarkData;
        }
    
        return response()->json(['landmarks' => $landmarksWithImages]);
    }




    public function update_landmark(Landmark_updateRequest $request)
    {
        $landmark = Landmark::find($request->landmark_id);
    
        if (!$landmark) {
            return response()->json(['error' => 'Landmark not found']);
        }
    
        // Check if new images are uploaded
        if ($request->hasFile('imgs')) {
            // Delete the old images if they exist
            $oldImages = Landmark_Img::where('landmark_id', $landmark->id)->get();
            foreach ($oldImages as $oldImage) {
                Storage::delete($oldImage->img);
                $oldImage->delete();
            }
    
            // Upload and save the new images
            foreach ($request->file('imgs') as $image) {
                $img = $image->store('imgs/landmark');
                Landmark_Img::create(['img' => $img, 'landmark_id' => $landmark->id]);
            }
        }
    
        // Update other landmark data
        $landmark->update($request->except(['imgs']));
    
        return response()->json(['message' => 'Successfully Updated']);
    }








public function delete_landmark(Landmark_deleteRequest $request)
    {
        $landmark = Landmark::find($request->landmark_id);

        if (!$landmark) {
            return response()->json(['error' => 'Landmark not found'], 404);
        }

        // Delete the associated images
        $images = Landmark_Img::where('landmark_id', $landmark->id)->get();
        foreach ($images as $image) {
            Storage::delete($image->img);
            $image->delete();
        }

        // Now, delete the landmark itself
        $landmark->delete();

        return response()->json(['message' => 'Landmark and associated images deleted successfully']);
    }
}
