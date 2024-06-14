<?php

declare(strict_types=1);

namespace App\Crm\Transport\API\Export\Timesheet;

use App\Crm\Transport\API\Export\Base\PDFRenderer as BasePDFRenderer;
use App\Crm\Transport\API\Export\TimesheetExportInterface;

final class PDFRenderer extends BasePDFRenderer implements TimesheetExportInterface
{
}
