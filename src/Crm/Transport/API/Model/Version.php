<?php

declare(strict_types=1);

namespace App\Crm\Transport\API\Model;

use App\Constants;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\ExclusionPolicy('all')]
final class Version
{
    /**
     * Kimai Version, eg. "2.0.0"
     */
    #[Serializer\Expose]
    #[Serializer\Groups(['Default'])]
    #[Serializer\Type(name: 'string')]
    public readonly string $version;
    /**
     * Kimai Version as integer, eg. 20000
     *
     * Follows the same logic as PHP_VERSION_ID, see https://www.php.net/manual/de/function.phpversion.php
     */
    #[Serializer\Expose]
    #[Serializer\Groups(['Default'])]
    #[Serializer\Type(name: 'integer')]
    public readonly int $versionId;
    /**
     * A full copyright notice
     */
    #[Serializer\Expose]
    #[Serializer\Groups(['Default'])]
    #[Serializer\Type(name: 'string')]
    public readonly string $copyright;

    public function __construct()
    {
        $this->version = Constants::VERSION;
        $this->versionId = Constants::VERSION_ID;
        $this->copyright = Constants::SOFTWARE . ' ' . Constants::VERSION . ' by Kevin Papst.';
    }
}
