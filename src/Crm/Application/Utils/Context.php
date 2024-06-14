<?php

declare(strict_types=1);

namespace App\Crm\Application\Utils;

use App\User\Domain\Entity\User;

final class Context
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
