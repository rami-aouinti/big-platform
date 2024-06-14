<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\WorkingTime\Model;

use App\Crm\Application\Model\Month as BaseMonth;
use App\User\Domain\Entity\User;

/**
 * @method array<Day> getDays()
 * @method Day getDay(\DateTimeInterface $date)
 */
final class Month extends BaseMonth
{
    public function __construct(
        \DateTimeImmutable $month,
        private User $user
    ) {
        parent::__construct($month);
    }

    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * A month is only locked IF every day is approved.
     * If there is even one day left open, the entire month is not locked.
     */
    public function isLocked(): bool
    {
        foreach ($this->getDays() as $day) {
            if (!$day->isLocked()) {
                return false;
            }
        }

        return true;
    }

    public function getLockDate(): ?\DateTimeInterface
    {
        foreach ($this->getDays() as $day) {
            if ($day->getWorkingTime() !== null && $day->getWorkingTime()->isApproved()) {
                return $day->getWorkingTime()->getApprovedAt();
            }
        }

        return null;
    }

    public function getLockedBy(): ?User
    {
        foreach ($this->getDays() as $day) {
            if ($day->getWorkingTime() !== null && $day->getWorkingTime()->isApproved()) {
                return $day->getWorkingTime()->getApprovedBy();
            }
        }

        return null;
    }

    public function getExpectedTime(?\DateTimeInterface $until = null): int
    {
        $time = 0;

        foreach ($this->getDays() as $day) {
            if ($until !== null && $until < $day->getDay()) {
                break;
            }
            if ($day->getWorkingTime() !== null) {
                $time += $day->getWorkingTime()->getExpectedTime();
            }
        }

        return $time;
    }

    public function getActualTime(): int
    {
        $time = 0;

        foreach ($this->getDays() as $day) {
            if ($day->getWorkingTime() !== null) {
                $time += $day->getWorkingTime()->getActualTime();
            }
        }

        return $time;
    }

    protected function createDay(\DateTimeImmutable $day): Day
    {
        return new Day($day);
    }
}
