<?php

declare(strict_types=1);

namespace App\Crm\Application\Model;

use App\Crm\Application\Model\Statistic\BudgetStatistic;
use App\Crm\Domain\Entity\Activity;

class ActivityStatistic extends BudgetStatistic implements \JsonSerializable
{
    /**
     * @var Activity
     */
    private $activity;

    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    public function setActivity(Activity $activity): void
    {
        $this->activity = $activity;
    }

    /**
     * Added for simpler re-use in frontend (charts).
     */
    public function getColor(): ?string
    {
        if ($this->activity === null) {
            return null;
        }

        return $this->activity->getColor();
    }

    /**
     * Added for simpler re-use in frontend (charts).
     */
    public function getName(): ?string
    {
        if ($this->activity === null) {
            return null;
        }

        return $this->activity->getName();
    }

    public function jsonSerialize(): mixed
    {
        return array_merge(parent::jsonSerialize(), [
            'name' => $this->getName(),
            'color' => $this->getColor(),
        ]);
    }
}
