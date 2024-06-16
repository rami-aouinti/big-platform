<?php

declare(strict_types=1);

namespace App\Crm\Transport\API\Export\Spreadsheet\CellFormatter;

use PhpOffice\PhpSpreadsheet\Cell\CellAddress;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

final class DurationFormatter implements CellFormatterInterface
{
    public const DURATION_FORMAT = '[hh]:mm';

    public function setFormattedValue(Worksheet $sheet, int $column, int $row, $value): void
    {
        if ($value === null) {
            $value = 0;
        }

        if (!\is_int($value)) {
            throw new \InvalidArgumentException('Unsupported value given, only int is supported');
        }

        $sheet->setCellValue(CellAddress::fromColumnAndRow($column, $row), sprintf('=%s/86400', $value));
        $sheet->getStyle(CellAddress::fromColumnAndRow($column, $row))->getNumberFormat()->setFormatCode(self::DURATION_FORMAT);
    }
}
