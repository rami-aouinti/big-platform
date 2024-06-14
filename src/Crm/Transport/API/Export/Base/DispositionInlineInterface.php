<?php

declare(strict_types=1);

namespace App\Crm\Transport\API\Export\Base;

interface DispositionInlineInterface
{
    public function setDispositionInline(bool $useInlineDisposition): void;
}
