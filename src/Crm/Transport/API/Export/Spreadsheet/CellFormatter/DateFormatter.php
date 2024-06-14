<?php

declare(strict_types=1);

namespace App\Crm\Transport\API\Export\Spreadsheet\CellFormatter;

use PhpOffice\PhpSpreadsheet\Cell\CellAddress;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

final class DateFormatter implements CellFormatterInterface
{
    public function setFormattedValue(Worksheet $sheet, int $column, int $row, $value): void
    {
        if ($value === null) {
            $sheet->setCellValue(CellAddress::fromColumnAndRow($column, $row), '');

            return;
        }

        if (!$value instanceof \DateTimeInterface) {
            throw new \InvalidArgumentException('Unsupported value given, only DateTimeInterface is supported');
        }

        $sheet->setCellValue(CellAddress::fromColumnAndRow($column, $row), Date::PHPToExcel($value));
        $sheet->getStyle(CellAddress::fromColumnAndRow($column, $row))->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDD);
    }
}
