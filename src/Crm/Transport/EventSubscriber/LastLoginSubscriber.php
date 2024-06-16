<?php

declare(strict_types=1);

namespace App\Crm\Transport\EventSubscriber;

use App\Crm\Domain\Repository\UserRepository;
use App\Crm\Transport\Event\UserInteractiveLoginEvent;
use App\User\Domain\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

final class LastLoginSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private UserRepository $repository
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // triggered for programmatic logins like "password reset" or "registration"
            UserInteractiveLoginEvent::class => 'onImplicitLogin',
            // We do not use the InteractiveLoginEvent because it is not triggered e.g. for SAML
            LoginSuccessEvent::class => 'onFormLogin',
        ];
    }

    public function onImplicitLogin(UserInteractiveLoginEvent $event): void
    {
        $user = $event->getUser();

        $user->setLastLogin(new \DateTime('now', new \DateTimeZone($user->getTimezone())));
        $this->repository->saveUser($user);
    }

    public function onFormLogin(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();

        if ($user instanceof User) {
            $user->setLastLogin(new \DateTime('now', new \DateTimeZone($user->getTimezone())));
            $this->repository->saveUser($user);
        }
    }
}
