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
    
        // Create a new Transportation instance with validated data
        $transportation = Transportation::create($validatedData);
    
        // Handle the transportation image upload
        if ($request->hasFile('transportation_img')) {
            $transportation_img = $request->file('transportation_img')->store('public/imgs/transportation');
            $transportation->transportation_img = $transportation_img;
        }
    
        // Handle the lines image upload
        if ($request->hasFile('lines_img')) {
            $lines_img = $request->file('lines_img')->store('public/imgs/transportation');
            $transportation->lines_img = $lines_img;
        }
    
        // Save the updated transportation model
        $transportation->save();
    
        // Return a success response
        return response()->json(['message' => 'Transportation created successfully']);
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
            $lines_img = $request->file('lines_img')->store('public/imgs/transportation');
            $validatedData['lines_img'] = $lines_img;
        }
    
        if ($request->hasFile('transportation_img')) {
            // Delete old transportation image
            if ($oldTransportationImg) {
                Storage::delete($oldTransportationImg);
            }
            // Store new transportation image
            $transportation_img = $request->file('transportation_img')->store('public/imgs/transportation');
            $validatedData['transportation_img'] = $transportation_img;
        }
    
        // Update transportation record with validated data
        $transportation->update($validatedData);
    
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
        $transportationData['transportation_img'] = url('storage/' . str_replace('public/', '', $transportation->transportation_img));
        $transportationData['lines_img'] = url('storage/' . str_replace('public/', '', $transportation->lines_img));

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
