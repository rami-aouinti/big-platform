<?php

declare(strict_types=1);

namespace App\Validator;

final class ValidationException extends \RuntimeException
{
    public function __construct(string $message = null)
    {
        if ($message === null) {
            $message = 'Validation failed';
        }
        parent::__construct($message, 400);
    }
}
