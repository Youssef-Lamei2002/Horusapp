<?php

namespace App\Http\Controllers;

use App\Http\Requests\Booking_img_createRequest;
use App\Http\Requests\Booking_img_deleteRequest;
use App\Http\Requests\Booking_img_updateRequest;
use App\Http\Requests\Hotel_createRequest;
use App\Http\Requests\Hotel_deleteRequest;
use App\Http\Requests\Hotel_updateRequest;
use App\Models\Hotel;
use App\Models\Hotel_Booking;
use App\Models\Hotel_Booking_Img;
use App\Models\Hotel_Img;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HotelController extends Controller
{
    public function create_Hotel(Hotel_createRequest $request)
    {
        $hotel = Hotel::create($request->all());
    
        // Check if the fields exist and are arrays
        if ($request->has('imgs') && is_array($request->imgs)) {
            foreach ($request->file('imgs') as $image) {
                $img = $image->store('imgs/hotel');
                Hotel_Img::create(['img' => $img, 'hotel_id' => $hotel->id]);
            }
        }
    
        // Check if booking data exists before accessing it
        if ($request->has('booking_link') && $request->has('booking_img_id')) {
            $bookingData = [
                'hotel_id' => $hotel->id,
                'booking_img_id' => $request->booking_img_id[0], // Accessing the first element
                'booking_link' => $request->booking_link[0] // Accessing the first element
            ];
            Hotel_Booking::create($bookingData);
        }
    
        return response()->json(['message' => 'Successfully Created']);
    }
    
    public function read_Hotel()
    {
        // Fetch all hotels
        $hotels = Hotel::all();
        
        // Initialize array to store hotels with related data
        $hotelsWithRelatedData = [];
        
        // Loop through each hotel
        foreach ($hotels as $hotel) {
            // Fetch images related to the current hotel
            $images = Hotel_Img::where('hotel_id', $hotel->id)->get();
            
            // Fetch booking information related to the current hotel
            $bookings = Hotel_Booking::where('hotel_id', $hotel->id)->get();
            
            // Initialize array to store hotel data along with related images and bookings
            $hotelData = $hotel->toArray();
            
            // Add images to hotel data
            $hotelData['images'] = $images->toArray();
            
            // Initialize array to store booking data
            $hotelBookings = [];
            
            // Loop through each booking related to the current hotel
            foreach ($bookings as $booking) {
                // Fetch booking image related to the booking
                $bookingImage = Hotel_Booking_Img::find($booking->booking_img_id);
                
                // Add booking data along with the booking image to hotel bookings array
                $bookingData = $booking->toArray();
                $bookingData['booking_image'] = $bookingImage ? $bookingImage->toArray() : null;
                $hotelBookings[] = $bookingData;
            }
            
            // Add bookings to hotel data
            $hotelData['bookings'] = $hotelBookings;
            
            // Add hotel data to array
            $hotelsWithRelatedData[] = $hotelData;
        }
        
        // Return JSON response with hotels and related data
        return response()->json(['hotels' => $hotelsWithRelatedData]);
    }
    
    
    public function delete_hotel(Hotel_deleteRequest $request)
    {
        $hotel = Hotel::find($request->hotel_id);
            if (!$hotel) {
            return response()->json(['error' => 'Hotel not found'], 404);
        }
        $hotelImages = Hotel_Img::where('hotel_id', $hotel->id)->get();
        foreach ($hotelImages as $image) {
            Storage::delete($image->img);
            $image->delete();
        }
        $hotelBookings = Hotel_Booking::where('hotel_id', $hotel->id)->get();
        foreach ($hotelBookings as $booking) {
            $booking->delete();
        }
        $hotel->delete();    
        return response()->json(['message' => 'Hotel and associated data deleted successfully']);
    }
    

    public function update_hotel(Hotel_updateRequest $request)
{
    $hotel = Hotel::find($request->hotel_id);

    if (!$hotel) {
        return response()->json(['error' => 'Hotel not found']);
    }

    // Check if new images are uploaded
    if ($request->hasFile('imgs')) {
        // Delete the old images if they exist
        $oldImages = Hotel_Img::where('hotel_id', $hotel->id)->get();
        foreach ($oldImages as $oldImage) {
            Storage::delete($oldImage->img);
            $oldImage->delete();
        }

        // Upload and save the new images
        foreach ($request->file('imgs') as $image) {
            $img = $image->store('imgs/hotel');
            Hotel_Img::create(['img' => $img, 'hotel_id' => $hotel->id]);
        }
    }

    // Update other hotel data
    $hotel->update($request->except(['imgs']));

    // Update bookings if necessary
    if ($request->has('booking_link')) {
        $hotel->update(['booking_link' => $request->booking_link]);
    }

    if ($request->has('booking_img_id')) {
        // You can handle updating booking image IDs here
        // For example, you might want to delete old booking images and add new ones
        // You can implement this logic similar to how images are handled above
    }

    return response()->json(['message' => 'Successfully Updated']);
}

    








public function create_booking_img(Booking_img_createRequest $request)
{
    // Store the image
    $imgPath = $request->file('img')->store('imgs/booking_img');

    // Create a new booking image record
    $bookingImg = Hotel_Booking_Img::create(['img' => $imgPath]);

    return response()->json(['message' => 'Booking image created successfully']);
}

public function read_booking_img()
{
    $bookingImgs = Hotel_Booking_Img::all();

    if ($bookingImgs->isEmpty()) {
        return response()->json(['message' => 'No booking images found']);
    }

    return response()->json($bookingImgs);
}

public function update_booking_img(Booking_img_updateRequest $request)
{
    $bookingImg = Hotel_Booking_Img::find($request->id);
    if (!$bookingImg) {
        return response()->json(['message' => 'Booking image not found'], 404);
    }
    if ($request->has('img')) {
        if (Storage::exists($bookingImg->img)) {
            Storage::delete($bookingImg->img);
        }
        
        // Store the new image
        $bookingImg->img = $request->img->store('imgs/booking_img');
    }

    // Save the changes to the booking image
    $bookingImg->save();

    // Return a success response
    return response()->json(['message' => 'Booking image updated successfully']);
}



public function delete_booking_img(Booking_img_deleteRequest $request)
{
    // Find the booking image by its ID
    $bookingImg = Hotel_Booking_Img::find($request->id);
    if (!$bookingImg) {
        // If not found, return a 404 response
        return response()->json(['message' => 'Booking image not found'], 404);
    }

    // Check if the image exists in storage and delete it
    if (Storage::exists($bookingImg->img)) {
        Storage::delete($bookingImg->img);
    }

    // Delete the booking image from the database
    $bookingImg->delete();

    // Return a success response
    return response()->json(['message' => 'Booking image deleted successfully']);
}
}






 
