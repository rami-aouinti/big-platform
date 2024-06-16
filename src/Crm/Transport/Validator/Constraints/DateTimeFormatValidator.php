<?php

declare(strict_types=1);

namespace App\Crm\Transport\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class DateTimeFormatValidator extends ConstraintValidator
{
    /**
     * @param string|mixed|null $value
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!($constraint instanceof DateTimeFormat)) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\DateTimeFormat');
        }

        if (!\is_string($value)) {
            return;
        }

        if ($constraint->separator === null || $constraint->separator === '') {
            if (str_contains($value, ',')) {
                $this->context->buildViolation('The given value should not contain a comma.')
                    ->setTranslationDomain('validators')
                    ->setCode(DateTimeFormat::INVALID_FORMAT)
                    ->addViolation();
            }

            $this->validateDateTime($value);

            return;
        }

        foreach (explode($constraint->separator, $value) as $v) {
            $this->validateDateTime($v);
        }
    }

    private function validateDateTime(mixed $value): void
    {
        $valid = true;

        if (!\is_string($value)) {
            $valid = false;
        } else {
            try {
                $test = new \DateTime($value);
            } catch (\Exception $ex) {
                $valid = false;
            }
        }

        if ($valid === false) {
            $this->context->buildViolation(DateTimeFormat::getErrorName(DateTimeFormat::INVALID_FORMAT))
                ->setTranslationDomain('validators')
                ->setCode(DateTimeFormat::INVALID_FORMAT)
                ->addViolation();
        }
    }
}
