<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Project;

use App\Crm\Domain\Entity\Project;
use App\Crm\Domain\Repository\ActivityRateRepository;
use App\Crm\Domain\Repository\ActivityRepository;
use App\Crm\Domain\Repository\ProjectRateRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

/**
 * @package App\Crm\Application\Service\Project
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final readonly class ProjectDuplicationService
{
    public function __construct(
        private ProjectService $projectService,
        private ActivityRepository $activityRepository,
        private ProjectRateRepository $projectRateRepository,
        private ActivityRateRepository $activityRateRepository
    ) {
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function duplicate(Project $project, string $newName): Project
    {
        $newProject = clone $project;
        $newProject->setName($newName);

        foreach ($project->getTeams() as $team) {
            $newProject->addTeam($team);
        }

        foreach ($project->getMetaFields() as $metaField) {
            $newMetaField = clone $metaField;
            $newMetaField->setEntity($newProject);
            $newProject->setMetaField($newMetaField);
        }

        if ($project->getEnd() !== null) {
            $newProject->setStart(clone $project->getEnd());
            $newProject->setEnd(null);
        }

        $this->projectService->saveNewProject($newProject);

        foreach ($this->projectRateRepository->getRatesForProject($project) as $rate) {
            $newRate = clone $rate;
            $newRate->setProject($newProject);
            $this->projectRateRepository->saveRate($newRate);
        }

        $allActivities = $this->activityRepository->findByProject($project);
        foreach ($allActivities as $activity) {
            $newActivity = clone $activity;
            $newActivity->setProject($newProject);
            foreach ($activity->getMetaFields() as $metaField) {
                $newMetaField = clone $metaField;
                $newMetaField->setEntity($newActivity);
                $newActivity->setMetaField($newMetaField);
            }

            $this->activityRepository->saveActivity($newActivity);

            foreach ($this->activityRateRepository->getRatesForActivity($activity) as $rate) {
                $newRate = clone $rate;
                $newRate->setActivity($newActivity);
                $this->activityRateRepository->saveRate($newRate);
            }
        }

        return $newProject;
    }
}
