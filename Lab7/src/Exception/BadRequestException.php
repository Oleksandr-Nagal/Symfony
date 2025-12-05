<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Кастомний Exception для помилок 400 Bad Request.
 * RequestCheckerService буде використовувати його, коли відсутні обов'язкові поля.
 */
class BadRequestException extends HttpException
{
    public function __construct(string $message, int $code)
    {
        parent::__construct(400, $message, null, [], $code);
    }
}