<?php

declare(strict_types=1);

namespace App\Crm\Transport\Validator\Constraints;

use App\Crm\Domain\Entity\Timesheet as TimesheetEntity;
use App\Configuration\SystemConfiguration;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class TimesheetLongRunningValidator
 *
 * @package App\Crm\Transport\Validator\Constraints
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class TimesheetLongRunningValidator extends ConstraintValidator
{
    public function __construct(
        private readonly SystemConfiguration $systemConfiguration
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!($constraint instanceof TimesheetLongRunning)) {
            throw new UnexpectedTypeException($constraint, TimesheetLongRunning::class);
        }

        if (!\is_object($value) || !($value instanceof TimesheetEntity)) {
            throw new UnexpectedTypeException($value, TimesheetEntity::class);
        }

        if ($value->isRunning()) {
            return;
        }

        /** @var int $duration */
        $duration = $value->getCalculatedDuration();

        // one year is currently the maximum that can be logged (which is already not logically)
        // the database column could hold more data, but let's limit it here
        if ($duration > 31536000) {
            $this->context->buildViolation($constraint->maximumMessage)
                ->setTranslationDomain('validators')
                ->atPath('duration')
                ->setCode(TimesheetLongRunning::MAXIMUM)
                ->addViolation();

            return;
        }

        $maxMinutes = $this->systemConfiguration->getTimesheetLongRunningDuration();

        if ($maxMinutes <= 0) {
            return;
        }

        // float on purpose, because one second more than the configured minutes is already too long
        $minutes = $duration / 60;

        // allow maximum of the exact configured minutes
        if ($minutes <= $maxMinutes) {
            return;
        }

        $format = new \App\Crm\Application\Utils\Duration();
        $hours = $format->format($maxMinutes * 60);

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $hours)
            ->setTranslationDomain('validators')
            ->atPath('duration')
            ->setCode(TimesheetLongRunning::LONG_RUNNING)
            ->addViolation();
    }
}
