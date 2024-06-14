<?php

declare(strict_types=1);

namespace App\Crm\Transport\EventSubscriber;

use App\Crm\Transport\Event\PrepareUserEvent;
use App\User\Domain\Entity\User;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class UserProfileSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private TokenStorageInterface $storage
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => ['prepareUserProfile', 200],
        ];
    }

    public function prepareUserProfile(KernelEvent $event): void
    {
        // ignore sub-requests
        if (!$event->isMainRequest()) {
            return;
        }

        // ignore events like the toolbar where we do not have a token
        if (null === ($token = $this->storage->getToken())) {
            return;
        }

        $user = $token->getUser();

        if ($user instanceof User) {
            $event = new PrepareUserEvent($user);
            $this->eventDispatcher->dispatch($event);
        }
    }
}
