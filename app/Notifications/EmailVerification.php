<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;
use Illuminate\Auth\Notifications\VerifyEmail;

class EmailVerification extends VerifyEmail
{
    use Queueable;

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  \App\Models\User  $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        if (static::$createUrlCallback) {
            return call_user_func(static::$createUrlCallback, $notifiable);
        }

        $url = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        $apiUrl = str(env('APP_URL'))->append('/api');
        $reactAppUrl = str(env('REACT_APP_URL', 'http://localhost:3000'))->append('/auth');
        $url = str($url)->replace($apiUrl, $reactAppUrl);

        return $url;
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase($notifiable): array
    {
        return [
            'user' => $notifiable
        ];  
    }
}
