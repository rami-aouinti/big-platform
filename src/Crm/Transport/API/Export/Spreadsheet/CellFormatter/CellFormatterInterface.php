<?php

declare(strict_types=1);

namespace App\Crm\Transport\API\Export\Spreadsheet\CellFormatter;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

interface CellFormatterInterface
{
    /**
     * @param mixed $value
     * @throws \InvalidArgumentException
     */
    public function setFormattedValue(Worksheet $sheet, int $column, int $row, $value): void;
}
