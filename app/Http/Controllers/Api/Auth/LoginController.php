<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Resources\Api\UserResource;
use App\Models\User;
use App\Services\PassportService;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:api')->only('login');
        $this->middleware('verified');
    }

    public function login(LoginRequest $request, PassportService $service)
    {
        if (! Auth::attempt($request->only(['email', 'password']), $request->boolean('remember_me'))) {
            return $this->error('Login failed.', 500);
        }

        return $service->generateToken(
            PassportService::personalAccessToken($request),
            'Logged in successfully.',
            UserResource::make($request->user()->load('profile')),
        );
    }

    public function logout(Request $request)
    {
        $request
            ->user('api')
            ->token()
            ->revoke();

        return $this->success('Logged out successfully.');
    }
}
