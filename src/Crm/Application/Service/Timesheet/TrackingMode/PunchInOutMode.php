<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Timesheet\TrackingMode;

use App\Crm\Domain\Entity\Timesheet;
use DateTime;
use Symfony\Component\HttpFoundation\Request;

final class PunchInOutMode implements TrackingModeInterface
{
    use TrackingModeTrait;

    public function canEditBegin(): bool
    {
        return false;
    }

    public function canEditEnd(): bool
    {
        return false;
    }

    public function canEditDuration(): bool
    {
        return false;
    }

    public function canUpdateTimesWithAPI(): bool
    {
        return false;
    }

    public function create(Timesheet $timesheet, ?Request $request = null): void
    {
        if ($timesheet->getBegin() === null) {
            $timesheet->setBegin(new DateTime('now', $this->getTimezone($timesheet)));
        }
    }

    public function getId(): string
    {
        return 'punch';
    }

    public function canSeeBeginAndEndTimes(): bool
    {
        return true;
    }

    public function getEditTemplate(): string
    {
        return 'timesheet/edit-default.html.twig';
    }
}
