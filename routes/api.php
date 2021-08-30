<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\LoginController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post("Register",[OnboardingController::class,'Register']);
Route::post("login",[LoginController::class,'Login']);
Route::post("CheckPhoneAvailablity",[OnboardingController::class,'CheckPhoneAvailablity']);
Route::post("CheckEmailAvailablity",[OnboardingController::class,'CheckEmailAvailablity']);
Route::group(['middleware' => ['jwt.verify']], function() {
Route::post('GetUserDetail',[OnboardingController::class,'GetUserDetail']);
});
