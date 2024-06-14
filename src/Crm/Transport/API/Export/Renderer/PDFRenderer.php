<?php

declare(strict_types=1);

namespace App\Crm\Transport\API\Export\Renderer;

use App\Crm\Transport\API\Export\Base\PDFRenderer as BasePDFRenderer;
use App\Crm\Transport\API\Export\ExportRendererInterface;

/**
 * @package App\Crm\Transport\API\Export\Renderer
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class PDFRenderer extends BasePDFRenderer implements ExportRendererInterface
{
    public function getIcon(): string
    {
        return 'pdf';
    }

    public function getTitle(): string
    {
        return 'pdf';
    }
}
