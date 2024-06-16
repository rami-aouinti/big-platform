<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Timesheet\TrackingMode;

use App\Crm\Application\Service\Timesheet\RoundingService;
use App\Crm\Domain\Entity\Timesheet;
use DateTime;
use Symfony\Component\HttpFoundation\Request;

final class DefaultMode extends AbstractTrackingMode
{
    public function __construct(
        private RoundingService $rounding
    ) {
    }

    public function canEditBegin(): bool
    {
        return true;
    }

    public function canEditEnd(): bool
    {
        return true;
    }

    public function canEditDuration(): bool
    {
        return true;
    }

    public function canUpdateTimesWithAPI(): bool
    {
        return true;
    }

    public function getId(): string
    {
        return 'default';
    }

    public function canSeeBeginAndEndTimes(): bool
    {
        return true;
    }

    public function getEditTemplate(): string
    {
        return 'timesheet/edit-default.html.twig';
    }

    public function create(Timesheet $timesheet, ?Request $request = null): void
    {
        parent::create($timesheet, $request);

        if ($timesheet->getBegin() === null) {
            $timesheet->setBegin(new DateTime('now', $this->getTimezone($timesheet)));
        }

        $this->rounding->roundBegin($timesheet);

        if (!$timesheet->isRunning()) {
            $this->rounding->roundEnd($timesheet);

            if ($timesheet->getDuration() !== null) {
                $this->rounding->roundDuration($timesheet);
            }
        }
    }
}
