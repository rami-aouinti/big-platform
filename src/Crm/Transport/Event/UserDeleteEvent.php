<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use App\User\Domain\Entity\User;

class UserDeleteEvent extends AbstractUserEvent
{
    public function __construct(
        User $user,
        private ?User $replacement = null
    ) {
        parent::__construct($user);
    }

    public function getReplacementUser(): ?User
    {
        return $this->replacement;
    }
}
