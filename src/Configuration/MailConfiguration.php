<?php

declare(strict_types=1);

namespace App\Configuration;

final class MailConfiguration
{
    public function __construct(
        private string $mailFrom
    ) {
    }

    public function getFromAddress(): ?string
    {
        if (empty($this->mailFrom)) {
            return null;
        }

        return $this->mailFrom;
    }
}
