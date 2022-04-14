<?php

namespace Tests\Feature\Http\Controllers\Api\Auth;

use App\Jobs\QueuePasswordResetNotification;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ForgotPasswordControllerTest extends TestCase
{
    use RefreshDatabase;
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
            ->has(UserDetail::factory(), 'detail')
            ->create();

        $this->post('/api/auth/forgot-password', [
            'email' => $user->email,
        ]);

        Queue::assertPushed(QueuePasswordResetNotification::class);
    }

    /**
     * @test
     */
    public function user_does_not_receive_password_reset_link()
    {
        $this->post('api/auth/forgot-password', [
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
            ->has(UserDetail::factory(), 'detail')
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

        $this->post('/api/auth/reset-password', $data)
            ->assertSuccessful();

        $this->assertFalse(Hash::check('New Password', Hash::make('password')));
    }
}
