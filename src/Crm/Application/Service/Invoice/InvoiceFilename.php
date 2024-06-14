<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Invoice;

use App\Utils\FileHelper;
use Exception;

use function count;

/**
 * @package App\Crm\Application\Service\Invoice
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class InvoiceFilename
{
    private string $filename;

    /**
     * @throws Exception
     */
    public function __construct(InvoiceModel $model)
    {
        $filename = $model->getInvoiceNumber();

        $filename = str_replace(['/', '\\'], '-', $filename);

        $company = $model->getCustomer()->getCompany();
        if (empty($company)) {
            $company = $model->getCustomer()->getName();
        }

        if (!empty($company)) {
            $filename .= '-' . $this->convert($company);
        }

        if ($model->getQuery() !== null) {
            $projects = $model->getQuery()->getProjects();
            if (count($projects) === 1) {
                $filename .= '-' . $this->convert($projects[0]->getName());
            }
        }

        $this->filename = $filename;
    }

    public function __toString(): string
    {
        return $this->getFilename();
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    private function convert(string $filename): string
    {
        return FileHelper::convertToAsciiFilename($filename);
    }
}
