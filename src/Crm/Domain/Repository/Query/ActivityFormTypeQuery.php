<?php

declare(strict_types=1);

namespace App\Crm\Domain\Repository\Query;

use App\Crm\Domain\Entity\Activity;
use App\Crm\Domain\Entity\Project;

final class ActivityFormTypeQuery extends BaseFormTypeQuery
{
    private ?Activity $activityToIgnore = null;

    /**
     * @param Activity|array<Activity>|int|null $activity
     * @param Project|array<Project>|int|null $project
     */
    public function __construct(Activity|array|int|null $activity = null, Project|array|int|null $project = null)
    {
        if ($activity !== null) {
            if (!\is_array($activity)) {
                $activity = [$activity];
            }
            $this->setActivities($activity);
        }

        if ($project !== null) {
            if (!\is_array($project)) {
                $project = [$project];
            }
            $this->setProjects($project);
        }
    }

    public function getActivityToIgnore(): ?Activity
    {
        return $this->activityToIgnore;
    }

    public function setActivityToIgnore(Activity $activityToIgnore): self
    {
        $this->activityToIgnore = $activityToIgnore;

        return $this;
    }

    public function isGlobalsOnly(): bool
    {
        if ($this->hasProjects()) {
            return false;
        }

        if (!$this->hasActivities()) {
            return true;
        }

        foreach ($this->getActivities() as $activity) {
            // this is a potential problem, if only IDs were set
            if ($activity instanceof Activity && !$activity->isGlobal()) {
                return false;
            }
        }

        return true;
    }
}
