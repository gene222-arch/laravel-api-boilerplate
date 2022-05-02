<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function __construct()
    {
        $this->middleware([
            'guest:api',
            'signed',
            'throttle:6,1',
        ]);
    }

    public function verify(Request $request, User $user)
    {
        if (! hash_equals((string) $user->uuid, (string) $user->getUuidKey())) {
            throw new AuthorizationException;
        }

        if (! hash_equals((string) $request->get('hash'), sha1($user->getEmailForVerification()))) {
            throw new AuthorizationException;
        }

        if (! $request->hasValidSignature()) {
            return $this->error('This token is invalid.');
        }

        if ($user->hasVerifiedEmail()) {
            return $this->error('Account has been verified already.');
        }

        $user->markEmailAsVerified();

        return $this->success('Account verified successfully.');
    }

    public function resend(Request $request)
    {
        $user = User::firstWhere('email', $request->email);

        if ($user->hasVerifiedEmail()) {
            return $this->error('Account has been verified already.', 400);
        }

        $user->sendEmailVerificationNotification();

        return $this->success('Email verification has been sent to your email address.');
    }
}
