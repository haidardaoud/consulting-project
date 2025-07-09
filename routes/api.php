<?php

use App\Http\Controllers\usercontroller;
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
Route::post('signup',[usercontroller::class,'store']);
Route::post('login',[usercontroller::class,'login']);
Route::post('addExpert',[\App\Http\Controllers\expertcontroller::class,'store']);

Route::middleware('auth:sanctum')->group(function (){
    Route::get('logout',[usercontroller::class,'logout']);
    Route::post('updateUser',[usercontroller::class,'update']);
    Route::get('show/{consult}',[usercontroller::class,'show']);
    Route::post('expert-time',[\App\Http\Controllers\expertcontroller::class,'expert_time']);
    Route::post('addConsult',[\App\Http\Controllers\consultcontroller::class,'addconsult']);
    Route::post('search',[usercontroller::class,'search']);
    Route::get('show_expertdetails/{expert_id}',[\App\Http\Controllers\expertcontroller::class,'show']);
    Route::post('reserve/{id}',[usercontroller::class,'reserve']);
    Route::post('addrate/{expert_id}',[usercontroller::class,'addrate']);
    Route::get('allrate/{expert_id}',[usercontroller::class,'all_rate']);
    Route::post('addfavourite/{expert_id}',[usercontroller::class,'favourites']);
    Route::get('allfavourite',[usercontroller::class,'allfavourite']);
    Route::get('getreserve/{expert_id}',[usercontroller::class,'getreserve']);
    Route::post('charge',[usercontroller::class,'charge']);
});


