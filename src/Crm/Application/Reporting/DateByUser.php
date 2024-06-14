<?php

declare(strict_types=1);

namespace App\Crm\Application\Reporting;

use App\User\Domain\Entity\User;

abstract class DateByUser extends AbstractUserList
{
    private ?User $user = null;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }
}
