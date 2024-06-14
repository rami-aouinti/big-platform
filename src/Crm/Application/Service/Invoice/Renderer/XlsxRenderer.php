<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Invoice\Renderer;

use App\Crm\Application\Service\Invoice\RendererInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

final class XlsxRenderer extends AbstractSpreadsheetRenderer implements RendererInterface
{
    protected function getFileExtensions(): array
    {
        return ['.xlsx', '.xls'];
    }

    protected function getContentType(): string
    {
        return 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    }

    protected function saveSpreadsheet(Spreadsheet $spreadsheet): string
    {
        $filename = @tempnam(sys_get_temp_dir(), 'kimai-invoice-xlsx');
        if ($filename === false) {
            throw new \Exception('Could not open temporary file');
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($filename);

        return $filename;
    }
}
