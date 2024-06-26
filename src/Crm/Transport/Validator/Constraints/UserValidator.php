<?php

declare(strict_types=1);

namespace App\Crm\Transport\Validator\Constraints;

use App\User\Domain\Entity\User as UserEntity;
use App\User\UserService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class UserValidator extends ConstraintValidator
{
    public function __construct(
        private UserService $userService
    ) {
    }

    /**
     * @param UserEntity $value
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!($constraint instanceof User)) {
            throw new UnexpectedTypeException($constraint, User::class);
        }

        if (!\is_object($value) || !($value instanceof UserEntity)) {
            return;
        }

        $this->validateUser($value, $this->context);
    }

    protected function validateUser(UserEntity $user, ExecutionContextInterface $context): void
    {
        if ($user->hasEmail()) {
            $this->validateEmailExists($user->getId(), $user->getEmail(), 'email', User::USER_EXISTING_EMAIL, $context);
            $this->validateUsernameExists($user->getId(), $user->getEmail(), 'email', User::USER_EXISTING_EMAIL_AS_NAME, $context);
        }

        if ($user->hasUsername()) {
            $this->validateEmailExists($user->getId(), $user->getUserIdentifier(), 'username', User::USER_EXISTING_NAME_AS_EMAIL, $context);
            $this->validateUsernameExists($user->getId(), $user->getUserIdentifier(), 'username', User::USER_EXISTING_NAME, $context);
        }
    }

    private function validateEmailExists(?string $userId, string $email, string $path, string $code, ExecutionContextInterface $context): void
    {
        $existingByEmail = $this->userService->findUserByEmail($email);

        if ($existingByEmail !== null && $userId !== $existingByEmail->getId()) {
            $context->buildViolation(User::getErrorName($code))
                ->atPath($path)
                ->setTranslationDomain('validators')
                ->setCode($code)
                ->addViolation();
        }
    }

    private function validateUsernameExists(?string $userId, string $username, string $path, string $code, ExecutionContextInterface $context): void
    {
        $existingByName = $this->userService->findUserByName($username);

        if ($existingByName !== null && $userId !== $existingByName->getId()) {
            $context->buildViolation(User::getErrorName($code))
                ->atPath($path)
                ->setTranslationDomain('validators')
                ->setCode($code)
                ->addViolation();
        }
    }
}
