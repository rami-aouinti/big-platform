<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Timesheet;

use App\Configuration\SystemConfiguration;
use App\Crm\Application\Service\Timesheet\TrackingMode\TrackingModeInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

final class TrackingModeService
{
    /**
     * @param TrackingModeInterface[] $modes
     */
    public function __construct(
        private readonly SystemConfiguration $configuration,
        #[TaggedIterator(TrackingModeInterface::class)]
        private readonly iterable $modes
    ) {
    }

    /**
     * @return TrackingModeInterface[]
     */
    public function getModes(): iterable
    {
        return $this->modes;
    }

    public function getActiveMode(): TrackingModeInterface
    {
        $trackingMode = $this->configuration->getTimesheetTrackingMode();

        foreach ($this->getModes() as $mode) {
            if ($mode->getId() === $trackingMode) {
                return $mode;
            }
        }

        throw new ServiceNotFoundException($trackingMode);
    }
}
