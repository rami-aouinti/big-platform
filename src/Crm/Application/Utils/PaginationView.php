<?php

declare(strict_types=1);

namespace App\Crm\Application\Utils;

use Pagerfanta\View\Template\TemplateInterface;
use Pagerfanta\View\TwitterBootstrap5View;

final class PaginationView extends TwitterBootstrap5View
{
    protected function getDefaultProximity(): int
    {
        return 2;
    }

    protected function createDefaultTemplate(): TemplateInterface
    {
        return new PaginationTemplate();
    }
}
