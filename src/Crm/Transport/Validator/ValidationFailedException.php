<?php

declare(strict_types=1);

namespace App\Crm\Transport\Validator;

use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * @package App\Crm\Transport\Validator
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class ValidationFailedException extends \RuntimeException
{
    public function __construct(
        private readonly ConstraintViolationListInterface $violations,
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
