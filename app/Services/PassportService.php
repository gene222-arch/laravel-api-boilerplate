<?php 
namespace App\Services;

use App\Traits\HasApiResponse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Passport\Passport;
use Laravel\Passport\PersonalAccessTokenResult;
use Symfony\Component\HttpFoundation\Response;

class PassportService
{
    use HasApiResponse;

    public function generateToken(
        PersonalAccessTokenResult $token,
        ?string $message = null,
        $data = null,
        int $code = Response::HTTP_OK,
    ): JsonResponse
    {
        $response = [
            'access_token' => $token->accessToken,
            'token_type' => 'Bearer',
            'expired_at' => Carbon::parse($token->token->expires_at)->toDateTime(),
            'data' => $data,
        ];

        return $this->success($message, $response, $code);
    }

    public static function personalAccessToken(Request $request): PersonalAccessTokenResult
    {
        if (
            $request->has('remember_me') &&
            $request->boolean('remember_me')
        ) {
            Passport::personalAccessTokensExpireIn(
                Carbon::now()->addDays(config('passport.remember_me_days'))
            );
        }

        return auth()->user()->createToken(config('passport.personal_access_client.secret'));
    }
}