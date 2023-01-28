<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var string[]
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var string[]
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (Throwable $exception, Request $request) {
            $exceptionClass = get_class($exception);
            // dump($exceptionClass);
            $status = match ($exceptionClass) {
                ValidationException::class => Response::HTTP_UNPROCESSABLE_ENTITY,
                AccessDeniedHttpException::class => Response::HTTP_FORBIDDEN,
                NotFoundHttpException::class => Response::HTTP_NOT_FOUND,
                ThrottleRequestsException::class => Response::HTTP_TOO_MANY_REQUESTS,
                AuthenticationException::class => Response::HTTP_UNAUTHORIZED,
                default => $exception->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR,
            };
            $message = match ($exceptionClass) {
                AccessDeniedHttpException::class => 'You cannot perform this action.',
                NotFoundHttpException::class => 'Resource not found.',
                ThrottleRequestsException::class => 'Too many requests per minute.',
                AuthenticationException::class => 'Unauthenticated.',
                default => $exception->getMessage() ?: 'Internal server error.',
            };

            return response()->json(array_merge(
                compact([
                    'message',
                    'status'
                ]),
                ($exception instanceof ValidationException ? [
                    'errors' => $exception->errors(),
                ] : []),
                (config('app.debug') === true ? [
                    'trace' => $exception->getTrace(),
                ] : [])
            ), $status);
        });
    }
}
