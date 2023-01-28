<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class InvalidCredentialsException extends Exception
{
    protected $message = 'Invalid credentials';
    protected $code = Response::HTTP_UNAUTHORIZED;
}
