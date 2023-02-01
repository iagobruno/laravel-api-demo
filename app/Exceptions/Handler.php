<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
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
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        $exceptionClass = get_class($exception);
        // dump($exceptionClass);
        $status = match ($exceptionClass) {
            ValidationException::class => Response::HTTP_UNPROCESSABLE_ENTITY,
            AuthenticationException::class => Response::HTTP_UNAUTHORIZED,
            AuthorizationException::class => Response::HTTP_FORBIDDEN,
            ModelNotFoundException::class => Response::HTTP_NOT_FOUND,
            ThrottleRequestsException::class => Response::HTTP_TOO_MANY_REQUESTS,
            default => $exception->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR,
        };
        $message = match ($exceptionClass) {
            ValidationException::class => 'The given data was invalid.',
            AuthenticationException::class => 'Unauthenticated.',
            AuthorizationException::class => 'You cannot perform this action.',
            ModelNotFoundException::class => 'Resource not found.',
            ThrottleRequestsException::class => 'Too many requests per minute.',
            default => $exception->getMessage() ?: 'Internal server error.',
        };

        $error = compact([
            'message',
            'status'
        ]);
        if ($exception instanceof ValidationException) {
            $error['errors'] = $exception->errors();
        }
        return response()->json($error, $status);
    }
}
