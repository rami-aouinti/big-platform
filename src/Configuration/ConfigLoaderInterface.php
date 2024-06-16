<?php

declare(strict_types=1);

namespace App\Configuration;

/**
 * @internal
 */
interface ConfigLoaderInterface
{
    /**
     * @return array<string, string|null>
     */
    public function getConfigurations(): array;
}
