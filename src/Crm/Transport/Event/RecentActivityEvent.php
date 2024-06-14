<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use App\Crm\Domain\Entity\Timesheet;
use App\User\Domain\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

final class RecentActivityEvent extends Event
{
    /**
     * @param Timesheet[] $recentActivities
     */
    public function __construct(
        private User $user,
        private array $recentActivities
    ) {
    }

    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return Timesheet[]
     */
    public function getRecentActivities(): array
    {
        return array_values($this->recentActivities);
    }

    public function addRecentActivity(Timesheet $recentActivity): self
    {
        $this->recentActivities[] = $recentActivity;

        return $this;
    }

    public function removeRecentActivity(Timesheet $recentActivity): bool
    {
        $key = array_search($recentActivity, $this->recentActivities, true);
        if ($key === false) {
            return false;
        }

        unset($this->recentActivities[$key]);

        return true;
    }
}
