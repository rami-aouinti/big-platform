<?php

declare(strict_types=1);

namespace App\Crm\Transport\Validator\Constraints;

use App\Crm\Domain\Entity\Timesheet as TimesheetEntity;
use App\Crm\Application\Service\Timesheet\TrackingModeService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class TimesheetRestartValidator
 *
 * @package App\Crm\Transport\Validator\Constraints
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class TimesheetRestartValidator extends ConstraintValidator
{
    public function __construct(
        private readonly Security $security,
        private readonly TrackingModeService $trackingModeService
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!($constraint instanceof TimesheetRestart)) {
            throw new UnexpectedTypeException($constraint, TimesheetRestart::class);
        }

        if (!\is_object($value) || !($value instanceof TimesheetEntity)) {
            throw new UnexpectedTypeException($value, TimesheetEntity::class);
        }

        // special case that would otherwise need to be validated in several controllers:
        // an entry is edited and the end date is removed (or duration deleted) would restart the record,
        // which might be disallowed for the current user
        if ($value->getEnd() !== null) {
            return;
        }

        if ($this->context->getViolations()->count() > 0) {
            return;
        }

        if ($this->security->getUser() !== null && $this->security->isGranted('start', $value)) {
            return;
        }

        $mode = $this->trackingModeService->getActiveMode();
        $path = 'start_date';

        if ($mode->canEditEnd()) {
            $path = 'end_date';
        } elseif ($mode->canEditDuration()) {
            $path = 'duration';
        }

        $this->context->buildViolation('You are not allowed to start this timesheet record.')
            ->atPath($path)
            ->setTranslationDomain('validators')
            ->setCode(TimesheetRestart::START_DISALLOWED)
            ->addViolation();
    }
}
