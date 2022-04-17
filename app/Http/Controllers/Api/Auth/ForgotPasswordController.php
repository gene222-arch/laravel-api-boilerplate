<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Http\Requests\Api\Auth\ResetPasswordRequest;
use App\Http\Requests\Api\Auth\ForgotPasswordRequest;
use App\Models\PasswordReset;
use App\Models\User;

class ForgotPasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware([
            'throttle:6,1',
            'guest:api',
        ]);
    }   

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $status = Password::sendResetLink($request->only('email'));

        if (! $status === Password::RESET_LINK_SENT) {
            return $this->error(trans($status));
        }

        return $this->success(trans($status));
    }

    public function reset(ResetPasswordRequest $request)
    {
        $user = User::firstWhere('email', $request->email);

        if (! Password::tokenExists($user, $request->token)) {
            return $this->error(trans(Password::INVALID_TOKEN), 400);
        }

        $status = Password::reset(
            $request->only(['email', 'password', 'password_confirmation', 'token']),
            function ($user) use ($request)
            {
                $user->update([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ]);
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return $this->error('Password reset unsuccessful.');
        }

        return $this->success('Password updated successfully.');
    }
}
