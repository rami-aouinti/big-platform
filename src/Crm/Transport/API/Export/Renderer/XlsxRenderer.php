<?php

declare(strict_types=1);

namespace App\Crm\Transport\API\Export\Renderer;

use App\Crm\Transport\API\Export\Base\XlsxRenderer as BaseXlsxRenderer;
use App\Crm\Transport\API\Export\RendererInterface;

final class XlsxRenderer extends BaseXlsxRenderer implements RendererInterface
{
    public function getIcon(): string
    {
        return 'xlsx';
    }

    public function getTitle(): string
    {
        return 'xlsx';
    }
}
