<?php

declare(strict_types=1);

namespace App\Crm\Transport\Validator\Constraints;

use App\Crm\Domain\Entity\Timesheet as TimesheetEntity;
use App\Configuration\SystemConfiguration;
use App\Crm\Domain\Repository\TimesheetRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class TimesheetOverlappingValidator
 *
 * @package App\Crm\Transport\Validator\Constraints
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class TimesheetOverlappingValidator extends ConstraintValidator
{
    public function __construct(
        private readonly SystemConfiguration $configuration,
        private readonly TimesheetRepository $repository
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!($constraint instanceof TimesheetOverlapping)) {
            throw new UnexpectedTypeException($constraint, TimesheetOverlapping::class);
        }

        if (!\is_object($value) || !($value instanceof TimesheetEntity)) {
            throw new UnexpectedTypeException($value, TimesheetEntity::class);
        }

        $begin = $value->getBegin();
        $end = $value->getEnd();

        // this case is handled in TimesheetValidator and should not raise a second validation
        if ($begin !== null && $end !== null && $begin > $end) {
            return;
        }

        if ($this->configuration->isTimesheetAllowOverlappingRecords()) {
            return;
        }

        if (!$this->repository->hasRecordForTime($value)) {
            return;
        }

        $this->context->buildViolation('You already have an entry for this time.')
            ->atPath('begin_date')
            ->setTranslationDomain('validators')
            ->setCode(TimesheetOverlapping::RECORD_OVERLAPPING)
            ->addViolation();
    }
}
