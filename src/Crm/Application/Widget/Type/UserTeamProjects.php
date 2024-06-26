<?php

declare(strict_types=1);

namespace App\Crm\Application\Widget\Type;

use App\Crm\Application\Service\Project\ProjectStatisticService;
use App\Crm\Domain\Entity\Project;
use App\Crm\Domain\Entity\Team;
use App\Crm\Domain\Repository\Loader\ProjectLoader;
use App\Crm\Domain\Repository\Loader\TeamLoader;
use App\Widget\WidgetInterface;
use Doctrine\ORM\EntityManagerInterface;

final class UserTeamProjects extends AbstractWidget
{
    public function __construct(
        private ProjectStatisticService $statisticService,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function getWidth(): int
    {
        return WidgetInterface::WIDTH_HALF;
    }

    public function getHeight(): int
    {
        return WidgetInterface::HEIGHT_LARGE;
    }

    public function getTitle(): string
    {
        return 'my_team_projects';
    }

    public function getTemplateName(): string
    {
        return 'widget/widget-userteamprojects.html.twig';
    }

    /**
     * @return string[]
     */
    public function getPermissions(): array
    {
        return [
            'budget_team_project', 'budget_teamlead_project', 'budget_project',
            'time_team_project', 'time_teamlead_project', 'time_project',
        ];
    }

    public function getId(): string
    {
        return 'UserTeamProjects';
    }

    /**
     * @param array<string, string|bool|int|null|array<string, mixed>> $options
     */
    public function getData(array $options = []): mixed
    {
        $user = $this->getUser();
        $now = new \DateTime('now', new \DateTimeZone($user->getTimezone()));

        $loader = new TeamLoader($this->entityManager);
        $loader->loadResults($user->getTeams());

        $teamProjects = [];
        $projects = [];

        /** @var Team $team */
        foreach ($user->getTeams() as $team) {
            /** @var Project $project */
            foreach ($team->getProjects() as $project) {
                if (!isset($teamProjects[$project->getId()])) {
                    $teamProjects[$project->getId()] = $project;
                }
            }
        }

        $loader = new ProjectLoader($this->entityManager, false, false, false);
        $loader->loadResults($teamProjects);

        foreach ($teamProjects as $id => $project) {
            if (!$project->isVisibleAtDate($now) || !$project->hasBudgets()) {
                continue;
            }
            $projects[$project->getId()] = $project;
        }

        return $this->statisticService->getBudgetStatisticModelForProjects($projects, $now);
    }
}
