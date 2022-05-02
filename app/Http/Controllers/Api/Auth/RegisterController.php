<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Models\User;
use App\Services\PassportService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request, PassportService $service)
    {
        $user = User::create([
            ...$request->validated(), 
            'password' => Hash::make($request->password)
        ]);

        $user->profile()->create($request->validated());

        $user->sendEmailVerificationNotification();

        if (! Auth::attempt($request->only(['email', 'password']))) {
            return $this->error('Registration failed.', 500);
        }

        return $service->generateToken(
            PassportService::personalAccessToken($request),
            'Registration successful.',
            null,
            201
        );
    }
}
