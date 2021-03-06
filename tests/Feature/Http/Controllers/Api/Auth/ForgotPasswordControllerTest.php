<?php

namespace Tests\Feature\Http\Controllers\Api\Auth;

use App\Jobs\QueuePasswordResetNotification;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ForgotPasswordControllerTest extends TestCase
{
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    /**
     * @test
     */
    public function user_can_receive_forgot_password_reset_link()
    {
        $user = User::factory()
            ->has(Profile::factory(), 'profile')
            ->create();

        $this->post(route('auth.password.forgot'), [
            'email' => $user->email,
        ]);

        Queue::assertPushed(QueuePasswordResetNotification::class);
    }

    /**
     * @test
     */
    public function user_does_not_receive_password_reset_link()
    {
        $this->post(route('auth.password.forgot'), [
            'email' => $this->faker()->safeEmail()
        ]);

        Queue::assertNotPushed(QueuePasswordResetNotification::class);
    }

    /**
     * @test
     */
    public function user_can_reset_password()
    {
        $user = User::factory()
            ->has(Profile::factory(), 'profile')
            ->create([
                'password' => Hash::make('password')
            ]);

        $token = Password::createToken($user);

        $data = [
            'token' => $token,
            'email' => $user->email,
            'password' => 'New Password',
            'password_confirmation' => 'New Password'
        ];

        $this->post(route('auth.password.reset'), $data)
            ->assertSuccessful();

        $this->assertFalse(Hash::check('New Password', Hash::make('password')));
    }
}
