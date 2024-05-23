<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\LandmarkController;
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