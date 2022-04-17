<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Traits\HasApiResponse;
use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class EnsureEmailIsVerified
{
    use HasApiResponse;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $redirectToRoute
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|null
     */
    public function handle($request, Closure $next, $redirectToRoute = null)
    {
        /**
         * Check unauthenticated user email
         */
        if (
            $request->has('email') &&
            $request->filled('email')
        ) {
            if (! $user = User::firstWhere('email', $request->email)) {
                return $this->error('Account does not exists.', null, 400);
            }

            if (! $user->hasVerifiedEmail()) {
                return $this->error('Your email is not verified.', null, 403);
            }

            return $next($request);
        }

        /**
         * Check authenticated user
         */
        if (
            $request->bearerToken() &&
            ! $request->user('api') ||
            ($request->user('api') instanceof MustVerifyEmail &&
            ! $request->user('api')->hasVerifiedEmail())
        ) {
            if ($request->wantsJson()) {
                return $this->error('Your email is not verified.', null, 403);
            }
        }

        return $next($request);
    }
}
