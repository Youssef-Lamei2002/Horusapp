<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Validator;
use Illuminate\Support\Str;
class CityController extends Controller
{
    public function create_city(Request $request)
    {
        // Validate the request data
        $request->validate([
            'city_name' => 'required|string|unique:cities',
            'city_img' => 'required|image|max:2048', // max 2MB
            'city_cover' => 'required|image|max:2048', // max 2MB
        ]);
        $cityImg =  Str::uuid()  . '.' . request('city_img')->extension();
        request('city_img')->storeAs("public/imgs/city",$cityImg );
        $cityCover =  Str::uuid()  . '.' . request('city_cover')->extension();
        request('city_cover')->storeAs("public/imgs/city", $cityCover);
        // Create a new city record
        $city = City::create([
            'city_name' => $request->city_name,
            'city_img' => url("api/images/city/".$cityImg),
            'city_cover' => url("api/images/city/".$cityCover),
        ]);
    
        // Return a success response
        return response()->json(['message' => 'City created successfully'], 200);
    }
    public function update_city(Request $request)
    {
        // Validate the request data
        $request->validate([
            'city_id' => 'required|exists:cities,id',
            'city_name' => 'required|string|unique:cities,city_name,' . $request->city_id,
            'city_img' => 'nullable|image|max:2048', // max 2MB
            'city_cover' => 'nullable|image|max:2048', // max 2MB
        ]);
    
        // Find the city by ID
        $city = City::find($request->city_id);
    
        // Update city images if new images are uploaded
        if ($request->hasFile('city_img')) {
            // Delete the old city image if it exists
            if ($city->city_img) {
                Storage::delete(str_replace(url(''), '', $city->city_img));
            }
            // Store the new city image
            $cityImg = time() . '.' . $request->file('city_img')->extension();
            $request->file('city_img')->storeAs('public/imgs/city', $cityImg);
            $city->city_img = url("api/images/city/" . $cityImg);
        }
    
        if ($request->hasFile('city_cover')) {
            // Delete the old city cover image if it exists
            if ($city->city_cover) {
                Storage::delete(str_replace(url(''), '', $city->city_cover));
            }
            // Store the new city cover image
            $cityCover = time() . '.' . $request->file('city_cover')->extension();
            $request->file('city_cover')->storeAs('public/imgs/city', $cityCover);
            $city->city_cover = url("api/images/city/" . $cityCover);
        }
    
        // Update other city data
        $city->city_name = $request->city_name;
        $city->save();
    
        // Return a success response
        return response()->json(['message' => 'City updated successfully'], 200);
    }
    
    public function read_city()
    {
        $cities = City::all();
        // Check if there are no cities found
        if ($cities->isEmpty()) {
            return response()->json(['message' => 'No cities found.'], 404);
        }
        return response()->json(['cities' => $cities]);
    }
    public function delete_city(Request $request)
{
    $cityId = $request->input('city_id');

    $city = City::find($cityId);

    if (!$city) {
        return response()->json(['error' => 'City not found'], 404);
    }

    // Delete the city image and cover from storage
    Storage::delete($city->city_img);
    Storage::delete($city->city_cover);

    // Delete the city from the database
    $city->delete();

    return response()->json(['message' => 'City deleted successfully']);
    }
}
