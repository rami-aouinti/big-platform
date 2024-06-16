<?php

declare(strict_types=1);

namespace App\Crm\Transport\API\Export\Timesheet;

use App\Crm\Transport\API\Export\Base\HtmlRenderer as BaseHtmlRenderer;
use App\Crm\Transport\API\Export\TimesheetExportInterface;

final class HtmlRenderer extends BaseHtmlRenderer implements TimesheetExportInterface
{
    public function getId(): string
    {
        return 'print';
    }
    protected function getTemplate(): string
    {
        return 'timesheet/export.html.twig';
    }
}
