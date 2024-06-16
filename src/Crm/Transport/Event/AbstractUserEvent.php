<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use App\User\Domain\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Base event class to used with user manipulations.
 */
abstract class AbstractUserEvent extends Event
{
    public function __construct(
        private User $user
    ) {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
