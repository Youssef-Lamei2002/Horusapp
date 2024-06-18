<?php

namespace App\Http\Controllers;

use App\Http\Requests\Transportation_createRequest;
use App\Http\Requests\Transportation_deleteRequest;
use App\Http\Requests\Transportation_updateRequest;
use App\Models\Transportation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TransportationController extends Controller
{
    public function create_transportation(Transportation_createRequest $request)
    {
        // Validate the request data
        $validatedData = $request->validated();
    
        // Handle the transportation image upload
        $transportationImgName = time() . '_transportation.' . request('transportation_img')->extension();
        request('transportation_img')->storeAs('public/imgs/transportation', $transportationImgName);
        $transportationImgUrl = url("api/images/transportation/" . $transportationImgName);
    
        // Handle the lines image upload
        $linesImgName = time() . '_lines.' . request('lines_img')->extension();
        request('lines_img')->storeAs('public/imgs/transportation', $linesImgName);
        $linesImgUrl = url("api/images/transportation/" . $linesImgName);
    
        // Create a new Transportation instance with validated data
        $transportation = Transportation::create([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'sunday_open' => $validatedData['sunday_open'],
            'sunday_close' => $validatedData['sunday_close'],
            'monday_open' => $validatedData['monday_open'],
            'monday_close' => $validatedData['monday_close'],
            'tuesday_open' => $validatedData['tuesday_open'],
            'tuesday_close' => $validatedData['tuesday_close'],
            'wednesday_open' => $validatedData['wednesday_open'],
            'wednesday_close' => $validatedData['wednesday_close'],
            'thursday_open' => $validatedData['thursday_open'],
            'thursday_close' => $validatedData['thursday_close'],
            'friday_open' => $validatedData['friday_open'],
            'friday_close' => $validatedData['friday_close'],
            'saturday_open' => $validatedData['saturday_open'],
            'saturday_close' => $validatedData['saturday_close'],
            'lines_img' => $linesImgUrl,
            'transportation_img' => $transportationImgUrl,
            'prices' => $validatedData['prices'],
            'city_id' => $validatedData['city_id'],
            'app_link' => $validatedData['app_link'],
        ]);
    
        // Return a success response
        return response()->json(['message' => 'Transportation created successfully'], 200);
    }
    
    
    public function update_transportation(Transportation_updateRequest $request)
    {
        // Find the transportation record by ID
        $transportation = Transportation::find($request->transportation_id);
    
        // Check if the transportation record exists
        if (!$transportation) {
            return response()->json(['error' => 'Transportation not found'], 404);
        }
    
        // Validate the request data
        $validatedData = $request->validated();
    
        // Store old image paths
        $oldLinesImg = $transportation->lines_img;
        $oldTransportationImg = $transportation->transportation_img;
    
        // Handle image updates
        if ($request->hasFile('lines_img')) {
            // Delete old lines image
            if ($oldLinesImg) {
                Storage::delete($oldLinesImg);
            }
            // Store new lines image
            $linesImgName = time() . '_lines.' . request('lines_img')->extension();
            request('lines_img')->storeAs('public/imgs/transportation', $linesImgName);
            $linesImgUrl = url("api/images/transportation/" . $linesImgName);        }
    
        if ($request->hasFile('transportation_img')) {
            // Delete old transportation image
            if ($oldTransportationImg) {
                Storage::delete($oldTransportationImg);
            }
            // Store new transportation image
            $transportationImgName = time() . '_transportation.' . request('transportation_img')->extension();
            request('transportation_img')->storeAs('public/imgs/transportation', $transportationImgName);
            $transportationImgUrl = url("api/images/transportation/" . $transportationImgName);
        }
    
        // Update transportation record with validated data
        $transportation->update([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'sunday_open' => $validatedData['sunday_open'],
            'sunday_close' => $validatedData['sunday_close'],
            'monday_open' => $validatedData['monday_open'],
            'monday_close' => $validatedData['monday_close'],
            'tuesday_open' => $validatedData['tuesday_open'],
            'tuesday_close' => $validatedData['tuesday_close'],
            'wednesday_open' => $validatedData['wednesday_open'],
            'wednesday_close' => $validatedData['wednesday_close'],
            'thursday_open' => $validatedData['thursday_open'],
            'thursday_close' => $validatedData['thursday_close'],
            'friday_open' => $validatedData['friday_open'],
            'friday_close' => $validatedData['friday_close'],
            'saturday_open' => $validatedData['saturday_open'],
            'saturday_close' => $validatedData['saturday_close'],
            'lines_img' => $linesImgUrl,
            'transportation_img' => $transportationImgUrl,
            'prices' => $validatedData['prices'],
            'city_id' => $validatedData['city_id'],
            'app_link' => $validatedData['app_link'],
        ]);
    
        // Check if update was successful
        if ($transportation->wasChanged()) {
            return response()->json(['message' => 'Transportation updated successfully']);
        }
    
        // Revert to old image paths if update failed
        if ($oldLinesImg !== $transportation->lines_img) {
            $transportation->lines_img = $oldLinesImg;
        }
        if ($oldTransportationImg !== $transportation->transportation_img) {
            $transportation->transportation_img = $oldTransportationImg;
        }
        $transportation->save();
    
        return response()->json(['error' => 'Failed to update transportation']);
    }
    
    
    
    
    public function read_transportation(Request $request)
{
    // Fetch all transportations
    $transportations = Transportation::all();

    // Check if any transportations are found
    if ($transportations->isEmpty()) {
        return response()->json(['message' => 'No transportations found'], 404);
    }

    // Initialize array to store transportations with related data
    $transportationsWithDetails = [];

    // Loop through each transportation
    foreach ($transportations as $transportation) {
        // Convert the transportation to an array
        $transportationData = $transportation->toArray();

        // Convert the relative paths to full URLs for images
        $transportationData['transportation_img'] = url($transportation->transportation_img);
        $transportationData['lines_img'] = url($transportation->lines_img);

        // Add the transportation data to the result array
        $transportationsWithDetails[] = $transportationData;
    }

    // Return the transportations with their details as a JSON response
    return response()->json(['transportations' => $transportationsWithDetails]);
    }
    public function delete_transportation(Transportation_deleteRequest $request)
{
    $transportation = Transportation::find($request->transportation_id);

    if (!$transportation) {
        return response()->json(['error' => 'Transportation not found'], 404);
    }

    // Delete images from storage
    if ($transportation->lines_img) {
        Storage::delete($transportation->lines_img);
    }
    if ($transportation->transportation_img) {
        Storage::delete($transportation->transportation_img);
    }

    // Delete the transportation record
    $transportation->delete();

    return response()->json(['message' => 'Transportation deleted successfully']);
}

}
