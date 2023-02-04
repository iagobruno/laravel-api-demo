<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Laravel\Sanctum\Exceptions\MissingAbilityException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
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
        $shouldReturnJson = $request->is('api/*') || $request->expectsJson();
        if ($shouldReturnJson) {
            return $this->convertExceptionToJsonResponse($exception);
        }

        return parent::render($request, $exception);
    }

    protected function convertExceptionToJsonResponse(Throwable $exception): JsonResponse
    {
        $exceptionClass = get_class($exception);
        // dump($exceptionClass);
        $status = match ($exceptionClass) {
            ModelNotFoundException::class => Response::HTTP_NOT_FOUND,
            ValidationException::class => Response::HTTP_UNPROCESSABLE_ENTITY,
            AuthenticationException::class => Response::HTTP_UNAUTHORIZED,
            AuthorizationException::class => Response::HTTP_FORBIDDEN,
            MissingAbilityException::class => Response::HTTP_FORBIDDEN,
            ThrottleRequestsException::class => Response::HTTP_TOO_MANY_REQUESTS,
            default => $exception->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR,
        };
        $message = match ($exceptionClass) {
            ModelNotFoundException::class => class_basename($exception->getModel()) . ' not found.',
            ValidationException::class => 'The given data was invalid.',
            AuthenticationException::class => "You did not provide an API token. You need to provide it in the Authorization header.",
            AuthorizationException::class => str_replace(
                'This action is unauthorized.',
                "You don't have permission to perform this action.",
                $exception->getMessage()
            ),
            MissingAbilityException::class => 'The provided API token does not have permission to perform this action.',
            ThrottleRequestsException::class => 'Too many request attempts in too short a time.',
            default => $exception->getMessage() ?: 'Internal server error.',
        };

        $error = compact([
            'message',
            'status'
        ]);
        if (config('app.debug') === true) {
            $error['exception'] = get_class($exception);
        }
        if ($exception instanceof ValidationException) {
            $error['errors'] = $exception->errors();
        }
        return response()->json($error, $status);
    }
}
