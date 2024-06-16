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
            return response()->json(['message' => "This File $name Is Not Found"], 404);
        }
    }
}
