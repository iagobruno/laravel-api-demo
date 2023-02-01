<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class RouteNotFoundException extends Exception
{
    protected $message = 'Route not found';
    protected $code = Response::HTTP_NOT_FOUND;
}
