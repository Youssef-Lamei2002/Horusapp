<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\Forget_passwordController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\LandmarkController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ProfileSettingController;
use App\Http\Controllers\Reservation_tourguideController;
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
Route::post('/create_landmark',[LandmarkController::class, 'create_landmark']);
Route::post('/delete_landmark',[LandmarkController::class, 'delete_landmark']);
Route::post('/update_landmark',[LandmarkController::class, 'update_landmark']);
Route::get('/read_landmark',[LandmarkController::class, 'read_landmark']);
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




