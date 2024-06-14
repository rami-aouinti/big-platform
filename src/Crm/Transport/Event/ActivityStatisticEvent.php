<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use App\Crm\Application\Model\ActivityStatistic;
use App\Crm\Domain\Entity\Activity;
use DateTime;
use DateTimeInterface;

final class ActivityStatisticEvent extends AbstractActivityEvent
{
    private readonly ?DateTime $begin;
    private readonly ?DateTime $end;

    public function __construct(
        Activity $activity,
        private readonly ActivityStatistic $statistic,
        ?DateTimeInterface $begin = null,
        ?DateTimeInterface $end = null
    ) {
        parent::__construct($activity);

        if ($begin !== null) {
            $begin = DateTime::createFromInterface($begin);
        }
        $this->begin = $begin;

        if ($end !== null) {
            $end = DateTime::createFromInterface($end);
        }
        $this->end = $end;
    }

    public function getStatistic(): ActivityStatistic
    {
        return $this->statistic;
    }

    public function getBegin(): ?DateTime
    {
        return $this->begin;
    }

    public function getEnd(): ?DateTime
    {
        return $this->end;
    }
}
