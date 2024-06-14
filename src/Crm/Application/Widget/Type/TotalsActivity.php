<?php

declare(strict_types=1);

namespace App\Crm\Application\Widget\Type;

use App\Crm\Domain\Repository\ActivityRepository;
use App\Crm\Domain\Repository\Query\ActivityQuery;
use App\Widget\WidgetInterface;

final class TotalsActivity extends AbstractWidget
{
    public function __construct(
        private ActivityRepository $activity
    ) {
    }

    public function getTitle(): string
    {
        return 'stats.activityTotal';
    }

    /**
     * @param array<string, string|bool|int|null|array<string, mixed>> $options
     @return array<string, string|bool|int|null|array<string, mixed>>
     */
    public function getOptions(array $options = []): array
    {
        return array_merge([
            'route' => 'admin_activity',
            'icon' => 'activity',
            'color' => WidgetInterface::COLOR_TOTAL,
        ], parent::getOptions($options));
    }

    /**
     * @param array<string, string|bool|int|null|array<string, mixed>> $options
     */
    public function getData(array $options = []): mixed
    {
        $user = $this->getUser();
        $query = new ActivityQuery();
        $query->setCurrentUser($user);

        return $this->activity->countActivitiesForQuery($query);
    }

    /**
     * @return string[]
     */
    public function getPermissions(): array
    {
        return ['view_activity', 'view_teamlead_activity', 'view_team_activity'];
    }

    public function getTemplateName(): string
    {
        return 'widget/widget-more.html.twig';
    }

    public function getId(): string
    {
        return 'TotalsActivity';
    }
}
