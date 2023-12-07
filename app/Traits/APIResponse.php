<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait APIResponse
{
    /**
     * Return a success JSON response.
     *
     * @param array $data
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    public function successResponse(array $data, string $message, int $code = Response::HTTP_OK): JsonResponse
    {
        return response()->json(['data' => $data, 'message' => $message], $code);
    }

    /**
     * Return an error JSON response.
     *
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    public function errorResponse(string $message, int $code = Response::HTTP_INTERNAL_SERVER_ERROR): JsonResponse
    {
        return response()->json(['error' => $message], $code);
    }
}
