<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Language;
use App\Models\Tourguide;
use App\Models\Tourguide_Language;
use App\Models\Tourist;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        
        $data=$request->all();
        $data['password']=Hash::make($data['password']);
        if (request()->email_type == 0)
        {
            $tourist=Tourist::create($data);  
        }
        elseif (request()->email_type == 1)
        {
            $tourguide=Tourguide::create($data);  
            foreach ($request ->languages as $language)
            {
                Tourguide_Language::create(['tourguide_id'=>$tourguide->id,'language_id'=>$language]);
            }
        }
        return response()->json(['message' => 'Successfully Registered']);
    }
    public function login(LoginRequest $request)
    {
        if (request()->email_type == 0)
        { 
            $credentials = request(['email', 'password']);
        if ($token = auth()->guard('tourists_api')->attempt($credentials)) {
            $user =  auth()->guard('tourists_api')->user();
            $user->token = $token;

            return $user;
         }
        }
        elseif (request()->email_type == 1)
        {
            $credentials = request(['email', 'password']);
            if ($token = auth()->guard('tourguides_api')->attempt($credentials)) {
                $user =  auth()->guard('tourguides_api')->user();
                $user->token = $token;
                return $user;
        }
        
        }

    }

}
