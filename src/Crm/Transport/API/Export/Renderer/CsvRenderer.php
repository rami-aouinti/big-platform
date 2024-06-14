<?php

declare(strict_types=1);

namespace App\Crm\Transport\API\Export\Renderer;

use App\Crm\Transport\API\Export\Base\CsvRenderer as BaseCsvRenderer;
use App\Crm\Transport\API\Export\RendererInterface;

final class CsvRenderer extends BaseCsvRenderer implements RendererInterface
{
    public function getIcon(): string
    {
        return 'csv';
    }

    public function getTitle(): string
    {
        return 'csv';
    }
}
