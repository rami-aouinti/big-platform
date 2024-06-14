<?php

declare(strict_types=1);

namespace App\Crm\Transport\API\Export\Renderer;

use App\Crm\Application\Service\Project\ProjectStatisticService;
use App\Pdf\HtmlToPdfConverter;
use Twig\Environment;

final class PdfRendererFactory
{
    public function __construct(
        private Environment $twig,
        private HtmlToPdfConverter $converter,
        private ProjectStatisticService $projectStatisticService
    ) {
    }

    public function create(string $id, string $template): PDFRenderer
    {
        $renderer = new PDFRenderer($this->twig, $this->converter, $this->projectStatisticService);
        $renderer->setId($id);
        $renderer->setTemplate($template);

        return $renderer;
    }
}
