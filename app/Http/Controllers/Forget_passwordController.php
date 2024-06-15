<?php

namespace App\Http\Controllers;

use App\Models\OTP;
use App\Models\Tourguide;
use App\Models\Tourist;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class Forget_passwordController extends Controller
{
    public function checkEmailExists(Request $request)
    {
        // Delete expired OTP codes before proceeding
        $this->deleteExpiredOTPCodes();

        $request->validate([
            'email' => 'required|email',
            'email_type' => 'required|boolean' // Validate email_type as boolean
        ]);

        $email = $request->input('email');
        $emailType = $request->input('email_type');

        $cooldownMinutes = 5; // Cooldown period in minutes

        if ($emailType == 0) {
            // Check if the email exists in the Tourist model
            $tourist = Tourist::where('email', $email)->first();
            if ($tourist) {
                // Check if there's a recent OTP request for this tourist
                $recentOTPRequest = OTP::where('tourist_id', $tourist->id)->where('created_at', '>=', now()->subMinutes($cooldownMinutes))->first();
                if ($recentOTPRequest) {
                    // If a recent OTP request exists, return a message indicating the cooldown period
                    return response()->json(['message' => 'Too many requests. Please wait for ' . $cooldownMinutes . ' minutes before requesting a new OTP.'], 429);
                }
                // Generate OTP
                $otp = $this->generateOtp();
                // Create OTP record for tourist
                OTP::create([
                    'tourist_id' => $tourist->id,
                    'otp' => $otp,
                    'expires_at' => now()->addMinutes(5) // Set expiration time to 1 minute from now
                ]);
                $data['title'] = "Horus APP";
                $data['otp'] = $otp;
                $email = $tourist->email;
                Mail::send('setotp',  ['otp' => $data['otp']], function ($message) use ($data, $email) {
                $message->to($email)->subject($data['title']);
        });
                return response()->json(['message' => 'Email exists and OTP created'], 200);
            }
        } elseif ($emailType == 1) {
            // Check if the email exists in the Tourguide model
            $tourguide = Tourguide::where('email', $email)->first();
            if ($tourguide) {
                // Check if there's a recent OTP request for this tourguide
                $recentOTPRequest = OTP::where('tourguide_id', $tourguide->id)->where('created_at', '>=', now()->subMinutes($cooldownMinutes))->first();
                if ($recentOTPRequest) {
                    // If a recent OTP request exists, return a message indicating the cooldown period
                    return response()->json(['message' => 'Too many requests. Please wait for ' . $cooldownMinutes . ' minutes before requesting a new OTP.'], 429);
                }
                // Generate OTP
                $otp = $this->generateOtp();
                // Create OTP record for tourguide
                OTP::create([
                    'tourguide_id' => $tourguide->id,
                    'otp' => $otp,
                    'expires_at' => now()->addMinutes(5) // Set expiration time to 1 minute from now
                ]);
                $data['title'] = "Horus APP";
                $data['otp'] = $otp;
                $email = $tourguide->email;
                Mail::send('setotp',  ['otp' => $data['otp']], function ($message) use ($data, $email) {
            $message->to($email)->subject($data['title']);
        });
                return response()->json(['message' => 'Email exists and OTP created'], 200);
            }
        }

        // If the email does not exist in the specified model, return an error response
        return response()->json(['message' => 'Email not found'], 404);
    }

    // Function to generate OTP of 4 random characters
    private function generateOtp()
    {
        return str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
    }

    // Method to delete expired OTP codes
    private function deleteExpiredOTPCodes()
    {
        OTP::where('expires_at', '<', now())->delete();
    }

    public function verifyOtpAndUpdatePassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:4',
            'password' => 'required|string|min:8|confirmed', // Ensure confirmation with password_confirmation field
            'email_type' => 'required|boolean', // Validate email_type as boolean
        ]);
    
        $email = $request->input('email');
        $otp = $request->input('otp');
        $emailType = $request->input('email_type');
    
        if ($emailType == 0) {
            $user = Tourist::where('email', $email)->first();
        } elseif ($emailType == 1) {
            $user = Tourguide::where('email', $email)->first();
        } else {
            return response()->json(['message' => 'Invalid email type'], 400);
        }
    
        // If user not found, return error
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
    
        // Verify OTP
        if ($emailType == 0) {
            $otpRecord = OTP::where('otp', $otp)->where('expires_at', '>=', now())->where('tourist_id', $user->id)->first();
        } elseif ($emailType == 1) {
            $otpRecord = OTP::where('otp', $otp)->where('expires_at', '>=', now())->where('tourguide_id', $user->id)->first();
        }
    
        if ($otpRecord) {
            // OTP is valid, delete the OTP record
            $otpRecord->delete();
    
            // Update the user's password
            $user->password = Hash::make($request->password);
            $user->save();
    
            return response()->json(['message' => 'OTP is valid and password updated successfully'], 200);
        } else {
            // OTP is either expired or incorrect
            return response()->json(['message' => 'OTP is expired or incorrect'], 400);
        }
    }
    

}
