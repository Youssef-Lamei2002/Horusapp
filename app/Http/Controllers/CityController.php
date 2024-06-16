<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Validator;

class CityController extends Controller
{
    public function create_city(Request $request)
    {
        // Validate the request data
        $request->validate([
            'city_name' => 'required|string|unique:cities',
            'city_img' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // max 2MB
            'city_cover' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // max 2MB
        ]);
    
        // Store the city image
        $cityImgPath = $request->file('city_img')->store('public/imgs/city');
    
        // Store the city cover image
        $cityCoverPath = $request->file('city_cover')->store('public/imgs/city');
    
        // Create a new city record
        $city = City::create([
            'city_name' => $request->city_name,
            'city_img' => url("api/images/city/".$cityImgPath),
            'city_cover' => url("api/images/city/".$cityCoverPath),
        ]);
    
        // Return a success response
        return response()->json(['message' => 'City created successfully'], 200);
    }
    public function update_city(Request $request)
    {
        $city = City::find($request->city_id);
    
        if (!$city) {
            return response()->json(['error' => 'City not found'], 404);
        }
    
        $city_img = $request->file('city_img');
        $city_cover = $request->file('city_cover');
    
        // Check if new images are uploaded
        if ($city_img && $city_cover) {
            // Delete the old images if they exist
            Storage::delete($city->city_img);
            Storage::delete($city->city_cover);
    
            // Upload and save the new images
            $city->city_img = $city_img->store('public/imgs/city');
            $city->city_cover = $city_cover->store('public/imgs/city');
        }
    
        // Update other city data
        $city->city_name = $request->city_name;
        $city->save();
    
        return response()->json(['message' => 'City updated successfully']);
    }
    public function read_city()
    {
        $cities = City::all();
    
        // Check if there are no cities found
        if ($cities->isEmpty()) {
            return response()->json(['message' => 'No cities found.'], 404);
        }
    
        $citiesWithImages = [];
    
        foreach ($cities as $city) {
            // Convert the city to an array
            $cityData = $city->toArray();
    
            // Append city image URLs
            $cityData['city_img'] = url('storage/' . str_replace('public/', '', $city->city_img));
            $cityData['city_cover'] = url('storage/' . str_replace('public/', '', $city->city_cover));
    
            // Add the city with images to the result array
            $citiesWithImages[] = $cityData;
        }
    
        // Return the cities with their images as a JSON response
        return response()->json(['cities' => $citiesWithImages]);
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
