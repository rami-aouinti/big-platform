<?php

declare(strict_types=1);

namespace App\Crm\Transport\Validator\Constraints;

use App\Crm\Domain\Entity\Team as TeamEntity;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class TeamValidator
 *
 * @package App\Crm\Transport\Validator\Constraints
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class TeamValidator extends ConstraintValidator
{
    /**
     * @param TeamEntity $value
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!($constraint instanceof Team)) {
            throw new UnexpectedTypeException($constraint, Team::class);
        }

        if (!\is_object($value) || !($value instanceof TeamEntity)) {
            return;
        }

        if (!$value->hasTeamleads()) {
            $this->context->buildViolation(Team::getErrorName(Team::MISSING_TEAMLEAD))
                ->atPath('members')
                ->setTranslationDomain('validators')
                ->setCode(Team::MISSING_TEAMLEAD)
                ->addViolation();
        }
    }
}
