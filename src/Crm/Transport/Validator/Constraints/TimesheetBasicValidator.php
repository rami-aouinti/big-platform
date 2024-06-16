<?php

declare(strict_types=1);

namespace App\Crm\Transport\Validator\Constraints;

use App\Calendar\Domain\Entity\Timesheet as TimesheetEntity;
use App\Configuration\SystemConfiguration;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class TimesheetBasicValidator extends ConstraintValidator
{
    public function __construct(
        private readonly SystemConfiguration $systemConfiguration
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!($constraint instanceof TimesheetBasic)) {
            throw new UnexpectedTypeException($constraint, TimesheetBasic::class);
        }

        if (!\is_object($value) || !($value instanceof TimesheetEntity)) {
            throw new UnexpectedTypeException($value, TimesheetEntity::class);
        }

        $this->validateBeginAndEnd($value, $this->context);
        $this->validateActivityAndProject($value, $this->context);
    }

    protected function validateBeginAndEnd(TimesheetEntity $timesheet, ExecutionContextInterface $context): void
    {
        $begin = $timesheet->getBegin();
        $end = $timesheet->getEnd();

        if ($begin === null) {
            $context->buildViolation(TimesheetBasic::getErrorName(TimesheetBasic::MISSING_BEGIN_ERROR))
                ->atPath('begin_date')
                ->setTranslationDomain('validators')
                ->setCode(TimesheetBasic::MISSING_BEGIN_ERROR)
                ->addViolation();

            return;
        }

        if ($end !== null && $begin > $end) {
            $context->buildViolation(TimesheetBasic::getErrorName(TimesheetBasic::END_BEFORE_BEGIN_ERROR))
                ->atPath('end_date')
                ->setTranslationDomain('validators')
                ->setCode(TimesheetBasic::END_BEFORE_BEGIN_ERROR)
                ->addViolation();
        }
    }

    protected function validateActivityAndProject(TimesheetEntity $timesheet, ExecutionContextInterface $context): void
    {
        $activity = $timesheet->getActivity();

        if ($this->systemConfiguration->isTimesheetRequiresActivity() && $activity === null) {
            $context->buildViolation(TimesheetBasic::getErrorName(TimesheetBasic::MISSING_ACTIVITY_ERROR))
                ->atPath('activity')
                ->setTranslationDomain('validators')
                ->setCode(TimesheetBasic::MISSING_ACTIVITY_ERROR)
                ->addViolation();
        }

        if (null === ($project = $timesheet->getProject())) {
            $context->buildViolation(TimesheetBasic::getErrorName(TimesheetBasic::MISSING_PROJECT_ERROR))
                ->atPath('project')
                ->setTranslationDomain('validators')
                ->setCode(TimesheetBasic::MISSING_PROJECT_ERROR)
                ->addViolation();
        }

        $hasActivity = $activity !== null;

        if ($project === null) {
            return;
        }

        if ($hasActivity && $activity->getProject() !== null && $activity->getProject() !== $project) {
            $context->buildViolation(TimesheetBasic::getErrorName(TimesheetBasic::ACTIVITY_PROJECT_MISMATCH_ERROR))
                ->atPath('project')
                ->setTranslationDomain('validators')
                ->setCode(TimesheetBasic::ACTIVITY_PROJECT_MISMATCH_ERROR)
                ->addViolation();
        }

        if ($hasActivity && !$project->isGlobalActivities() && $activity->isGlobal()) {
            $context->buildViolation(TimesheetBasic::getErrorName(TimesheetBasic::PROJECT_DISALLOWS_GLOBAL_ACTIVITY))
                ->atPath('activity')
                ->setTranslationDomain('validators')
                ->setCode(TimesheetBasic::PROJECT_DISALLOWS_GLOBAL_ACTIVITY)
                ->addViolation();
        }

        $pathStart = 'begin_date';
        $pathEnd = 'end_date';

        $projectBegin = $project->getStart();
        $projectEnd = $project->getEnd();

        if ($projectBegin === null && $projectEnd === null) {
            return;
        }

        $timesheetStart = $timesheet->getBegin();
        $timesheetEnd = $timesheet->getEnd();

        if ($timesheetStart !== null) {
            if ($projectBegin !== null && $timesheetStart->getTimestamp() < $projectBegin->getTimestamp()) {
                $context->buildViolation(TimesheetBasic::getErrorName(TimesheetBasic::PROJECT_NOT_STARTED))
                    ->atPath($pathStart)
                    ->setTranslationDomain('validators')
                    ->setCode(TimesheetBasic::PROJECT_NOT_STARTED)
                    ->addViolation();
            } elseif ($projectEnd !== null && $timesheetStart->getTimestamp() > $projectEnd->getTimestamp()) {
                $context->buildViolation(TimesheetBasic::getErrorName(TimesheetBasic::PROJECT_ALREADY_ENDED))
                    ->atPath($pathStart)
                    ->setTranslationDomain('validators')
                    ->setCode(TimesheetBasic::PROJECT_ALREADY_ENDED)
                    ->addViolation();
            }
        }

        if ($timesheetEnd !== null) {
            if ($projectEnd !== null && $timesheetEnd > $projectEnd) {
                $context->buildViolation(TimesheetBasic::getErrorName(TimesheetBasic::PROJECT_ALREADY_ENDED))
                    ->atPath($pathEnd)
                    ->setTranslationDomain('validators')
                    ->setCode(TimesheetBasic::PROJECT_ALREADY_ENDED)
                    ->addViolation();
            } elseif ($projectBegin !== null && $timesheetEnd < $projectBegin) {
                $context->buildViolation(TimesheetBasic::getErrorName(TimesheetBasic::PROJECT_NOT_STARTED))
                    ->atPath($pathEnd)
                    ->setTranslationDomain('validators')
                    ->setCode(TimesheetBasic::PROJECT_NOT_STARTED)
                    ->addViolation();
            }
        }
    }
}
