<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use App\Crm\Transport\Form\Model\SystemConfiguration;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * This event should be used, if system configurations should be changed/added dynamically.
 */
final class SystemConfigurationEvent extends Event
{
    /**
     * @param SystemConfiguration[] $configurations
     */
    public function __construct(
        private array $configurations
    ) {
    }

    /**
     * @return SystemConfiguration[]
     */
    public function getConfigurations(): array
    {
        return $this->configurations;
    }

    public function addConfiguration(SystemConfiguration $configuration): self
    {
        $this->configurations[] = $configuration;

        return $this;
    }
}
