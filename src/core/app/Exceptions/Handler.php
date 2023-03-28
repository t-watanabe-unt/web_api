<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
        $this->renderable(
            function (Throwable $e, $request) {
                // APIルーティング
                if ($request->is('api/*')) {
                    if ($e instanceof HttpException) {
                        $statusCode = $e->getStatusCode();
                        switch ($statusCode) {
                        case 403:
                            $message = __('messages.exception.400');
                            break;
                        case 404:
                            $message = __('messages.exception.404');
                            break;
                        case 405:
                            $message = __('messages.exception.405');
                            break;
                        case 500:
                            $message = __('messages.exception.500');
                            break;
                        default:
                            return;
                        }

                        return response()->json(
                            [
                            'code' => $statusCode,
                            'message' => $message,
                            ], $statusCode
                        );
                    }

                    // HttpException 以外の場合
                    return response()->json(
                        [
                        'status' => 500,
                        'message' => __('messages.exceptioin.500'),
                        ], 500, [
                        'Content-Type' => 'application/problem+json',
                        ]
                    );
                }
            }
        );

        $this->reportable(
            function (Throwable $e) {
                //
            }
        );
    }
}
