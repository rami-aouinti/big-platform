<?php

declare(strict_types=1);

namespace App\Plugin;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag]
interface PluginInterface
{
    public function getName(): string;

    public function getPath(): string;
}
