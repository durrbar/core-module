<?php

namespace Modules\Core\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class DurrbarBadRequestException extends HttpException
{
    /**
     * Create a new "Bad Request" exception instance for REST Client.
     *
     * @param  string  $message
     * @param  int  $statusCode
     * @param  int  $code
     * @return void
     */
    public function __construct($message = SOMETHING_WENT_WRONG, $statusCode = Response::HTTP_BAD_REQUEST, ?Throwable $previous = null, array $headers = [], $code = 0)
    {
        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }
}
