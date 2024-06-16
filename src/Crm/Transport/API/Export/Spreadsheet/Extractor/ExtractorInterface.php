<?php

declare(strict_types=1);

namespace App\Crm\Transport\API\Export\Spreadsheet\Extractor;

use App\Crm\Transport\API\Export\Spreadsheet\ColumnDefinition;

/**
 * Extract ColumnDefinition objects from various sources.
 */
interface ExtractorInterface
{
    /**
     * @return ColumnDefinition[]
     * @throws ExtractorException
     */
    public function extract(mixed $value): array;
}
