<?php

namespace App\Exceptions;

use App\Traits\APIResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    use APIResponse;
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e, Request $request) {
            $exception = $this->prepareException($e);

            if ($request->expectsJson() && $exception instanceof NotEnoughStockException) {
                return $this->errorResponse($exception->getMessage(), Response::HTTP_BAD_REQUEST);
            }
        });
    }
}
