<?php

use App\Http\Controllers\Api\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\VerificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Authentication Routes
|--------------------------------------------------------------------------
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
        Route::get('verify/{user:uuid}', 'verify')->name('verify');
        Route::get('resend', 'resend')
            ->name('resend')
            ->withoutMiddleware('signed');
    });
});

Route::middleware('auth:api')->group(function ()
{
    Route::post('auth/logout', [LoginController::class, 'logout'])->name('auth.logout');
});