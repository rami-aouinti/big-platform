<?php

declare(strict_types=1);

namespace App\Crm\Transport\API\Export;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag]
interface RendererInterface extends ExportRendererInterface
{
}
