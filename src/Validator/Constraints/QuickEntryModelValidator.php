<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Crm\Application\Model\QuickEntryModel;
use App\Validator\Constraints\QuickEntryModel as QuickEntryModelConstraint;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class QuickEntryModelValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof QuickEntryModelConstraint) {
            throw new UnexpectedTypeException($constraint, QuickEntryModelConstraint::class);
        }

        if (!\is_object($value) || !($value instanceof QuickEntryModel)) {
            throw new UnexpectedTypeException($value, QuickEntryModel::class);
        }

        $model = $value;

        if ($model->isPrototype()) {
            return;
        }

        if ($model->hasExistingTimesheet() || $model->hasNewTimesheet()) {
            if ($model->getActivity() === null) {
                $this->context->buildViolation($constraint->messageActivityRequired)
                    ->atPath('activity')
                    ->setCode(QuickEntryModelConstraint::ACTIVITY_REQUIRED)
                    ->addViolation();
            }

            if ($model->getProject() === null) {
                $this->context->buildViolation($constraint->messageProjectRequired)
                    ->atPath('project')
                    ->setCode(QuickEntryModelConstraint::PROJECT_REQUIRED)
                    ->addViolation();
            }
        }
    }
}
