<?php

declare(strict_types=1);

namespace App\Crm\Transport\Validator\Constraints;

use App\Crm\Domain\Entity\Timesheet as TimesheetEntity;
use App\Configuration\SystemConfiguration;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class TimesheetFutureTimesValidator
 *
 * @package App\Crm\Transport\Validator\Constraints
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class TimesheetFutureTimesValidator extends ConstraintValidator
{
    public function __construct(
        private readonly SystemConfiguration $configuration
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!($constraint instanceof TimesheetFutureTimes)) {
            throw new UnexpectedTypeException($constraint, TimesheetFutureTimes::class);
        }

        if (!\is_object($value) || !($value instanceof TimesheetEntity)) {
            throw new UnexpectedTypeException($value, TimesheetEntity::class);
        }

        if ($this->configuration->isTimesheetAllowFutureTimes()) {
            return;
        }

        $now = new \DateTime('now', $value->getBegin()->getTimezone());

        // allow configured default rounding time + 1 minute - see #1295
        $allowedDiff = ($this->configuration->getTimesheetDefaultRoundingBegin() * 60) + 60;
        $nowTs = $now->getTimestamp() + $allowedDiff;
        if ($value->getBegin() !== null && $nowTs < $value->getBegin()->getTimestamp()) {
            $this->context->buildViolation(TimesheetFutureTimes::getErrorName(TimesheetFutureTimes::BEGIN_IN_FUTURE_ERROR))
                ->atPath('begin_date')
                ->setTranslationDomain('validators')
                ->setCode(TimesheetFutureTimes::BEGIN_IN_FUTURE_ERROR)
                ->addViolation();
        }

        $allowedDiff = ($this->configuration->getTimesheetDefaultRoundingEnd() * 60) + 60;
        $nowTs = $now->getTimestamp() + $allowedDiff;
        if ($value->getEnd() !== null && $nowTs < $value->getEnd()->getTimestamp()) {
            $this->context->buildViolation(TimesheetFutureTimes::getErrorName(TimesheetFutureTimes::END_IN_FUTURE_ERROR))
                ->atPath('end_time')
                ->setTranslationDomain('validators')
                ->setCode(TimesheetFutureTimes::END_IN_FUTURE_ERROR)
                ->addViolation();
        }
    }
}
