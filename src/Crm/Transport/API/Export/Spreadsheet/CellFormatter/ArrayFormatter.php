<?php

declare(strict_types=1);

namespace App\Crm\Transport\API\Export\Spreadsheet\CellFormatter;

use PhpOffice\PhpSpreadsheet\Cell\CellAddress;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

final class ArrayFormatter implements CellFormatterInterface
{
    public function setFormattedValue(Worksheet $sheet, int $column, int $row, $value): void
    {
        if ($value === null) {
            $sheet->setCellValue(CellAddress::fromColumnAndRow($column, $row), '');

            return;
        }

        if (!\is_array($value)) {
            throw new \InvalidArgumentException('Unsupported value given, only array is supported');
        }

        $sheet->setCellValue(CellAddress::fromColumnAndRow($column, $row), implode(';', $value));
    }
}
