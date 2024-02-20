<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GameController;

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

Route::post('/auth/register',[UserController::class,"register"]);
Route::post('/auth/login',[UserController::class,"login"]);


Route::get('/game/leaderboard',[GameController::class,"leaderboard"]);

Route::post('/game/start',[GameController::class,"start"])->middleware('auth:sanctum');
Route::post('/game/end',[GameController::class,"end"])->middleware('auth:sanctum');
