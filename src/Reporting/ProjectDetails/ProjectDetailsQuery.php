<?php

declare(strict_types=1);

namespace App\Reporting\ProjectDetails;

use App\Entity\Project;
use App\User\Domain\Entity\User;
use DateTime;

final class ProjectDetailsQuery
{
    private ?Project $project = null;

    public function __construct(
        private DateTime $today,
        private User $user
    ) {
    }

    public function getToday(): DateTime
    {
        return $this->today;
    }

    public function getUser(): User
    {
        return $this->user;
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
