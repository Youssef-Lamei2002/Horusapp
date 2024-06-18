<?php

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function read_language()
    {
        $languages = Language::all();
        return response()->json(['languages' => $languages]);
    }
    public function create_language(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:languages|max:255',
        ]);

        $language = Language::create([
            'name' => $request->name,
        ]);

        return response()->json(['language' => $language], 200);
    }
    public function update_language(Request $request)
{
    // Validate the request data
    $request->validate([
        'id' => 'required|exists:languages,id', // Ensure the language ID exists
        'name' => 'required|string|max:255|unique:languages,name,' . $request->input('id'), // Ensure unique name excluding the current language
    ]);

    // Extract the language ID from the request data
    $languageId = $request->input('id');

    // Find the language by ID
    $language = Language::find($languageId);

    // Check if the language exists
    if (!$language) {
        return response()->json(['message' => 'Language not found'], 200);
    }

    // Update the language name
    $language->name = $request->input('name');
    $language->save();

    return response()->json(['message' => 'Language updated successfully']);
    }
    public function delete_language(Request $request)
    {
        // Validate the request
        $request->validate([
            'id' => 'required|integer|exists:languages,id',
        ]);

        // Get the ID from the request
        $id = $request->input('id');

        // Find the language by ID
        $language = Language::find($id);

        // Check if the language exists
        if (!$language) {
            return response()->json(['message' => 'Language not found'], 200);
        }

        // Delete the language
        $language->delete();

        return response()->json(['message' => 'Language deleted successfully']);
    }
}
