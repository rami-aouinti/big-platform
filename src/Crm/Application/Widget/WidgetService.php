<?php

declare(strict_types=1);

namespace App\Crm\Application\Widget;

use InvalidArgumentException;

use function array_key_exists;

/**
 * @final
 */
class WidgetService
{
    private array $widgets = [];

    public function hasWidget(string $id): bool
    {
        return array_key_exists($id, $this->widgets);
    }

    public function registerWidget(WidgetInterface $widget): void
    {
        $id = trim($widget->getId());
        if ($id === '') {
            throw new InvalidArgumentException('Widget needs a non-empty ID');
        }
        $this->widgets[$id] = $widget;
    }

    /**
     * @return array<string, WidgetInterface>
     */
    public function getAllWidgets(): array
    {
        return $this->widgets;
    }

    public function getWidget(string $id): WidgetInterface
    {
        if (!$this->hasWidget($id)) {
            throw new InvalidArgumentException(sprintf('Cannot find widget: %s', $id));
        }

        return $this->widgets[$id];
    }
}
