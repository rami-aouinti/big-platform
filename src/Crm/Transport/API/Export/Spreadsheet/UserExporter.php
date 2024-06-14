<?php

declare(strict_types=1);

namespace App\Crm\Transport\API\Export\Spreadsheet;

use App\Crm\Transport\API\Export\Spreadsheet\Extractor\AnnotationExtractor;
use App\Crm\Transport\API\Export\Spreadsheet\Extractor\UserPreferenceExtractor;
use App\Crm\Transport\Event\UserPreferenceDisplayEvent;
use App\User\Domain\Entity\User;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

final class UserExporter
{
    public function __construct(
        private SpreadsheetExporter $exporter,
        private AnnotationExtractor $annotationExtractor,
        private UserPreferenceExtractor $userPreferenceExtractor
    ) {
    }

    /**
     * @param User[] $entries
     *
     * @throws App\Crm\Transport\API\Export\Spreadsheet\Extractor\ExtractorException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function export(array $entries, UserPreferenceDisplayEvent $event): Spreadsheet
    {
        $columns = array_merge(
            $this->annotationExtractor->extract(User::class),
            $this->userPreferenceExtractor->extract($event)
        );

        return $this->exporter->export($columns, $entries);
    }
}
