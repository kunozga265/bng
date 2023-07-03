<?php

use App\Http\Controllers\API\V1_0\AppController;
use App\Http\Controllers\API\V1_0\PlotController;
use App\Http\Controllers\API\V1_0\SiteController;
use App\Http\Controllers\API\V1_0\UserController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return new \App\Http\Resources\UserResource($request->user());
});
Route::group(['prefix' => '1.0'],function () {

    Route::group(['prefix' => 'users'], function () {
        Route::post("/login", [UserController::class, 'login']);

        Route::post('/register', [
            "uses" => "App\Http\Controllers\API\V1_0\UserController@register",
            'roles' => ['administrator']
        ]);
    });

    //Protected Routes
    Route::group(['middleware'=>'auth:sanctum'], function () {

        Route::get('/dashboard', [AppController::class, 'index']);

        Route::group(['prefix' => 'sites'], function () {
            Route::get('/', [SiteController::class, 'index']);
            Route::get('/{id}', [SiteController::class, 'show']);
            Route::get('/{id}/plots', [SiteController::class, 'plots']);
        });

    });

//    Route::group(['prefix' => 'bookings', 'middleware'=>'auth:sanctum'], function () {
//        Route::get('/', [SiteController::class, 'index']);
//    });
});
