<?php

declare(strict_types=1);

namespace App\Crm\Domain\Repository\Loader;

interface LoaderInterface
{
    /**
     * Prepares the given database results, to prevent lazy loading.
     */
    public function loadResults(array $results): void;
}
