<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use App\User\Domain\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

final class DashboardEvent extends Event
{
    /**
     * @var array<string>
     */
    private array $widgets = [];

    public function __construct(
        private User $user
    ) {
    }

    /**
     * @return array<string>
     */
    public function getWidgets(): array
    {
        ksort($this->widgets, SORT_NUMERIC);

        return $this->widgets;
    }

    /**
     * Adding a widget here will add it to the default dashboard settings for users,
     * which do not yet have their own dashboard configured.
     */
    public function addWidget(string $widget, ?int $position = null): void
    {
        if ($position === null) {
            $position = 0;
            $keys = array_keys($this->widgets);
            if (\count($keys) > 0) {
                $position = max($keys) + 10;
            }
        }

        while (\array_key_exists($position, $this->widgets)) {
            $position++;
        }

        $this->widgets[$position] = $widget;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
