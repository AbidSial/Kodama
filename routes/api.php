<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\Homecontroller;
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
Route::post("register",[OnboardingController::class,'Register']);
Route::post("login",[LoginController::class,'Login']);
Route::post("home_experiences",[homecontroller::class,'homeExperiences']);
Route::post("addlocation",[DataController::class,'AddLocation']);
Route::post("addexperience",[DataController::class,'AddExperience']);
Route::post("checkphoneavailablity",[OnboardingController::class,'CheckPhoneAvailablity']);
Route::post("checkemailavailablity",[OnboardingController::class,'CheckEmailAvailablity']);
Route::group(['middleware' => ['jwt.verify']], function() {
Route::post('getuserdetail',[OnboardingController::class,'GetUserDetail']);
Route::post('getlist',[DataController::class,'getlist']);
//Route::post("viewfeature",[homecontroller::class,'feature']);
});
