<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Tourguide;
use App\Models\Tourguide_Language;
use App\Models\Tourist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileSettingController extends Controller
{
    public function updateName(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string|max:255',
            'email_type' => 'required|boolean', // 0 for Tourist, 1 for Tourguide
        ]);

        $email = $request->input('email');
        $name = $request->input('name');
        $emailType = $request->input('email_type');

        if ($emailType == 0) {
            $user = Tourist::where('email', $email)->first();
        } elseif ($emailType == 1) {
            $user = Tourguide::where('email', $email)->first();
        } else {
            return response()->json(['message' => 'Invalid email type'], 400);
        }

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->name = $name;
        $user->save();

        return response()->json(['message' => 'Name updated successfully']);
    }
    public function addLanguage(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'tourguide_id' => 'required|exists:tourguides,id',
            'language_id' => 'required|exists:languages,id',
            // Add any additional validation rules as needed
        ]);

        // Retrieve the authenticated tour guide's ID
        $tourguideId = $request->input('tourguide_id');

        // Retrieve the language object
        $language = Language::find($request->input('language_id'));

        // Check if the authenticated tour guide already has the selected language
        if (Tourguide_Language::where('tourguide_id', $tourguideId)->where('language_id', $language->id)->exists()) {
            return response()->json(['message' => 'Tour guide already has the selected language'], 422);
        }

        // Insert a new row into tourguide__languages table
        $tourguideLanguage = new Tourguide_Language();
        $tourguideLanguage->tourguide_id = $tourguideId;
        $tourguideLanguage->language_id = $language->id;
        $tourguideLanguage->save();

        return response()->json(['message' => 'Language added successfully'], 200);
    }

    public function removeLanguage(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'tourguide_id' => 'required|exists:tourguides,id',
            'language_id' => 'required|exists:languages,id',
            // Add any additional validation rules as needed
        ]);

        // Retrieve the authenticated tour guide's ID
        $tourguideId = $request->input('tourguide_id');

        // Retrieve the ID of the language to be removed
        $languageId = $request->input('language_id');

        // Find the tour guide language entry to delete
        $tourguideLanguage = Tourguide_Language::where('tourguide_id', $tourguideId)
                                              ->where('language_id', $languageId)
                                              ->first();

        // Check if the tour guide has the specified language
        if (!$tourguideLanguage) {
            return response()->json(['message' => 'Tour guide does not have the specified language'], 404);
        }

        // Check if the tour guide has at least one language remaining after removal
        $remainingLanguages = Tourguide_Language::where('tourguide_id', $tourguideId)
                                                ->where('language_id', '!=', $languageId)
                                                ->count();

        if ($remainingLanguages === 0) {
            return response()->json(['message' => 'Tour guide must have at least one language'], 422);
        }

        // Delete the tour guide language entry
        $tourguideLanguage->delete();

        return response()->json(['message' => 'Language removed successfully'], 200);
    }
    public function updateCity(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'tourguide_id' => 'required|exists:tourguides,id',
            'city_id' => 'required|exists:cities,id',
        ]);

        // Retrieve the tour guide by ID
        $tourguideId = $request->input('tourguide_id');
        $tourguide = Tourguide::findOrFail($tourguideId);

        // Update the tour guide's current city
        $tourguide->city_id = $request->input('city_id');
        $tourguide->save();

        return response()->json(['message' => 'Tour guide city updated successfully'], 200);
    }
}
