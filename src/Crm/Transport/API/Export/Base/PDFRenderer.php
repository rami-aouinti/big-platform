<?php

declare(strict_types=1);

namespace App\Crm\Transport\API\Export\Base;

use App\Crm\Application\Pdf\HtmlToPdfConverter;
use App\Crm\Application\Pdf\PdfContext;
use App\Crm\Application\Pdf\PdfRendererTrait;
use App\Crm\Application\Service\Project\ProjectStatisticService;
use App\Crm\Application\Twig\SecurityPolicy\ExportPolicy;
use App\Crm\Domain\Entity\ExportableItem;
use App\Crm\Domain\Repository\Query\TimesheetQuery;
use App\Crm\Transport\API\Export\ExportFilename;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Extension\SandboxExtension;

class PDFRenderer implements DispositionInlineInterface
{
    use RendererTrait;
    use PDFRendererTrait;

    private string $id = 'pdf';
    private string $template = 'default.pdf.twig';
    private array $pdfOptions = [];

    public function __construct(
        private Environment $twig,
        private HtmlToPdfConverter $converter,
        private ProjectStatisticService $projectStatisticService
    ) {
    }

    public function getPdfOptions(): array
    {
        return $this->pdfOptions;
    }

    public function setPdfOption(string $key, string $value): self
    {
        $this->pdfOptions[$key] = $value;

        return $this;
    }

    /**
     * @param ExportableItem[] $timesheets
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function render(array $timesheets, TimesheetQuery $query): Response
    {
        $filename = new ExportFilename($query);
        $context = new PdfContext();
        $context->setOption('filename', $filename->getFilename());

        $summary = $this->calculateSummary($timesheets);

        // enable basic security measures
        $sandbox = new SandboxExtension(new ExportPolicy());
        $sandbox->enableSandbox();
        $this->twig->addExtension($sandbox);

        $content = $this->twig->render($this->getTemplate(), array_merge([
            'entries' => $timesheets,
            'query' => $query,
            'summaries' => $summary,
            'budgets' => $this->calculateProjectBudget($timesheets, $query, $this->projectStatisticService),
            'decimal' => false,
            'pdfContext' => $context,
        ], $this->getOptions($query)));

        $pdfOptions = array_merge($context->getOptions(), $this->getPdfOptions());

        $content = $this->converter->convertToPdf($content, $pdfOptions);

        return $this->createPdfResponse($content, $context);
    }

    public function setTemplate(string $filename): self
    {
        $this->template = $filename;

        return $this;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    protected function getTemplate(): string
    {
        return '@export/' . $this->template;
    }

    protected function getOptions(TimesheetQuery $query): array
    {
        $decimal = false;
        if ($query->getCurrentUser() !== null) {
            $decimal = $query->getCurrentUser()->isExportDecimal();
        } elseif ($query->getUser() !== null) {
            $decimal = $query->getUser()->isExportDecimal();
        }

        return [
            'decimal' => $decimal,
        ];
    }
}
