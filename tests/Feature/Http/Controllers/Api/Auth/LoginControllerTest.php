<?php

namespace Tests\Feature\Http\Controllers\Api\Auth;

use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function user_can_login()
    {
        $user = User::factory()
            ->has(UserDetail::factory(), 'detail')
            ->create();

        $credentials = [
            'email' => $user->email,
            'password' => 'password',
        ];

        $this->post('/api/auth/login', $credentials)
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    'access_token',
                    'token_type',
                    'expired_at',
                    'data',
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
            ->has(UserDetail::factory(), 'detail')
            ->create();

        $credentials = [
            'email' => $user->email,
            'password' => 'password',
        ];

        $loginResponse = $this->post('/api/auth/login', $credentials)
            ->assertSuccessful();

        $data = $loginResponse['data'];

        $this->post('api/auth/logout', [], [
            'Authorization' => "{$data['token_type']} {$data['access_token']}"
        ])
            ->assertSuccessful();
    }
}
