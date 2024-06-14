<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class HexColorValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof HexColor) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\HexColor');
        }

        $color = $value;

        if ($color === null || $color === '') {
            return;
        }

        if (!\is_string($color) || preg_match('/^#([0-9a-fA-F]{6}|[0-9a-fA-F]{3})$/i', $color) !== 1) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($color))
                ->setCode(HexColor::HEX_COLOR_ERROR)
                ->addViolation();
        }
    }
}
