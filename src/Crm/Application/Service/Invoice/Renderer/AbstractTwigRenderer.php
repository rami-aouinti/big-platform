<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Invoice\Renderer;

use App\Crm\Application\Model\InvoiceDocument;
use App\Crm\Application\Service\Invoice\InvoiceModel;
use App\Crm\Application\Service\Invoice\RendererInterface;
use App\Crm\Application\Twig\TwigRendererTrait;
use Twig\Environment;

/**
 * @internal
 */
abstract class AbstractTwigRenderer implements RendererInterface
{
    use TwigRendererTrait;

    public function __construct(
        private Environment $twig
    ) {
    }

    protected function renderTwigTemplate(InvoiceDocument $document, InvoiceModel $model, array $options = []): string
    {
        $language = $model->getTemplate()->getLanguage();
        $formatLocale = $model->getFormatter()->getLocale();
        $template = '@invoice/' . basename($document->getFilename());
        $entries = [];
        foreach ($model->getCalculator()->getEntries() as $entry) {
            $entries[] = $model->itemToArray($entry);
        }

        $options = array_merge([
            // model should not be used in the future, but we can likely not remove it
            'model' => $model,
            // new since 1.16.7 - templates should only use the pre-generated values
            'invoice' => $model->toArray(),
            // new since 1.19.5 - templates should only use the pre-generated values
            'entries' => $entries,
        ], $options);

        return $this->renderTwigTemplateWithLanguage($this->twig, $template, $options, $language, $formatLocale);
    }
}
