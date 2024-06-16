<?php

declare(strict_types=1);

namespace App\Crm\Domain\Repository\Loader;

final class DefaultLoader implements LoaderInterface
{
    public function loadResults(array $results): void
    {
        // nothing to do here, the results are already fully populated

        // if your entities have lazy collections or other data that needs population,
        // consider to create a custom loader!
    }
}
