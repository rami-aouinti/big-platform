<?php

declare(strict_types=1);

namespace App\Crm\Transport\Validator\Constraints;

use App\Crm\Transport\Form\Model\MultiUserTimesheet;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class TimesheetMultiUserValidator extends ConstraintValidator
{
    /**
     * @param Timesheet|mixed $value
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!($constraint instanceof TimesheetMultiUser)) {
            throw new UnexpectedTypeException($constraint, TimesheetMultiUser::class);
        }

        if (!\is_object($value) || !($value instanceof MultiUserTimesheet)) {
            return;
        }

        if ($value->getTeams()->isEmpty() && $value->getUsers()->isEmpty()) {
            $this->context->buildViolation('You must select at least one user or team.')
                ->atPath('users')
                ->setTranslationDomain('validators')
                ->setCode(TimesheetMultiUser::MISSING_USER_OR_TEAM)
                ->addViolation();

            $this->context->buildViolation('You must select at least one user or team.')
                ->atPath('teams')
                ->setTranslationDomain('validators')
                ->setCode(TimesheetMultiUser::MISSING_USER_OR_TEAM)
                ->addViolation();
        }
    }
}
