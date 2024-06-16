<?php

declare(strict_types=1);

namespace App\Crm\Transport\Validator;

/**
 * @package App\Crm\Transport\Validator
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
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
