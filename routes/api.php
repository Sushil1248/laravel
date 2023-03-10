<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{AuthController,UserController,ApiController,StephWorkoutController,ExerciseController,ProgressController,WorkoutController,UserRmController,UserWorkoutSetsController,WeeklyPlannerController,ForumController};
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

Route::post('register', [AuthController::class,'register']);
Route::post('resend-verification-link',[AuthController::class,'resendVerificationLink']);
// Route::post('login', [AuthController::class,'login']);

// API controller
Route::post('login', [ApiController::class,'login']);
Route::post('activate/device',[ApiController::class,'activateDevice']);

Route::post('forgot/password', [AuthController::class,'passwordResetLink']);
Route::post('verify/otp', [AuthController::class,'verifyOtp']);
Route::post('reset/password', [AuthController::class,'updateNewPassword']);
Route::get('get-countries',[ApiController::class,'getCountries']);
Route::get('get-states/{country}',[ApiController::class,'getStates']);
Route::get('get-cities/{state}',[ApiController::class,'getCities']);

Route::middleware(['auth:api','validateUser'])->group(function(){
    Route::get('logout', [ApiController::class,'logout']);
    Route::get('profile',[ApiController::class,'profile']);
    Route::post('update-profile',[ApiController::class,'updateProfile']);
    Route::post('update-device/{device_id}',[ApiController::class,'updateDeviceDetails']);
});


