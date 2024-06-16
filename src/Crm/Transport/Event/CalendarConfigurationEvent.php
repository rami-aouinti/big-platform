<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class CalendarConfigurationEvent extends Event
{
    /**
     * @param array<string, string|int|bool|array> $configuration
     */
    public function __construct(
        private array $configuration
    ) {
    }

    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    public function setConfiguration(array $configuration): void
    {
        foreach ($configuration as $key => $value) {
            if (\array_key_exists($key, $this->configuration)) {
                $this->configuration[$key] = $value;
            }
        }
    }
}
