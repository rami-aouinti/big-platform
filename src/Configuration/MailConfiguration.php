<?php

declare(strict_types=1);

namespace App\Configuration;

/**
 * Class MailConfiguration
 *
 * @package App\Configuration
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final readonly class MailConfiguration
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
