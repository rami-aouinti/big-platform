<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use App\Crm\Application\Service\WorkingTime\Model\Year;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Triggered for work-contract status pages, to add additional content boxes.
 *
 * @see https://symfony.com/doc/current/templates.html#embedding-controllers
 */
final class WorkContractDetailControllerEvent extends Event
{
    /**
     * @var array<string>
     */
    private array $controller = [];

    public function __construct(
        private Year $year
    ) {
    }

    public function getYear(): Year
    {
        return $this->year;
    }

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
