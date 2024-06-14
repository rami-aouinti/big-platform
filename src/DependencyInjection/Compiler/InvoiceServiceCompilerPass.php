<?php

declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use App\Crm\Application\Service\Invoice\CalculatorInterface;
use App\Crm\Application\Service\Invoice\InvoiceItemRepositoryInterface;
use App\Crm\Application\Service\Invoice\NumberGeneratorInterface;
use App\Crm\Application\Service\Invoice\RendererInterface;
use App\Crm\Application\Service\Invoice\ServiceInvoice;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Dynamically adds all dependencies to the InvoiceService.
 */
final class InvoiceServiceCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $definition = $container->findDefinition(ServiceInvoice::class);

        $taggedRenderer = $container->findTaggedServiceIds(RendererInterface::class);
        foreach ($taggedRenderer as $id => $tags) {
            $definition->addMethodCall('addRenderer', [new Reference($id)]);
        }

        $taggedGenerator = $container->findTaggedServiceIds(NumberGeneratorInterface::class);
        foreach ($taggedGenerator as $id => $tags) {
            $definition->addMethodCall('addNumberGenerator', [new Reference($id)]);
        }

        $taggedCalculator = $container->findTaggedServiceIds(CalculatorInterface::class);
        foreach ($taggedCalculator as $id => $tags) {
            $definition->addMethodCall('addCalculator', [new Reference($id)]);
        }

        $taggedRepository = $container->findTaggedServiceIds(InvoiceItemRepositoryInterface::class);
        foreach ($taggedRepository as $id => $tags) {
            $definition->addMethodCall('addInvoiceItemRepository', [new Reference($id)]);
        }
    }
}
