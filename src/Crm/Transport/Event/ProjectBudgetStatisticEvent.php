<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use App\Crm\Application\Model\ProjectBudgetStatisticModel;
use DateTime;
use DateTimeInterface;

final class ProjectBudgetStatisticEvent
{
    private readonly ?DateTime $begin;
    private readonly ?DateTime $end;

    /**
     * @param ProjectBudgetStatisticModel[] $models
     */
    public function __construct(
        private readonly array $models,
        ?DateTimeInterface $begin = null,
        ?DateTimeInterface $end = null
    ) {
        if ($begin !== null) {
            $begin = \DateTime::createFromInterface($begin);
        }
        $this->begin = $begin;

        if ($end !== null) {
            $end = \DateTime::createFromInterface($end);
        }
        $this->end = $end;
    }

    public function getModel(int $projectId): ?ProjectBudgetStatisticModel
    {
        if (isset($this->models[$projectId])) {
            return $this->models[$projectId];
        }

        foreach ($this->models as $model) {
            if ($model->getProject()->getId() === $projectId) {
                return $model;
            }
        }

        return null;
    }

    /**
     * @return ProjectBudgetStatisticModel[]
     */
    public function getModels(): array
    {
        return $this->models;
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
