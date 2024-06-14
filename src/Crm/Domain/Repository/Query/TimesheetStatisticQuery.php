<?php

declare(strict_types=1);

namespace App\Crm\Domain\Repository\Query;

use App\Crm\Application\Service\Invoice\Project;
use App\User\Domain\Entity\User;

final class TimesheetStatisticQuery
{
    private ?Project $project = null;

    /**
     * @param array<User> $users
     */
    public function __construct(
        private readonly \DateTimeInterface $begin,
        private readonly \DateTimeInterface $end,
        private array $users
    ) {
    }

    public function getBegin(): \DateTimeInterface
    {
        return $this->begin;
    }

    public function getEnd(): \DateTimeInterface
    {
        return $this->end;
    }

    /**
     * @return User[]
     */
    public function getUsers(): array
    {
        return $this->users;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): void
    {
        $this->project = $project;
    }
}
