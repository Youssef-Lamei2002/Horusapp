<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\Favorite_landmarkController;
use App\Http\Controllers\Forget_passwordController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\LandmarkController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ProfileSettingController;
use App\Http\Controllers\Reservation_tourguideController;
use App\Http\Controllers\TourguideController;
use App\Http\Controllers\TouristController;
use App\Http\Controllers\TransportationController;
use App\Models\Transportation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/tourguides/city/{cityId}', [AuthController::class, 'getTourGuides']);
Route::post('/create_landmark',[LandmarkController::class, 'create_landmark']);
Route::post('/delete_landmark',[LandmarkController::class, 'delete_landmark']);
Route::post('/update_landmark',[LandmarkController::class, 'update_landmark']);
Route::get('/read_landmark', [LandmarkController::class, 'read_landmark']);
Route::get('/read_landmark_type', [LandmarkController::class, 'read_landmark_type']);
Route::get('/read_landmark_recommended', [LandmarkController::class, 'read_landmark_recommended']);
Route::post('/create_hotel',[HotelController::class, 'create_hotel']);
Route::get('/read_hotel',[HotelController::class, 'read_hotel']);
Route::post('/delete_hotel',[HotelController::class, 'delete_hotel']);
Route::post('/update_hotel',[HotelController::class, 'update_hotel']);
Route::post('/create_booking_img',[HotelController::class, 'create_booking_img']);
Route::get('/read_booking_img',[HotelController::class, 'read_booking_img']);
Route::post('/update_booking_img',[HotelController::class, 'update_booking_img']);
Route::post('/delete_booking_img',[HotelController::class, 'delete_booking_img']);
Route::post('/create_transportation',[TransportationController::class, 'create_transportation']);
Route::post('/update_transportation',[TransportationController::class, 'update_transportation']);
Route::get('read_transportation',[TransportationController::class, 'read_transportation']);
Route::post('/delete_transportation',[TransportationController::class, 'delete_transportation']);
Route::post('/checkEmailExists', [Forget_passwordController::class, 'checkEmailExists']);
Route::post('/verifyOtpAndUpdatePassword', [Forget_passwordController::class, 'verifyOtpAndUpdatePassword']);
Route::post('/updatePassword', [Forget_passwordController::class, 'updatePassword']);
Route::post('/create_city', [CityController::class, 'create_city']);
Route::post('/update_city', [CityController::class, 'update_city']);
Route::post('/delete_city', [CityController::class, 'delete_city']);
Route::get('/read_city', [CityController::class, 'read_city']);
Route::post('/updateName', [ProfileSettingController::class, 'updateName']);
Route::post('/addLanguage', [ProfileSettingController::class, 'addLanguage']);
Route::post('/removeLanguage', [ProfileSettingController::class, 'removeLanguage']);
Route::post('/updatePassword', [ProfileSettingController::class, 'updatePassword']);
Route::post('/updateCity', [ProfileSettingController::class, 'updateCity']);
Route::get('/read_language', [LanguageController::class, 'read_language']);
Route::Post('/create_language', [LanguageController::class, 'create_language']);
Route::Post('/delete_language', [LanguageController::class, 'delete_language']);
Route::post('/createReservation', [Reservation_tourguideController::class, 'createReservation']);
Route::post('/artisanOrder', [LandmarkController::class, 'artisanOrder'])->name('artisanOrder');
Route::get('imgs/landmark/{imageName}', [LandmarkController::class, 'getLandmarkImage'])->name('landmark.image');
Route::post('/favorite_landmark', [Favorite_landmarkController::class, 'favourite'])->name('favorite.landmark');
Route::get('/read_favorite_landmarks', [Favorite_landmarkController::class, 'read_favorite_landmarks']);
Route::post('/remove_favourite', [Favorite_landmarkController::class, 'remove_favourite']);
Route::get('images/city/{name}', [ImageController::class, 'imageCity']);
Route::get('images/landmark/{name}', [ImageController::class, 'imageLandmark']);
Route::get('images/hotel/{name}', [ImageController::class, 'imageHotel']);
Route::get('images/booking_img/{name}',  [ImageController::class, 'imageBookingHotel']);
Route::get('images/transportation/{name}',  [ImageController::class, 'imagetransportation']);
Route::get('images/profile_pic/{name}',  [ImageController::class, 'imagetProfile_pic']);
Route::post('/reservations', [Reservation_tourguideController::class, 'createReservation']);
Route::post('/approval_reservation', [Reservation_tourguideController::class, 'approval_reservation'])->name('approval_reservation');
Route::get('/reservation_request_for_tour_guide/{tourguideId}', [Reservation_tourguideController::class, 'reservation_request_for_tour_guide']);
Route::get('/reservation_request_for_tourist/{touristId}', [Reservation_tourguideController::class, 'reservation_request_for_tour_guide']);

Route::post('/StripePayment/{Id}', [Reservation_tourguideController::class, 'StripePayment']);



Route::post('/tourguides', [TourguideController::class, 'deleteTourguide']);
Route::post('/tourists', [TouristController::class, 'deleteTourist']);
Route::get('/tourguides', [TourguideController::class, 'getAllTourguides']);
Route::get('/tourists', [TouristController::class, 'getAllTourists']);