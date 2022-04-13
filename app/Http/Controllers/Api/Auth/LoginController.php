<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Services\PassportService;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        if (! Auth::attempt($request->validated())) {
            return $this->error('Login failed.', 500);
        }

        return PassportService::generateToken(
            PassportService::personalAccessToken($request),
            'Logged in successfully.'
        );
    }
}