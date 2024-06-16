<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Calendar;

final class Google
{
    /**
     * @param GoogleSource[] $sources
     */
    public function __construct(
        private string $apiKey,
        private array $sources = []
    ) {
    }

    /**
     * @return GoogleSource[]
     */
    public function getSources(): array
    {
        return $this->sources;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }
}
