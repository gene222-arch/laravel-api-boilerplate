<?php

namespace Tests\Feature\Http\Controllers\Api\Auth;

use App\Models\User;
use App\Models\Profile;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    /**
     * @test
     */
    public function user_can_login()
    {
        $user = User::factory()
            ->has(Profile::factory(), 'profile')
            ->create();

        $credentials = [
            'email' => $user->email,
            'password' => 'password',
        ];

        $this->post(route('auth.login'), $credentials)
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    'access_token',
                    'token_type',
                    'expired_at',
                    'data' => [
                        'id',
                        'first_name',
                        'last_name',
                        'email',
                        'date_of_birth',
                        'email_verified_at',
                    ],
                ],
                'message',
                'status',
            ]);
    }

    /**
     * @test
     */
    public function user_can_logout()
    {
        $user = User::factory()
            ->has(Profile::factory(), 'profile')
            ->create();

        $credentials = [
            'email' => $user->email,
            'password' => 'password',
        ];

        $loginResponse = $this->post(route('auth.login'), $credentials)
            ->assertSuccessful();

        $data = $loginResponse['data'];

        $this->post(route('auth.logout'), [], [
            'Authorization' => "{$data['token_type']} {$data['access_token']}"
        ])
            ->assertSuccessful();
    }
}
