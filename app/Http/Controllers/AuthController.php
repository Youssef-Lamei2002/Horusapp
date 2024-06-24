<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Language;
use App\Models\Tourguide;
use App\Models\Tourguide_Language;
use App\Models\Tourist;
use App\Models\User;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $emailType = $request->input('email_type');
    
        try {
            // Check if the email exists in Tourist or Tourguide based on email type
            if ($emailType == 0) {
                $user = Tourist::where('email', $credentials['email'])->first();
            } elseif ($emailType == 1) {
                $user = Tourguide::where('email', $credentials['email'])->with('languages')->first();
            } else {
                return response()->json(['message' => 'Invalid email type'], 200);
            }
    
            // If user not found based on email
            if (!$user) {
                return response()->json(['message' => 'Email not found'], 200);
            }
    
            // Validate password if user found
            if (!Hash::check($credentials['password'], $user->password)) {
                return response()->json(['message' => 'Password is incorrect'], 200);
            }
    
            // Load city details if available
            if($emailType ==1)
            {
            $city = $user->city;
            $cityName = $city ? $city->city_name : null;
            $cityCover = $city ? $city->city_cover : null;
    
            // Generate JWT token manually
            $token = $this->generateJwtToken($user);
            }
            // Return user data with token and city details
            return response()->json([
                'message' => 'Login success',
                'user' => $user
            ]);
    
        } catch (\Exception $e) {
            return response()->json(['message' => 'Could not authenticate']);
        }
    }
    
    
    /**
     * Generate JWT token manually for the given user.
     *
     * @param $user
     * @return string
     */
    private function generateJwtToken($user)
    {
        $payload = [
            'sub' => $user->id,
            'email' => $user->email,
            // Add more claims as needed
        ];
    
        // Generate JWT token using the JWTAuth facade
        return JWTAuth::fromUser($user, $payload);
    }
    
    public function register(RegisterRequest $request)
    {
        // Check if the email is provided
        if (!$request->has('email') || empty($request->input('email'))) {
            return response()->json(['message' => 'Email is required'], 200);
        }
    
        $email = $request->input('email');
    
        // Check if the email is valid
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['message' => 'Email is incorrect'], 200);
        }
    
        $phoneNumber = $request->input('phone_number');
        // Check if the email or phone number already exists in either the Tourist or Tourguide tables
        if($request->input('email_type') == 0){
        $emailExistsInTourist = Tourist::where('email', $email)->exists();
        $phoneNumberExistsInTourist = Tourist::where('phone_number', $phoneNumber)->exists();
        $emailExists = $emailExistsInTourist;
        $phoneNumberExists = $phoneNumberExistsInTourist;
        }else if($request->input('email_type') == 1)
        {
        $emailExistsInTourguide = Tourguide::where('email', $email)->exists();
        $phoneNumberExistsInTourguide = Tourguide::where('phone_number', $phoneNumber)->exists();
        $emailExists = $emailExistsInTourguide;
        $phoneNumberExists = $phoneNumberExistsInTourguide;

        }

    
        // Return appropriate messages if email or phone number already exists
        if ($emailExists) {
            return response()->json(['message' => 'The email already exists'], 200);
        }
        if ($phoneNumberExists) {
            return response()->json(['message' => 'The phone number already exists'], 200);
        }
    
        // Check if the phone number starts with 010, 011, 012, or 015
        if (!preg_match('/^(010|011|012|015)[0-9]{8}$/', $phoneNumber)) {
            return response()->json(['message' => 'The phone number format is incorrect'], 200);
        }
    
        // Proceed with registration
        $data = $request->except('profile_pic'); // Exclude profile_pic from other data
        $data['password'] = Hash::make($data['password']);

        // Handle profile picture upload
        if ($request->hasFile('profile_pic')) {
            $profilePic = $request->file('profile_pic');
    
            // Generate a unique name for the profile picture
            $profilePicName = time() . '.' . request('profile_pic')->extension();
    
            // Store the profile picture in the specified directory
            $profilePicPath = $profilePic->storeAs('public/imgs/profile_pic', $profilePicName);
    
            // Assuming you want to store the full URL in the database
            $data['profile_pic'] = url('api/images/profile_pic/' . $profilePicName);
        }
    
        // Create a new user based on email_type
        if ($request->input('email_type') == 0) {
            // Create a Tourist
            Tourist::create($data);
        } elseif ($request->input('email_type') == 1) {
            // Create a Tourguide
            $tourguide = Tourguide::create($data);
    
            // Attach languages to the Tourguide
            foreach ($request->input('languages') as $language) {
                Tourguide_Language::create(['tourguide_id' => $tourguide->id, 'language_id' => $language]);
            }
    
            // Set additional Tourguide data
            $tourguide->update([
                'nationality' => 'Egypt',
                'rate' => null,
                'isBlocked' => 0,
                'isApproved' => 0,
            ]);
        }
    
        // Return a success message with a 200 status code
        return response()->json(['message' => 'Successfully Registered'], 200);
    }

    public function getTourGuides($cityId)
    {
        // Fetch tour guides with the specified city_id
        $tourGuides = Tourguide::where('city_id', $cityId)->with('languages')->get();
        // You can manipulate $tourGuides here if needed

        // Construct the final response
        $response = [
            'tourGuides' => $tourGuides,
        ];

        // Return the result as a JSON response
        return response()->json($response);
    }
    
}
