<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Timesheet\TrackingMode;

use App\Configuration\SystemConfiguration;
use App\Crm\Domain\Entity\Timesheet;
use DateTime;
use Symfony\Component\HttpFoundation\Request;

final class DurationFixedBeginMode implements TrackingModeInterface
{
    use TrackingModeTrait;

    public function __construct(
        private SystemConfiguration $configuration
    ) {
    }

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
        return true;
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

        /** @var DateTime $newBegin */
        $newBegin = clone $timesheet->getBegin(); // @phpstan-ignore-line

        // this prevents the problem that "now" is being ignored in modify()
        $beginTime = new DateTime($this->configuration->getTimesheetDefaultBeginTime(), $newBegin->getTimezone());
        $newBegin->setTime((int)$beginTime->format('H'), (int)$beginTime->format('i'), 0, 0);

        $timesheet->setBegin($newBegin);
    }

    public function getId(): string
    {
        return 'duration_fixed_begin';
    }

    public function canSeeBeginAndEndTimes(): bool
    {
        return false;
    }

    public function getEditTemplate(): string
    {
        return 'timesheet/edit-default.html.twig';
    }
}
