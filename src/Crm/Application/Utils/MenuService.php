<?php

declare(strict_types=1);

namespace App\Crm\Application\Utils;

use App\Crm\Transport\Event\ConfigureMainMenuEvent;
use App\User\Domain\Entity\User;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\SecurityBundle\Security;

final class MenuService
{
    private ?ConfigureMainMenuEvent $menuEvent = null;

    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private Security $security
    ) {
    }

    public function getKimaiMenu(): ConfigureMainMenuEvent
    {
        if ($this->menuEvent === null) {
            $this->menuEvent = new ConfigureMainMenuEvent();
            /** @var User $user */
            $user = $this->security->getUser();

            // error pages don't have a user and will fail when is_granted() is called
            if ($user !== null) {
                $this->eventDispatcher->dispatch($this->menuEvent);
            }
        }

        return $this->menuEvent;
    }
}
