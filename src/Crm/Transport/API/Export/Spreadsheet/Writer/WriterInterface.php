<?php

declare(strict_types=1);

namespace App\Crm\Transport\API\Export\Spreadsheet\Writer;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

interface WriterInterface
{
    public function getFileExtension(): string;

    public function getContentType(): string;

    /**
     * Save the given spreadsheet
     */
    public function save(Spreadsheet $spreadsheet, array $options = []): \SplFileInfo;
}
