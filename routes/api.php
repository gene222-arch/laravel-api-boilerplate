<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
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

Route::group([
    'prefix' => 'auth',
    'as' => 'auth.'
], function () {
    Route::post('login', [LoginController::class, 'login'])->name('login');
    Route::post('register', [RegisterController::class, 'register'])->name('register');
});

Route::middleware('auth:api')->group(function ()
{
    Route::post('auth/logout', [LoginController::class, 'logout'])->name('logout');
});