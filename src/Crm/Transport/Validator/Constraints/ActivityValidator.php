<?php

declare(strict_types=1);

namespace App\Crm\Transport\Validator\Constraints;

use App\Crm\Domain\Entity\Activity as ActivityEntity;
use App\Configuration\SystemConfiguration;
use App\Crm\Domain\Repository\ActivityRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class ActivityValidator
 *
 * @package App\Crm\Transport\Validator\Constraints
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class ActivityValidator extends ConstraintValidator
{
    public function __construct(
        private readonly SystemConfiguration $systemConfiguration,
        private readonly ActivityRepository $activityRepository
    ) {
    }

    /**
     * @param ActivityEntity|mixed $value
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!($constraint instanceof Activity)) {
            throw new UnexpectedTypeException($constraint, Activity::class);
        }

        if (!($value instanceof ActivityEntity)) {
            throw new UnexpectedTypeException($value, ActivityEntity::class);
        }

        if ((bool)$this->systemConfiguration->find('activity.allow_duplicate_number') === false && (($number = $value->getNumber()) !== null)) {
            foreach (
                $this->activityRepository->findBy([
                    'number' => $number,
                ]) as $tmp
            ) {
                if ($tmp->getId() !== $value->getId()) {
                    $this->context->buildViolation(Activity::getErrorName(Activity::ACTIVITY_NUMBER_EXISTING))
                        ->setParameter('%number%', $number)
                        ->atPath('number')
                        ->setTranslationDomain('validators')
                        ->setCode(Activity::ACTIVITY_NUMBER_EXISTING)
                        ->addViolation();
                    break;
                }
            }
        }
    }
}
