<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Timesheet\Rounding;

use App\Crm\Domain\Entity\Timesheet;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * Apply rounding rules to the given timesheet.
 */
#[AutoconfigureTag]
interface RoundingInterface
{
    public function roundBegin(Timesheet $record, int $minutes): void;

    public function roundEnd(Timesheet $record, int $minutes): void;

    public function roundDuration(Timesheet $record, int $minutes): void;

    public function getId(): string;
}
