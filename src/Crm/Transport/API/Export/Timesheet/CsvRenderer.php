<?php

declare(strict_types=1);

namespace App\Crm\Transport\API\Export\Timesheet;

use App\Crm\Transport\API\Export\Base\CsvRenderer as BaseCsvRenderer;
use App\Crm\Transport\API\Export\TimesheetExportInterface;

final class CsvRenderer extends BaseCsvRenderer implements TimesheetExportInterface
{
}
