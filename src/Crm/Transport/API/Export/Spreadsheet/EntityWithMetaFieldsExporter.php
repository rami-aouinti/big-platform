<?php

declare(strict_types=1);

namespace App\Crm\Transport\API\Export\Spreadsheet;

use App\Crm\Transport\API\Export\Spreadsheet\Extractor\AnnotationExtractor;
use App\Crm\Transport\API\Export\Spreadsheet\Extractor\MetaFieldExtractor;
use App\Crm\Transport\Event\MetaDisplayEventInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * @package App\Crm\Transport\API\Export\Spreadsheet
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class EntityWithMetaFieldsExporter
{
    public function __construct(
        private SpreadsheetExporter $exporter,
        private AnnotationExtractor $annotationExtractor,
        private MetaFieldExtractor $metaFieldExtractor
    ) {
    }

    public function export(string $class, array $entries, MetaDisplayEventInterface $event): Spreadsheet
    {
        $columns = array_merge($this->annotationExtractor->extract($class), $this->metaFieldExtractor->extract($event));

        return $this->exporter->export($columns, $entries);
    }
}
