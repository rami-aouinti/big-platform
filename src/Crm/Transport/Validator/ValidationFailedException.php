<?php

declare(strict_types=1);

namespace App\Crm\Transport\Validator;

use Symfony\Component\Validator\ConstraintViolationListInterface;

final class ValidationFailedException extends \RuntimeException
{
    public function __construct(
        private ConstraintViolationListInterface $violations,
        ?string $message = null
    ) {
        if ($message === null) {
            $message = 'Validation failed';
        }
        parent::__construct($message, 400);
    }

    public function getViolations(): ConstraintViolationListInterface
    {
        return $this->violations;
    }
}
