<?php 
namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

Trait HasApiResponse
{
    public function success(?string $message = '', $data = null, int $code = Response::HTTP_OK): JsonResponse
    {
        return $this->response($message, $data, $code, __FUNCTION__);
    }

    public function error(?string $message = '', $data = null, int $code = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return $this->response($message, $data, $code, __FUNCTION__);
    }

    private function response(?string $message = '', $data = null, int $code, string $status): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'status' => $status,
        ], $code);
    }
}