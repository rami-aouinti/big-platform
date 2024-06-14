<?php

declare(strict_types=1);

namespace App\Crm\Transport\API\Export;

use App\Crm\Domain\Entity\Timesheet;
use App\Crm\Domain\Repository\Query\ExportQuery;
use App\Crm\Domain\Repository\TimesheetRepository;

/**
 * @package App\Crm\Transport\API\Export
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class TimesheetExportRepository implements ExportRepositoryInterface
{
    public function __construct(
        private TimesheetRepository $repository
    ) {
    }

    /**
     * @param Timesheet[] $items
     */
    public function setExported(array $items): void
    {
        $timesheets = [];

        foreach ($items as $item) {
            if ($item instanceof Timesheet) {
                $timesheets[] = $item;
            }
        }

        if (empty($timesheets)) {
            return;
        }

        $this->repository->setExported($timesheets);
    }

    public function getExportItemsForQuery(ExportQuery $query): iterable
    {
        return $this->repository->getTimesheetsForQuery($query, true);
    }

    public function getType(): string
    {
        return 'timesheet';
    }
}
