<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ResController;
use App\Http\Controllers\AccountController;


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

Route::group([
    'middleware' => 'api',
    // 'prefix' => 'auth'
], function ($router) {

    Route::group([
        'prefix' => 'auth'
    ], function ($router) {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::post('token/issue', [AuthController::class, 'tokenIssue']);


        Route::group(['middleware' => ['jwt.verify']], function () {
            Route::get('me', [AuthController::class, 'me']);
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('allogout', [AuthController::class, 'logoutAll']);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | accounts
    |--------------------------------------------------------------------------
    */
    Route::group([
        'prefix' => 'accounts'
    ], function ($router) {

        // Route::post('{deleted}/restore', [AccountController::class, 'restore']);
    });
    Route::resource('accounts', AccountController::class);

});

Route::group([
    'middleware' => 'api',
    'prefix' => 'resource'
], function ($router) {

    Route::get('user', [ResController::class, 'user']);
});

Route::any('{any}', function () {
    return response()->json([
        'status' => 'error',
        'message' => 'Resource not found'
    ], 404);
})->where('any', '.*');
