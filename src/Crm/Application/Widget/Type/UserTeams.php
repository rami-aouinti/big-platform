<?php

declare(strict_types=1);

namespace App\Crm\Application\Widget\Type;

use App\Crm\Domain\Repository\Loader\UserLoader;
use App\Widget\WidgetInterface;
use Doctrine\ORM\EntityManagerInterface;

final class UserTeams extends AbstractWidget
{
    public function __construct(
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
        return 'my_teams';
    }

    public function getTemplateName(): string
    {
        return 'widget/widget-userteams.html.twig';
    }

    /**
     * @return string[]
     */
    public function getPermissions(): array
    {
        return ['view_team_member', 'view_team'];
    }

    public function getId(): string
    {
        return 'UserTeams';
    }

    /**
     * @param array<string, string|bool|int|null|array<string, mixed>> $options
     */
    public function getData(array $options = []): mixed
    {
        $user = $this->getUser();

        // without this, every user would be lazy loaded
        $loader = new UserLoader($this->entityManager, true);
        $loader->loadResults([$user->getId()]);

        return $user->getTeams();
    }
}
