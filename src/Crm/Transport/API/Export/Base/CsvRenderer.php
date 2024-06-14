<?php

declare(strict_types=1);

namespace App\Crm\Transport\API\Export\Base;

use PhpOffice\PhpSpreadsheet\Cell\CellAddress;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CsvRenderer extends AbstractSpreadsheetRenderer
{
    public function getFileExtension(): string
    {
        return '.csv';
    }

    public function getId(): string
    {
        return 'csv';
    }

    protected function getContentType(): string
    {
        return 'text/csv';
    }

    /**
     * @throws \Exception
     */
    protected function saveSpreadsheet(Spreadsheet $spreadsheet): string
    {
        $filename = @tempnam(sys_get_temp_dir(), 'kimai-export-csv');
        if ($filename === false) {
            throw new \Exception('Could not open temporary file');
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Csv');
        $writer->save($filename);

        return $filename;
    }

    protected function setDuration(Worksheet $sheet, int $column, int $row, ?int $duration): void
    {
        $sheet->setCellValue(CellAddress::fromColumnAndRow($column, $row), sprintf('=%s', $duration ?? 0));
    }

    protected function setRate(Worksheet $sheet, int $column, int $row, ?float $rate, ?string $currency): void
    {
        $sheet->setCellValue(CellAddress::fromColumnAndRow($column, $row), $rate);
        if ($rate === 0.00) {
            return;
        }
        $this->setRateStyle($sheet, $column, $row, $currency);
    }
}
