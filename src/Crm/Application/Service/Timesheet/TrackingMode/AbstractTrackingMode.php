<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Timesheet\TrackingMode;

use App\Crm\Domain\Entity\Timesheet;
use DateTime;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractTrackingMode implements TrackingModeInterface
{
    use TrackingModeTrait;

    public function create(Timesheet $timesheet, ?Request $request = null): void
    {
        if ($request === null) {
            return;
        }

        $this->setBeginEndFromRequest($timesheet, $request);
        $this->setFromToFromRequest($timesheet, $request);
    }

    protected function setBeginEndFromRequest(Timesheet $entry, Request $request)
    {
        $start = $request->get('begin');
        if ($start === null) {
            return;
        }

        $start = DateTime::createFromFormat('Y-m-d', $start, $this->getTimezone($entry));
        if ($start === false) {
            return;
        }

        $entry->setBegin($start);

        // only check for an end date if a begin date was given
        $end = $request->get('end');
        if ($end === null) {
            return;
        }

        $end = DateTime::createFromFormat('Y-m-d', $end, $this->getTimezone($entry));
        if ($end === false) {
            return;
        }

        $start->setTime(10, 0, 0);
        $end->setTime(18, 0, 0);

        $entry->setEnd($end);
        $entry->setDuration($end->getTimestamp() - $start->getTimestamp());
    }

    protected function setFromToFromRequest(Timesheet $entry, Request $request)
    {
        $from = $request->get('from');
        if ($from === null) {
            return;
        }

        try {
            $from = new DateTime($from, $this->getTimezone($entry));
        } catch (\Exception $ex) {
            return;
        }

        $entry->setBegin($from);

        $to = $request->get('to');
        if ($to === null) {
            return;
        }

        try {
            $to = new DateTime($to, $this->getTimezone($entry));
        } catch (\Exception $ex) {
            return;
        }

        $entry->setEnd($to);
        $entry->setDuration($to->getTimestamp() - $from->getTimestamp());
    }
}
