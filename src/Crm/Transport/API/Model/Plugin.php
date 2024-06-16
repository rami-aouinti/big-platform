<?php

declare(strict_types=1);

namespace App\Crm\Transport\API\Model;

use App\Plugin\Plugin as CorePlugin;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\ExclusionPolicy('all')]
final class Plugin
{
    /**
     * The plugin name, eg. "ExpensesBundle"
     */
    #[Serializer\Expose]
    #[Serializer\Groups(['Default'])]
    #[Serializer\Type(name: 'string')]
    private ?string $name = null;
    /**
     * The plugin version, eg. "1.14"
     */
    #[Serializer\Expose]
    #[Serializer\Groups(['Default'])]
    #[Serializer\Type(name: 'string')]
    private ?string $version = null;

    public function __construct(CorePlugin $plugin)
    {
        $this->name = $plugin->getId();
        $this->version = $plugin->getMetadata()->getVersion();
    }
}
