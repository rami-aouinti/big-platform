<?php

declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use App\Crm\Application\Widget\WidgetInterface;
use App\Crm\Application\Widget\WidgetService;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Dynamically adds all widgets to the WidgetRepository.
 */
final class WidgetCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $definition = $container->findDefinition(WidgetService::class);

        $taggedRenderer = $container->findTaggedServiceIds(WidgetInterface::class);
        foreach ($taggedRenderer as $id => $tags) {
            $definition->addMethodCall('registerWidget', [new Reference($id)]);
        }
    }
}
