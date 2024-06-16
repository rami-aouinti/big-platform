<?php

declare(strict_types=1);

namespace App\Crm\Transport\API\Export\Spreadsheet;

use App\Crm\Transport\API\Export\Spreadsheet\Extractor\AnnotationExtractor;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

final class AnnotatedObjectExporter
{
    public function __construct(
        private SpreadsheetExporter $spreadsheetExporter,
        private AnnotationExtractor $annotationExtractor
    ) {
    }

    public function export(string $class, array $entries): Spreadsheet
    {
        $columns = $this->annotationExtractor->extract($class);

        return $this->spreadsheetExporter->export($columns, $entries);
    }
}
