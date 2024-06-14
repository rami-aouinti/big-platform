<?php

declare(strict_types=1);

namespace App\Crm\Application\Pdf;

use Exception;

interface HtmlToPdfConverter
{
    /**
     * Returns the binary content of the PDF, which can be saved as file.
     * Throws an exception if conversion fails.
     *
     * @throws Exception
     */
    public function convertToPdf(string $html, array $options = []): string;
}
