<?php

declare(strict_types=1);

namespace App\Crm\Transport\Validator\Constraints;

use App\Admin\Auth\Security\RoleService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class RoleValidator extends ConstraintValidator
{
    public function __construct(
        private RoleService $service
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof Role) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\Role');
        }

        $roles = $value;

        if (!\is_array($roles)) {
            $roles = [$roles];
        }

        // user entity uses uppercase for the roles
        $allowedRoles = $this->service->getAvailableNames();

        foreach ($roles as $role) {
            if (!\is_string($role) || !\in_array($role, $allowedRoles, true)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ value }}', $this->formatValue($role))
                    ->setCode(Role::ROLE_ERROR)
                    ->addViolation();
            }
        }
    }
}
