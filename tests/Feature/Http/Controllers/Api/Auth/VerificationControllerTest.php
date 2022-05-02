<?php

namespace Tests\Feature\Http\Controllers\Api\Auth;

use App\Jobs\QueueEmailVerification;
use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\Queue;
use App\Notifications\EmailVerification;

class VerificationControllerTest extends TestCase
{
    /**
     * @test
     */
    public function user_can_verify_email()
    {
        $user = User::factory()
            ->has(Profile::factory(), 'profile')
            ->unverified()
            ->create();

        $this->assertFalse($user->hasVerifiedEmail());

        $notification = new EmailVerification();
        $mail = $notification->toMail($user);

        $transformVerificationUrl = str($mail->actionUrl)
            ->replace(
                env('CLIENT_APP_URL', 'http://localhost:3000'),
                env('APP_URL' . '/api', '/api')
            );

        $this->get($transformVerificationUrl)
            ->assertSuccessful();

        $user = User::first();
        $this->assertTrue($user->hasVerifiedEmail());
    }

    /**
     * @test
     */
    public function user_can_resend_email_verification()
    {
        Queue::fake();

        $user = User::factory()
            ->has(Profile::factory(), 'profile')
            ->unverified()
            ->create();

        $this->assertFalse($user->hasVerifiedEmail());

        $this->get(route('verification.resend', [
            'email' => $user->email
        ]));

        Queue::assertPushed(QueueEmailVerification::class);
    }
}
