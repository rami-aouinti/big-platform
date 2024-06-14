<?php

declare(strict_types=1);

namespace App\Crm\Transport\API\Export\Renderer;

use App\Crm\Transport\API\Export\Base\HtmlRenderer as BaseHtmlRenderer;
use App\Crm\Transport\API\Export\ExportRendererInterface;

/**
 * @package App\Crm\Transport\API\Export\Renderer
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class HtmlRenderer extends BaseHtmlRenderer implements ExportRendererInterface
{
    public function getIcon(): string
    {
        return 'print';
    }

    public function getTitle(): string
    {
        return 'print';
    }
}
