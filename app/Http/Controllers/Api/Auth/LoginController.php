<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Api\Auth\LoginRequest;
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

        $user = auth()->user();

        return $service->generateToken(
            PassportService::personalAccessToken($request),
            'Logged in successfully.',
            [
                'id' => $user->id,
                'first_name' => $user->detail->first_name,
                'last_name' => $user->detail->last_name,
                'email' => $user->email,
                'birthed_at' => $user->detail->birthed_at,
                'email_verified_at' => $user->email_verified_at,
            ],
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
