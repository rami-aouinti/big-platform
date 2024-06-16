<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\WorkingTime\Model;

use App\Crm\Application\Model\Day as BaseDay;
use App\Crm\Domain\Entity\WorkingTime;

/**
 * @package App\Crm\Application\Service\WorkingTime\Model
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class Day extends BaseDay
{
    private ?WorkingTime $workingTime = null;
    /**
     * @var array<DayAddon>
     */
    private array $addons = [];

    public function isLocked(): bool
    {
        if ($this->workingTime !== null && $this->workingTime->isApproved()) {
            return true;
        }

        return false;
    }

    public function getWorkingTime(): ?WorkingTime
    {
        return $this->workingTime;
    }

    public function setWorkingTime(?WorkingTime $workingTime): void
    {
        $this->workingTime = $workingTime;
    }

    /**
     * @return array<DayAddon>
     */
    public function getAddons(): array
    {
        return $this->addons;
    }

    public function hasAddons(): bool
    {
        return \count($this->addons) > 0;
    }

    /**
     * Descriptions show up in the approval PDF and maybe in other places as well.
     */
    public function addAddon(DayAddon $addon): void
    {
        $this->addons[] = $addon;

        if (!$this->isLocked() && $this->workingTime !== null) {
            $this->workingTime->setActualTime($this->workingTime->getActualTime() + $addon->getDuration());
        }
    }
}
