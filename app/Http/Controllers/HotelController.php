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
use Illuminate\Support\Str;

class HotelController extends Controller
{
    public function create_Hotel(Hotel_createRequest $request)
    {
        // Create the hotel record with the provided request data
        $hotel = Hotel::create($request->all());
    
        // Handle the image uploads if the 'imgs' field is present and is an array
        if ($request->has('imgs') && is_array($request->file('imgs'))) {
            foreach ($request->file('imgs') as $image) {
                $imgName = Str::uuid() . '.' . $image->extension(); // Generate unique image name using UUID
                $image->storeAs('public/imgs/hotel', $imgName); // Store image with unique name in 'public/imgs/hotel' directory
                Hotel_Img::create([
                    'img' => url("api/images/hotel/" . $imgName), // Example URL format
                    'hotel_id' => $hotel->id,
                ]);
            }
        }
    
        // Handle the booking data if 'booking_link' and 'booking_img_id' fields are present
        if ($request->has('booking_link') && $request->has('booking_img_id') && is_array($request->booking_link) && is_array($request->booking_img_id)) {
            foreach ($request->booking_link as $index => $link) {
                if (isset($request->booking_img_id[$index])) {
                    $bookingData = [
                        'hotel_id' => $hotel->id,
                        'booking_img_id' => $request->booking_img_id[$index],
                        'booking_link' => $link
                    ];
                    Hotel_Booking::create($bookingData);
                }
            }
        }
    
        // Return a success response
        return response()->json(['message' => 'Hotel created successfully'], 200);
    }
    
    
    public function read_Hotel(Request $request)
    {
        // Retrieve city_id from query parameters
        $city_id = $request->query('city_id');
    
        // Validate that city_id is provided
        if (!$city_id) {
            return response()->json(['message' => 'City ID is required.'], 200);
        }
    
        // Fetch all hotels for the specified city_id
        $hotels = Hotel::where('city_id', $city_id)->get();
    
        // Check if no hotels are found
        if ($hotels->isEmpty()) {
            return response()->json(['message' => 'No hotels found for the specified city.'], 200);
        }
    
        // Initialize array to store hotels with related data
        $hotelsWithRelatedData = [];
    
        // Loop through each hotel
        foreach ($hotels as $hotel) {
            // Retrieve images for the current hotel
            $images = Hotel_Img::where('hotel_id', $hotel->id)->get();
    
            // Convert the hotel to an array
            $hotelData = $hotel->toArray();
    
            // Add the images to the hotel data
            $hotelData['images'] = $images->map(function ($image) {
                // Convert the relative path to a full URL
                $image->img = url($image->img);
                return $image;
            })->toArray();
    
            // Retrieve bookings for the current hotel
            $bookings = Hotel_Booking::where('hotel_id', $hotel->id)->get();
    
            // Add booking data along with the booking image
            $hotelData['bookings'] = $bookings->map(function ($booking) {
                // Retrieve the booking image
                $bookingImage = Hotel_Booking_Img::find($booking->booking_img_id);
    
                // Convert booking to array and add the booking image
                $bookingData = $booking->toArray();
                $bookingData['booking_image'] = $bookingImage ? url($bookingImage->img) : null;
                return $bookingData;
            })->toArray();
    
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
            return response()->json(['message' => 'Hotel not found'], 200);
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
        // Find the hotel record by its ID
        $hotel = Hotel::find($request->hotel_id);
    
        // Check if the hotel exists
        if (!$hotel) {
            return response()->json(['message' => 'Hotel not found'], 404);
        }
    
        // Handle image uploads if 'imgs' field is present and is an array
        if ($request->hasFile('imgs') && is_array($request->file('imgs'))) {
            // Delete existing images associated with the hotel
            $oldImages = Hotel_Img::where('hotel_id', $hotel->id)->get();
            foreach ($oldImages as $oldImage) {
                // Extract the path from the URL and delete the file from storage
                $filePath = str_replace(url('/') . '/storage/', 'public/', $oldImage->img);
                Storage::delete($filePath);
                // Delete the record from the database
                $oldImage->delete();
            }
    
            // Upload and save new images
            foreach ($request->file('imgs') as $image) {
                $imgName = Str::uuid() . '.' . $image->extension(); // Generate unique image name using UUID
                $image->storeAs('public/imgs/hotel', $imgName); // Store image with unique name in 'public/imgs/hotel' directory
                Hotel_Img::create([
                    'img' => url("api/images/hotel/" . $imgName), // Example URL format
                    'hotel_id' => $hotel->id,
                ]);
            }
        }
    
        // Update other hotel data
        $hotel->update($request->except(['imgs', 'booking_link', 'booking_img_id']));
    
        // Update bookings if 'booking_link' and 'booking_img_id' fields are present
        if ($request->has('booking_link') && $request->has('booking_img_id')) {
            // Ensure 'booking_link' and 'booking_img_id' are arrays
            if (is_array($request->booking_link) && is_array($request->booking_img_id)) {
                // Delete existing bookings for the hotel
                Hotel_Booking::where('hotel_id', $hotel->id)->delete();
    
                // Iterate over each pair of 'booking_link' and 'booking_img_id'
                foreach ($request->booking_link as $index => $link) {
                    if (isset($request->booking_img_id[$index])) {
                        $bookingData = [
                            'hotel_id' => $hotel->id,
                            'booking_img_id' => $request->booking_img_id[$index],
                            'booking_link' => $link
                        ];
                        Hotel_Booking::create($bookingData);
                    }
                }
            } else {
                return response()->json(['message' => 'Booking link and booking image ID must be arrays'], 422);
            }
        }
    
        // Return success response
        return response()->json(['message' => 'Successfully Updated']);
    }
    

    








    public function create_booking_img(Booking_img_createRequest $request)
    {
        // Store the image with a unique filename
        $imgName = time() . '.' . $request->file('img')->extension();
        $request->file('img')->storeAs('public/imgs/booking_img', $imgName);
    
        // Create a new booking image record
        $bookingImg = Hotel_Booking_Img::create([
            'img' => url("api/images/booking_img/".$imgName),
        ]);
    
        // Return a success response
        return response()->json(['message' => 'Booking image created successfully'], 200);
    }
    

public function read_booking_img()
{
    // Fetch all booking images
    $bookingImgs = Hotel_Booking_Img::all();

    // Check if no booking images are found
    if ($bookingImgs->isEmpty()) {
        return response()->json(['message' => 'No booking images found'], 200);
    }

    // Map booking images to include full URL paths
    $bookingImgsWithFullUrls = $bookingImgs->map(function ($image) {
        $image->img = url($image->img);
        return $image;
    });

    // Return the booking images with full URL paths as a JSON response
    return response()->json(['booking_images' => $bookingImgsWithFullUrls]);
}

public function update_booking_img(Booking_img_updateRequest $request)
{
    $bookingImg = Hotel_Booking_Img::find($request->id);

    // Check if booking image exists
    if (!$bookingImg) {
        return response()->json(['message' => 'Booking image not found'], 200);
    }

    // If a new image is uploaded, delete the old image and store the new one
    if ($request->hasFile('img')) {
        // Delete the old image if it exists
        if (Storage::exists($bookingImg->img)) {
            Storage::delete($bookingImg->img);
        }

        // Store the new image with a unique filename
        $imgName = time() . '.' . $request->file('img')->extension();
        $imgPath = $request->file('img')->storeAs('public/imgs/booking_img', $imgName);

        // Update the booking image record with the new image path
        $bookingImg->img = url("api/images/booking_img/" . $imgName);
        $bookingImg->save();
    }

    // Return a success response
    return response()->json(['message' => 'Booking image updated successfully']);
}




public function delete_booking_img(Booking_img_deleteRequest $request)
{
    // Find the booking image by its ID
    $bookingImg = Hotel_Booking_Img::find($request->id);
    if (!$bookingImg) {
        // If not found, return a response
        return response()->json(['message' => 'Booking image not found'], 200);
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






 
