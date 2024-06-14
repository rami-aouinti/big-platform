<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Timesheet\Rounding;

use App\Crm\Domain\Entity\Timesheet;

final class ClosestRounding implements RoundingInterface
{
    public function getId(): string
    {
        return 'closest';
    }

    public function roundBegin(Timesheet $record, int $minutes): void
    {
        if ($minutes <= 0) {
            return;
        }

        $timestamp = $record->getBegin()->getTimestamp();
        $seconds = $minutes * 60;
        $diff = $timestamp % $seconds;

        if ($diff === 0) {
            return;
        }

        $newBegin = clone $record->getBegin();
        if ($diff > ($seconds / 2)) {
            $newBegin->setTimestamp($timestamp - $diff + $seconds);
        } else {
            $newBegin->setTimestamp($timestamp - $diff);
        }
        $record->setBegin($newBegin);
    }

    public function roundEnd(Timesheet $record, int $minutes): void
    {
        if ($minutes <= 0) {
            return;
        }

        $timestamp = $record->getEnd()->getTimestamp();
        $seconds = $minutes * 60;
        $diff = $timestamp % $seconds;

        if ($diff === 0) {
            return;
        }

        $newEnd = clone $record->getEnd();
        if ($diff > ($seconds / 2)) {
            $newEnd->setTimestamp($timestamp - $diff + $seconds);
        } else {
            $newEnd->setTimestamp($timestamp - $diff);
        }
        $record->setEnd($newEnd);
    }

    public function roundDuration(Timesheet $record, int $minutes): void
    {
        if ($minutes <= 0) {
            return;
        }

        $timestamp = $record->getDuration() ?? 0;
        $seconds = $minutes * 60;
        $diff = $timestamp % $seconds;

        if ($diff === 0) {
            return;
        }

        if ($diff > ($seconds / 2)) {
            $record->setDuration($timestamp - $diff + $seconds);
        } else {
            $record->setDuration($timestamp - $diff);
        }
    }
}
