<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Configuration\SystemConfiguration;
use App\Entity\Timesheet as TimesheetEntity;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class TimesheetZeroDurationValidator extends ConstraintValidator
{
    public function __construct(
        private readonly SystemConfiguration $configuration
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!($constraint instanceof TimesheetZeroDuration)) {
            throw new UnexpectedTypeException($constraint, TimesheetZeroDuration::class);
        }

        if (!\is_object($value) || !($value instanceof TimesheetEntity)) {
            throw new UnexpectedTypeException($value, TimesheetEntity::class);
        }

        if ($this->configuration->isTimesheetAllowZeroDuration()) {
            return;
        }

        if ($value->isRunning()) {
            return;
        }

        $duration = 0;
        if ($value->getEnd() !== null && $value->getBegin() !== null) {
            $duration = $value->getCalculatedDuration();
        }

        if ($duration <= 0) {
            $this->context->buildViolation($constraint->message)
                ->atPath('duration')
                ->setTranslationDomain('validators')
                ->setCode(TimesheetZeroDuration::ZERO_DURATION_ERROR)
                ->addViolation();
        }
    }
}
