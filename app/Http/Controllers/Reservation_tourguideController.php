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
            ->where('isFinished', '!=', 1) // Exclude reservations where isAccepted is 1
            ->where('isAccepted',0)
            ->where('created_at',Carbon::now()->addHours(10))
            ->with('tourist:id,name,profile_pic') // Eager load tourist with specified fields
            ->with('landmark:id,name')
            ->get();
    
        // Assuming Tourguide model exists to fetch tour guide details if needed
    
        return response()->json(['reservations' => $reservations], 200);
    }
    public function StripePayment(StripeRequest $stripeRequest,$id)
    {
        $cardData=$stripeRequest->all();
        // Get the current date
        $currentDate = now()->toDateString();
    
        // Fetch reservations for the specific tourist and include tour guide details
        $reservations = Reservation_tourguide::where('day', '>=', $currentDate)  ->find($id);
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
                    "amount" => $reservations->price_of_hour*$reservations->hours*100,
                    "currency" => 'EGP',
                    "source" => "$token->id",
                ]);
    
                return $charge;
    
            } catch (\Exception $e) {
                throw new \Exception('Failed to create Stripe token: ' . $e->getMessage());
            }

            
            return Response::json(['reservations' => $reservations], 200);
        }
    public function reservation_request_for_tourist($touristId)
    {
        $currentDate = now()->toDateString();
        // Fetch reservations for the specific tour guide where isAccepted is not 1
        $reservations = Reservation_tourguide::where('tourist', $touristId)
            ->where('isFinished', '!=', 1) // Exclude reservations where isAccepted is 1
            ->where('isAccepted',1)
            ->where('day', '>=', $currentDate)
            ->with('tourguide:id,name,profile_pic') // Eager load tourist with specified fields
            ->with('landmark:id,name')
            ->get();
    
        // Assuming Tourguide model exists to fetch tour guide details if needed
    
        return response()->json(['reservations' => $reservations], 200);
    }

}
