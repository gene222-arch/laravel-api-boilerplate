<?php

namespace Tests\Feature\Http\Controllers\Api\Auth;

use App\Jobs\QueueEmailVerification;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;

class RegisterControllerTest extends TestCase
{
    use WithFaker;

    /**
     * @test
     */
    public function user_can_register_and_received_a_queued_email_verification()
    {
        Queue::fake();

        $password = Str::random();

        $data = [
            'first_name' => $this->faker()->firstName(),
            'last_name' => $this->faker()->lastName(),
            'email' => $this->faker()->safeEmail(),
            'password' => $password,
            'password_confirmation' => $password,
            'date_of_birth' => Carbon::now()->subDays(random_int(1, 30)),
        ];

        $this->post(route('auth.register'), $data)
            ->assertCreated()
            ->assertJsonStructure([
                'data' => [
                    'access_token',
                    'token_type',
                    'expired_at',
                    'data',
                ],
                'message',
                'status'
            ]);

        Queue::assertPushed(QueueEmailVerification::class);
    }
}
