<?php

declare(strict_types=1);

namespace App\Crm\Transport\Validator\Constraints;

use App\Admin\Auth\Security\RoleService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @package App\Crm\Transport\Validator\Constraints
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class RoleNameValidator extends ConstraintValidator
{
    public function __construct(
        private RoleService $service
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof RoleName) {
            throw new UnexpectedTypeException($constraint, RoleName::class);
        }

        // user entity uses uppercase for the roles
        $roles = $this->service->getAvailableNames();

        if (!\is_string($value) || \in_array($value, $roles, true) || preg_match('/^[A-Z_]{5,}$/', $value) !== 1 || str_contains($value, '__') || str_starts_with($value, '_') || str_ends_with($value, '_')) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setCode(RoleName::ROLE_NAME_ERROR)
                ->addViolation();
        }
    }
}
