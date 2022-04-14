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
        if (
            $request->has('email') &&
            $user = User::firstWhere($request->email)
        ) {
            if (! $user->hasVerifiedEmail()) {
                return $this->error('Your email is not verified.', null, 403);
            }

            return $next($request);
        }

        if (
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
