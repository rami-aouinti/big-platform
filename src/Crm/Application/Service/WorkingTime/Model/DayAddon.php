<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\WorkingTime\Model;

/**
 * @package App\Crm\Application\Service\WorkingTime\Model
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class DayAddon
{
    private bool $billable = true;

    public function __construct(
        private readonly string $title,
        private readonly int $duration,
        private readonly int $visibleDuration
    ) {
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function isBillable(): bool
    {
        return $this->billable;
    }

    public function setBillable(bool $billable): void
    {
        $this->billable = $billable;
    }

    public function getVisibleDuration(): int
    {
        return $this->visibleDuration;
    }
}
