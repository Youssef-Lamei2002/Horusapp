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

    // Store the transportation image
    $transportation_img = $request->file('transportation_img')->store('imgs/transportation');
    $transportation->transportation_img = $transportation_img;

    // Store the lines image
    $lines_img = $request->file('lines_img')->store('imgs/transportation');
    $transportation->lines_img = $lines_img;

    // Save the updated transportation model
    $transportation->save();

    // Return a success response
    return response()->json(['message' => 'Transportation created successfully']);
}



public function update_transportation(Transportation_updateRequest $request)
{
    $transportation = Transportation::find($request->transportation_id);

    if (!$transportation) {
        return response()->json(['error' => 'Transportation not found']);
    }

    // Validate the request data
    $validatedData = $request->validated();

    // Store old image paths
    $oldLinesImg = $transportation->lines_img;
    $oldTransportationImg = $transportation->transportation_img;

    // Update transportation record with validated data
    $transportation->update($validatedData);

    // Delete old images if update is successful
    if ($transportation->wasChanged()) {
        // Delete old images from imgs/transportation
        if ($oldLinesImg) {
            Storage::delete($oldLinesImg);
        }
        if ($oldTransportationImg) {
            Storage::delete($oldTransportationImg);
        }

        // Store new images
        if ($request->hasFile('lines_img')) {
            $lines_img = $request->file('lines_img')->store('imgs/transportation');
            $transportation->lines_img = $lines_img;
        }
        if ($request->hasFile('transportation_img')) {
            $transportation_img = $request->file('transportation_img')->store('imgs/transportation');
            $transportation->transportation_img = $transportation_img;
        }

        // Save the updated transportation model
        $transportation->save();

        return response()->json(['message' => 'Transportation updated successfully']);
    }

    // Revert to old image paths if update failed
    $transportation->lines_img = $oldLinesImg;
    $transportation->transportation_img = $oldTransportationImg;
    $transportation->save();

    return response()->json(['error' => 'Failed to update transportation']);
}
public function read_transportation()
{
    $transportations = Transportation::all();
    return response()->json(['transportations' => $transportations]);
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
