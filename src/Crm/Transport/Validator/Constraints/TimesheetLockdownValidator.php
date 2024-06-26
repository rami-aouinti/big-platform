<?php

declare(strict_types=1);

namespace App\Crm\Transport\Validator\Constraints;

use App\Crm\Domain\Entity\Timesheet as TimesheetEntity;
use App\Crm\Application\Service\Timesheet\LockdownService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class TimesheetLockdownValidator
 *
 * @package App\Crm\Transport\Validator\Constraints
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class TimesheetLockdownValidator extends ConstraintValidator
{
    public function __construct(
        private readonly Security $security,
        private readonly LockdownService $lockdownService
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!($constraint instanceof TimesheetLockdown)) {
            throw new UnexpectedTypeException($constraint, TimesheetLockdown::class);
        }

        if (!\is_object($value) || !($value instanceof TimesheetEntity)) {
            throw new UnexpectedTypeException($value, TimesheetEntity::class);
        }

        if (!$this->lockdownService->isLockdownActive()) {
            return;
        }

        if (null === ($timesheetStart = $value->getBegin())) {
            return;
        }

        // lockdown never takes effect for users with special permission
        if ($this->security->getUser() !== null && $this->security->isGranted('lockdown_override_timesheet')) {
            return;
        }

        $now = new \DateTime('now', $timesheetStart->getTimezone());

        if (!empty($constraint->now)) {
            if ($constraint->now instanceof \DateTimeInterface) {
                $now = $constraint->now;
            } elseif (\is_string($constraint->now)) {
                try {
                    $now = new \DateTime($constraint->now, $timesheetStart->getTimezone());
                } catch (\Exception $ex) {
                }
            }
        }

        $allowEditInGracePeriod = false;
        if ($this->security->getUser() !== null && $this->security->isGranted('lockdown_grace_timesheet')) {
            $allowEditInGracePeriod = true;
        }

        if ($this->lockdownService->isEditable($value, $now, $allowEditInGracePeriod)) {
            return;
        }

        // raise a violation for all entries before the start of lockdown period
        $this->context->buildViolation('This period is locked, please choose a later date.')
            ->atPath('begin_date')
            ->setTranslationDomain('validators')
            ->setCode(TimesheetLockdown::PERIOD_LOCKED)
            ->addViolation();
    }
}
