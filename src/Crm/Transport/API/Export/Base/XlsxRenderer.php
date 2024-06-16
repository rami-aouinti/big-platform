<?php

declare(strict_types=1);

namespace App\Crm\Transport\API\Export\Base;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class XlsxRenderer extends AbstractSpreadsheetRenderer
{
    public function getFileExtension(): string
    {
        return '.xlsx';
    }

    public function getId(): string
    {
        return 'xlsx';
    }
    protected function isTotalRowSupported(): bool
    {
        return true;
    }

    protected function getContentType(): string
    {
        return 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    }

    /**
     * @throws \Exception
     */
    protected function saveSpreadsheet(Spreadsheet $spreadsheet): string
    {
        $filename = @tempnam(sys_get_temp_dir(), 'kimai-export-xlsx');
        if ($filename === false) {
            throw new \Exception('Could not open temporary file');
        }

        $this->applyStyles($spreadsheet);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($filename);

        return $filename;
    }

    protected function applyStyles(Spreadsheet $spreadsheet): void
    {
        // Store expensive calculations for later
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        // Enable auto filter for header row
        $sheet->setAutoFilter('A1:' . $highestColumn . '1');

        // Freeze first row and date & time columns for easier navigation
        $sheet->freezePane('D2');

        foreach ($sheet->getColumnIterator() as $columnName => $column) {
            // We default to a reasonable auto-width decided by the client,
            // sadly ->getDefaultColumnDimension() is not supported so it needs
            // to be specific about what column should be auto sized.
            $col = $sheet->getColumnDimension($columnName);

            // If no other width is specified (which defaults to -1)
            if ((int)$col->getWidth() === -1) {
                $col->setAutoSize(true);
            }
        }

        // Text inside cells should be top left
        $sheet
            ->getStyle('A2:' . $highestColumn . $highestRow)
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_TOP)
            ->setHorizontal(Alignment::HORIZONTAL_LEFT);
    }
}
