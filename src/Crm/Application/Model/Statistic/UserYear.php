<?php

declare(strict_types=1);

namespace App\Crm\Application\Model\Statistic;

use App\User\Domain\Entity\User;

final class UserYear
{
    public function __construct(
        private User $user,
        private Year $year
    ) {
    }

    public function getYear(): Year
    {
        return $this->year;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getDuration(): int
    {
        return $this->year->getDuration();
    }

    public function getBillableDuration(): int
    {
        return $this->year->getBillableDuration();
    }

    public function getRate(): float
    {
        return $this->year->getRate();
    }

    public function getBillableRate(): float
    {
        return $this->year->getBillableRate();
    }

    public function getInternalRate(): float
    {
        return $this->year->getInternalRate();
    }
}
