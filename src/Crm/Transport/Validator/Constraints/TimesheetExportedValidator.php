<?php

declare(strict_types=1);

namespace App\Crm\Transport\Validator\Constraints;

use App\Crm\Domain\Entity\Timesheet as TimesheetEntity;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @package App\Crm\Transport\Validator\Constraints
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class TimesheetExportedValidator extends ConstraintValidator
{
    public function __construct(
        private readonly Security $security
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!($constraint instanceof TimesheetExported)) {
            throw new UnexpectedTypeException($constraint, TimesheetExported::class);
        }

        if (!\is_object($value) || !($value instanceof TimesheetEntity)) {
            throw new UnexpectedTypeException($value, TimesheetEntity::class);
        }

        if ($value->getId() === null) {
            return;
        }

        if (!$value->isExported()) {
            return;
        }

        if ($this->security->getUser() !== null && $this->security->isGranted('edit_export', $value)) {
            return;
        }

        $this->context->buildViolation(TimesheetExported::getErrorName(TimesheetExported::TIMESHEET_EXPORTED))
            ->atPath('exported')
            ->setTranslationDomain('validators')
            ->setCode(TimesheetExported::TIMESHEET_EXPORTED)
            ->addViolation();
    }
}
