<?php

declare(strict_types=1);

namespace App\Crm\Application\Twig\Runtime;

use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookupInterface;
use Twig\Extension\RuntimeExtensionInterface;

final class EncoreExtension implements RuntimeExtensionInterface, ServiceSubscriberInterface
{
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly string $projectDirectory
    ) {
    }

    public static function getSubscribedServices(): array
    {
        return [
            EntrypointLookupInterface::class,
        ];
    }

    public function getEncoreEntryCssSource(string $packageName): string
    {
        $lookup = $this->container->get(EntrypointLookupInterface::class);
        $files = $lookup->getCssFiles($packageName);

        $source = '';

        foreach ($files as $file) {
            $source .= file_get_contents($this->projectDirectory . '/public/' . $file);
        }

        $lookup->reset();

        return $source;
    }
}
