<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    function imageCity($name)
    {
        try {
            return response()->file("../storage/app/public/imgs/city/$name");
        } catch (Exception $e) {
            return response()->json(['message' => "This File $name Is Not Found"], 200);
        }
    }
    public function imageLandmark($name)
    {
    try {
        return response()->file(storage_path("../storage/app/public/imgs/landmark/$name"));
    } catch (Exception $e) {
        return response()->json(['message' => "This File $name Is Not Found"], 200);
        }
    }
    public function imageHotel($name)
    {
    try {
        return response()->file(storage_path("../storage/app/public/imgs/hotel/$name"));
    } catch (Exception $e) {
        return response()->json(['message' => "This File $name Is Not Found"], 200);
        }
    }
    public function imageBookingHotel($name)
    {
    try {
        return response()->file(storage_path("../storage/app/public/imgs/booking_img/$name"));
    } catch (Exception $e) {
        return response()->json(['message' => "This File $name Is Not Found"], 200);
        }
    }
    public function imagetransportation($name)
    {
    try {
        return response()->file(storage_path("../storage/app/public/imgs/transportation/$name"));
    } catch (Exception $e) {
        return response()->json(['message' => "This File $name Is Not Found"], 200);
        }
    }
    public function imagetProfile_pic($name)
    {
    try {
        return response()->file(storage_path("../storage/app/public/imgs/profile_pic/$name"));
    } catch (Exception $e) {
        return response()->json(['message' => "This File $name Is Not Found"], 200);
        }
    }
}
