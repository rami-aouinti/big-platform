<?php

declare(strict_types=1);

namespace App\User;

use App\Configuration\SystemConfiguration;
use App\Crm\Domain\Entity\UserPreference;
use App\Crm\Domain\Repository\UserRepository;
use App\Crm\Transport\Event\UserCreateEvent;
use App\Crm\Transport\Event\UserCreatePostEvent;
use App\Crm\Transport\Event\UserCreatePreEvent;
use App\Crm\Transport\Event\UserDeletePostEvent;
use App\Crm\Transport\Event\UserDeletePreEvent;
use App\Crm\Transport\Event\UserUpdatePostEvent;
use App\Crm\Transport\Event\UserUpdatePreEvent;
use App\Crm\Transport\Validator\ValidationFailedException;
use App\General\Domain\Enum\Language;
use App\User\Domain\Entity\User;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use InvalidArgumentException;
use Psr\EventDispatcher\EventDispatcherInterface;
use Random\RandomException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use function array_key_exists;
use function strlen;

/**
 * @final
 */
class UserService
{
    /**
     * @var array<string, int>
     */
    private array $cache = [];

    public function __construct(
        private readonly UserRepository $repository,
        private readonly EventDispatcherInterface $dispatcher,
        private readonly ValidatorInterface $validator,
        private readonly SystemConfiguration $configuration,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function countUser(?bool $enabled = null): int
    {
        $key = 'count' . ($enabled === null ? '_all' : ($enabled ? '_visible' : '_invisible'));

        if (!array_key_exists($key, $this->cache)) {
            $this->cache[$key] = $this->repository->countUser($enabled);
        }

        return $this->cache[$key];
    }

    public function createNewUser(): User
    {
        $user = new User();
        $user->setEnabled(true);
        $user->setTimezone($this->configuration->getUserDefaultTimezone());
        $user->setLanguage(Language::EN);
        $user->setPreferenceValue(UserPreference::SKIN, $this->configuration->getUserDefaultTheme());

        // Attention: PrepareUserEvent cannot be dispatched on console, as it calls isGranted()
        $this->dispatcher->dispatch(new UserCreateEvent($user));

        return $user;
    }

    public function saveUser(User $user): User
    {
        if ($user->getId() === null) {
            return $this->saveNewUser($user);
        }

        return $this->updateUser($user);
    }

    /**
     * @internal will be made private soon
     */
    public function saveNewUser(User $user): User
    {
        if ($user->getId() !== null) {
            throw new InvalidArgumentException('Cannot create user, already persisted');
        }

        $this->validateUser($user, ['Registration', 'UserCreate']);

        $this->hashPassword($user);
        $this->hashApiToken($user);

        $this->dispatcher->dispatch(new UserCreatePreEvent($user)); // @CloudRequired
        $this->repository->saveUser($user);
        $this->dispatcher->dispatch(new UserCreatePostEvent($user));

        return $user;
    }

    public function updateUser(User $user, array $groups = []): User
    {
        $this->validateUser($user, $groups);

        $this->hashPassword($user);
        $this->hashApiToken($user);

        $this->dispatcher->dispatch(new UserUpdatePreEvent($user));
        $this->repository->saveUser($user);
        $this->dispatcher->dispatch(new UserUpdatePostEvent($user));

        return $user;
    }

    public function findUserByUsernameOrThrowException(string $username): User
    {
        $user = $this->findUserByName($username);

        if ($user === null) {
            throw new InvalidArgumentException(sprintf('User identified by "%s" username does not exist.', $username));
        }

        return $user;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findUserByUsernameOrEmail(string $usernameOrEmail): User
    {
        return $this->repository->loadUserByIdentifier($usernameOrEmail);
    }

    public function findUserByEmail(string $email): ?User
    {
        return $this->repository->findOneBy([
            'email' => $email,
        ]);
    }

    public function findUserByName(string $name): ?User
    {
        return $this->repository->findByUsername($name);
    }

    public function findUserByDisplayName(string $name): ?User
    {
        return $this->repository->findOneBy([
            'alias' => $name,
        ]);
    }

    public function findUserByConfirmationToken(string $token): ?User
    {
        return $this->repository->findOneBy([
            'confirmationToken' => $token,
        ]);
    }

    /**
     * @throws RandomException
     */
    public function generateSecurityToken(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }

    /**
     * @throws ORMException
     */
    public function deleteUser(User $delete, ?User $replace = null): void
    {
        $this->dispatcher->dispatch(new UserDeletePreEvent($delete, $replace));
        $this->repository->deleteUser($delete, $replace);
        $this->dispatcher->dispatch(new UserDeletePostEvent($delete, $replace));
    }

    /**
     * @param string[] $groups
     * @throws ValidationFailedException
     */
    private function validateUser(User $user, array $groups = []): void
    {
        $errors = $this->validator->validate($user, null, $groups);

        if ($errors->count() > 0) {
            throw new ValidationFailedException($errors, 'Validation Failed');
        }
    }

    private function hashPassword(User $user): void
    {
        $plain = $user->getPlainPassword();

        if ($plain === null || strlen($plain) === 0) {
            return;
        }

        $password = $this->passwordHasher->hashPassword($user, $plain);
        $user->setPlainPassword($password);
        $user->eraseCredentials();
    }

    private function hashApiToken(User $user): void
    {
        $plain = $user->getPlainApiToken();

        if ($plain === null || strlen($plain) === 0) {
            return;
        }

        $password = $this->passwordHasher->hashPassword($user, $plain);
        $user->setApiToken($password);
        $user->eraseCredentials();
    }
}
