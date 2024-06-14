<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use App\User\Domain\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * To be used when a user profile is loaded and should be filled with dynamic user preferences.
 *
 * @internal
 */
final class PrepareUserEvent extends Event
{
    public function __construct(
        private User $user,
        private bool $booting = true
    ) {
    }

    /**
     * Whether this event is dispatched for the currently logged in user during kernel boot.
     */
    public function isBooting(): bool
    {
        return $this->booting;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
