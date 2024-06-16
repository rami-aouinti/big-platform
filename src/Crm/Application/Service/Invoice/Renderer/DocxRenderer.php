<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Invoice\Renderer;

use App\Crm\Application\Model\InvoiceDocument;
use App\Crm\Application\Service\Invoice\InvoiceModel;
use App\Crm\Application\Service\Invoice\RendererInterface;
use Exception;
use PhpOffice\PhpWord\Escaper\Xml;
use PhpOffice\PhpWord\Exception\CopyFileException;
use PhpOffice\PhpWord\Exception\CreateTemporaryFileException;
use PhpOffice\PhpWord\Exception\Exception as OfficeException;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Component\HttpFoundation\File\Stream;
use Symfony\Component\HttpFoundation\Response;

use function count;

/**
 * @package App\Crm\Application\Service\Invoice\Renderer
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class DocxRenderer extends AbstractRenderer implements RendererInterface
{
    /**
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     * @throws Exception
     */
    public function render(InvoiceDocument $document, InvoiceModel $model): Response
    {
        Settings::setOutputEscapingEnabled(false);

        $xmlEscaper = new Xml();
        $template = new TemplateProcessor($document->getFilename());

        foreach ($model->toArray() as $search => $replace) {
            $replace = $xmlEscaper->escape($replace);
            $replace = preg_replace('/\n|\r\n?/', '</w:t><w:br /><w:t xml:space="preserve">', $replace);

            $template->setValue($search, $replace);
        }

        try {
            $template->cloneRow('entry.description', count($model->getCalculator()->getEntries()));
        } catch (OfficeException $ex) {
            try {
                $template->cloneRow('entry.row', count($model->getCalculator()->getEntries()));
            } catch (OfficeException $ex) {
                @trigger_error('Invoice document did not contain a clone row, was that on purpose?');
            }
        }

        $i = 1;
        foreach ($model->getCalculator()->getEntries() as $entry) {
            $values = $model->itemToArray($entry);
            foreach ($values as $search => $replace) {
                $replace = $xmlEscaper->escape($replace);
                $replace = preg_replace('/\n|\r\n?/', '</w:t><w:br /><w:t xml:space="preserve">', $replace);

                $template->setValue($search . '#' . $i, $replace);
            }
            $i++;
        }

        $cacheFile = @tempnam(sys_get_temp_dir(), 'kimai-invoice-docx');
        if ($cacheFile === false) {
            throw new Exception('Could not open temporary file');
        }

        $template->saveAs($cacheFile);

        clearstatcache(true, $cacheFile);

        $filename = $this->buildFilename($model) . '.' . $document->getFileExtension();

        return $this->getFileResponse(new Stream($cacheFile), $filename);
    }

    protected function getFileExtensions(): array
    {
        return ['.docx'];
    }

    protected function getContentType(): string
    {
        return 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    }
}
