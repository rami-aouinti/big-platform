<?php

declare(strict_types=1);

namespace App\Crm\Transport\API\Export\Spreadsheet\CellFormatter;

use App\Utils\StringHelper;
use PhpOffice\PhpSpreadsheet\Cell\CellAddress;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

final class StringFormatter implements CellFormatterInterface
{
    public function setFormattedValue(Worksheet $sheet, int $column, int $row, $value): void
    {
        if ($value === null) {
            $sheet->setCellValue(CellAddress::fromColumnAndRow($column, $row), '');

            return;
        }

        if (!\is_string($value)) {
            throw new \InvalidArgumentException('Unsupported value given, only string is supported');
        }

        $sheet->setCellValue(CellAddress::fromColumnAndRow($column, $row), StringHelper::sanitizeDDE($value));
    }
}
