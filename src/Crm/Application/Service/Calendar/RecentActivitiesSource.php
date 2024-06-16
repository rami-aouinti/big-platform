<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Calendar;

final class RecentActivitiesSource implements DragAndDropSource
{
    /**
     * @param DragAndDropEntry[] $entries
     */
    public function __construct(
        private array $entries
    ) {
    }

    public function getTitle(): string
    {
        return 'recent.activities';
    }

    public function getTranslationDomain(): string
    {
        return 'messages';
    }

    public function getRoute(): string
    {
        return 'post_timesheet';
    }

    public function getMethod(): string
    {
        return 'POST';
    }

    /**
     * @return array<string, string>
     */
    public function getRouteParams(): array
    {
        return [
            'full' => 'true',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function getRouteReplacer(): array
    {
        return [];
    }

    /**
     * @return DragAndDropEntry[]
     */
    public function getEntries(): array
    {
        return $this->entries;
    }

    public function getBlockInclude(): string
    {
        return 'calendar/drag-drop.html.twig';
    }
}
