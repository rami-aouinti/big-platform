<?php

declare(strict_types=1);

namespace App\Crm\Transport\API\Export;

use App\Crm\Domain\Entity\Timesheet;
use App\Crm\Domain\Repository\Query\TimesheetQuery;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Response;

#[AutoconfigureTag]
interface TimesheetExportInterface
{
    /**
     * @param Timesheet[] $timesheets
     */
    public function render(array $timesheets, TimesheetQuery $query): Response;

    public function getId(): string;
}
