<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\WorkingTime\Model;

use App\Crm\Application\Model\Year as BaseYear;
use App\User\Domain\Entity\User;

/**
 * @method array<Month> getMonths()
 * @method Month getMonth(\DateTimeInterface $month)
 */
final class Year extends BaseYear
{
    public function __construct(
        \DateTimeInterface $year,
        private User $user
    ) {
        parent::__construct($year);
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getExpectedTime(\DateTimeInterface $until): int
    {
        $time = 0;

        foreach ($this->getMonths() as $month) {
            $time += $month->getExpectedTime($until);
        }

        return $time;
    }

    public function getActualTime(): int
    {
        $time = 0;

        foreach ($this->getMonths() as $month) {
            $time += $month->getActualTime();
        }

        return $time;
    }

    protected function createMonth(\DateTimeImmutable $month): Month
    {
        return new Month($month, $this->user);
    }
}
