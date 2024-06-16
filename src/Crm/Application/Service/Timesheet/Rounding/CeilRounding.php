<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Timesheet\Rounding;

use App\Crm\Domain\Entity\Timesheet;

final class CeilRounding implements RoundingInterface
{
    public function getId(): string
    {
        return 'ceil';
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
        $newBegin->setTimestamp($timestamp - $diff + $seconds);
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
        $newEnd->setTimestamp($timestamp - $diff + $seconds);
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

        $record->setDuration($timestamp - $diff + $seconds);
    }
}
