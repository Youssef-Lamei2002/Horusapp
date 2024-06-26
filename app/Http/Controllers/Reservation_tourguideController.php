<?php

namespace App\Http\Controllers;

use App\Http\Requests\StripeRequest;
use App\Models\Reservation_tourguide;
use App\Models\Tourguide;
use App\Models\Tourist;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Stripe\StripeClient;

class Reservation_tourguideController extends Controller
{    
    protected $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(env('STRIPE_SECRET'));
    }
    public function createReservation(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'tourist_id' => 'required|exists:tourists,id',
            'tourguide_id' => 'required|exists:tourguides,id',
            'landmark_id' => 'required|exists:landmarks,id',
            'hours' => 'required|numeric|min:1',
            'starting_time' => 'required',
            'finished_time' => 'required|after:starting_time',
            'day' => 'required',
        ]);
    
        // Fetch price per hour from the tourguide table
        $tourguide = Tourguide::findOrFail($validatedData['tourguide_id']);
        $pricePerHour = $tourguide->price;
    
        // Create the reservation
        $reservation = Reservation_tourguide::create([
            'tourist_id' => $validatedData['tourist_id'],
            'tourguide_id' => $validatedData['tourguide_id'],
            'landmark_id' => $validatedData['landmark_id'],
            'hours' => $validatedData['hours'],
            'price_of_hour' => $pricePerHour,
            'isAccepted' => false,
            'isFinished' => false,
            'starting_time' => $validatedData['starting_time'],
            'finished_time' => $validatedData['finished_time'],
            'day' => $validatedData['day'],
        ]);
    
        return response()->json($reservation, 200);
    }
    public function approval_reservation(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'id' => 'required|exists:reservation_tourguides,id',
            'isAccepted' => 'required|boolean',
        ]);

        // Find the reservation
        $reservation = Reservation_tourguide::findOrFail($validatedData['id']);

        // Update the reservation's approval status
        $reservation->isAccepted = $validatedData['isAccepted'];

        // If rejected, also set isFinished to true
        if (!$validatedData['isAccepted']) {
            $reservation->isFinished = true;
        }

        $reservation->save();

        // Return a JSON response indicating success
        return response()->json(['message' => 'Reservation approval status updated successfully'], 200);
    }
    public function reservation_request_for_tour_guide($tourguideId)
    {

        // Fetch reservations for the specific tour guide where isAccepted is not 1
        $reservations = Reservation_tourguide::where('tourguide_id', $tourguideId)
        ->where('isFinished', '!=', 1) // Exclude reservations where isFinished is 1
        ->where('isAccepted', 0) // Only include reservations where isAccepted is 0
        ->where('created_at', '>', Carbon::now()->subHours(10)) // Only include reservations created within the last 10 hours
        ->with('tourist:id,name,profile_pic,email') // Eager load tourist with specified fields
        ->with('landmark:id,name') // Eager load landmark with specified fields
        ->get();

        return response()->json(['reservations' => $reservations], 200);
    }
    public function StripePayment(StripeRequest $stripeRequest, $id)
    {
        $cardData = $stripeRequest->all();
        // Get the current date
        $currentDate = now()->toDateString();
        
        // Fetch reservations for the specific tourist and include tour guide details
        $reservation = Reservation_tourguide::where('day', '>=', $currentDate)->find($id);
        if (!$reservation) {
            return response()->json(['error' => 'Reservation not found'], 404);
        }
    
        try {
            $token = $this->stripe->tokens->create([
                'card' => [
                    'name' => $cardData['card_name'],
                    'number' => $cardData['card_number'],
                    'exp_month' => $cardData['exp_month'],
                    'exp_year' => $cardData['exp_year'],
                    'cvc' => $cardData['cvc'],
                ],
            ]);
    
            $charge = $this->stripe->charges->create([
                "amount" => $reservation->price_of_hour * $reservation->hours * 100,
                "currency" => 'EGP',
                "source" => $token->id,
            ]);
    
            // Check if the charge was successful
            if ($charge->status === 'succeeded') {
                // Update the isFinished field to true
                $reservation->isFinished = true;
                $reservation->save();
                return response()->json(['success' => 'Payment processed and reservation updated'], 200);
            } else {
                return response()->json(['error' => 'Charge was not successful'], 400);
            }
    
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to process payment'], 500);
        }
    }
    
    
    public function reservation_request_for_tourist($touristId)
    {        
        // Fetch reservations for the specific tourist where isAccepted is 1
        $reservations = Reservation_tourguide::where('tourist_id', $touristId)
            ->where('created_at', '>', Carbon::now()->subHours(10))
            ->where('isAccepted', 1) // Only include reservations created within the last 10 hours
            ->with('tourguide:id,name,profile_pic,email') // Eager load tour guide with specified fields
            ->with('landmark:id,name')
            ->orWhere('isFinished', 1)
            ->get()
            ->filter(function ($reservation) {
                // Exclude reservations where both isAccepted and isFinished are true
                return !($reservation->isAccepted && $reservation->isFinished);
            })
            ->values(); // Reset the keys
    
        return response()->json(['reservations' => $reservations], 200);
    }
    

}
