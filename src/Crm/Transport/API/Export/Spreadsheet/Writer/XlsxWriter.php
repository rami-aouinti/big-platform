<?php

declare(strict_types=1);

namespace App\Crm\Transport\API\Export\Spreadsheet\Writer;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

final class XlsxWriter implements WriterInterface
{
    public function getFileExtension(): string
    {
        return 'xlsx';
    }

    public function getContentType(): string
    {
        return 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    }

    /**
     * Options:
     * - freeze (string, default: null)    Coordinate of a column to freeze, like: D2
     * - autofilter (bool, default true)  Enable auto filter for header row
     *
     * @throws \Exception
     */
    public function save(Spreadsheet $spreadsheet, array $options = []): \SplFileInfo
    {
        $options = array_merge([
            'autofilter' => true,
            'freeze' => null,
        ], $options);

        $filename = @tempnam(sys_get_temp_dir(), 'kimai-export-xlsx');
        if ($filename === false) {
            throw new \Exception('Could not open temporary file');
        }

        // Store expensive calculations for later
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        // Enable auto filter for header row
        if ($options['autofilter'] !== false) {
            $sheet->setAutoFilter('A1:' . $highestColumn . '1');
        }

        // Freeze first row and date & time columns for easier navigation
        if (!empty($options['freeze'])) {
            $sheet->freezePane($options['freeze']);
        }

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

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($filename);

        return new \SplFileInfo($filename);
    }
}
