<?php

declare(strict_types=1);

namespace App\Crm\Application\Model;

use App\Crm\Application\Model\Statistic\Month;
use App\User\Domain\Entity\User;

class UserStatistic extends TimesheetCountedStatistic
{
    public function __construct(
        private User $user
    ) {
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function addValuesFromMonth(Month $month): void
    {
        $this->setDuration($this->getDuration() + $month->getDuration());
        $this->setDurationBillable($this->getDurationBillable() + $month->getBillableDuration());
        $this->setRate($this->getRate() + $month->getRate());
        $this->setRateBillable($this->getRateBillable() + $month->getBillableRate());
        $this->setInternalRate($this->getInternalRate() + $month->getInternalRate());
    }
}
