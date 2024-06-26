<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

/**
 * Triggered for activity detail pages, to add additional content boxes.
 *
 * @see https://symfony.com/doc/5.4/templates.html#embedding-controllers
 */
final class ActivityDetailControllerEvent extends AbstractActivityEvent
{
    /**
     * @var array<string>
     */
    private array $controller = [];

    public function addController(string $controller): void
    {
        $this->controller[] = $controller;
    }

    /**
     * @return string[]
     */
    public function getController(): array
    {
        return $this->controller;
    }
}
