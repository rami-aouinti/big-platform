<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use App\Crm\Application\Model\ProjectStatistic;
use App\Crm\Domain\Entity\Project;

final class ProjectStatisticEvent extends AbstractProjectEvent
{
    public function __construct(
        Project $project,
        private readonly ProjectStatistic $statistic,
        private readonly ?\DateTimeInterface $begin = null,
        private readonly ?\DateTimeInterface $end = null
    ) {
        parent::__construct($project);
    }

    public function getStatistic(): ProjectStatistic
    {
        return $this->statistic;
    }

    public function getBegin(): ?\DateTimeInterface
    {
        return $this->begin;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }
}
