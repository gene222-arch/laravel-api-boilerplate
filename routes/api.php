<?php

use App\Http\Controllers\Api\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\VerificationController;
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

    Route::controller(ForgotPasswordController::class)->group(function ()
    {
        Route::post('forgot-password', 'forgotPassword')->name('forgot.password');
        Route::post('reset-password', 'reset')->name('reset.password');
    });
});

Route::group([
    'prefix' => 'email',
    'as' => 'verification.',
], function () 
{
    Route::controller(VerificationController::class)->group(function ()
    {
        Route::get('verify/{user}', 'verify')->name('verify');
        Route::get('resend', 'resend')->name('resend');
    });
});

Route::middleware('auth:api')->group(function ()
{
    Route::post('auth/logout', [LoginController::class, 'logout'])->name('auth.logout');
});