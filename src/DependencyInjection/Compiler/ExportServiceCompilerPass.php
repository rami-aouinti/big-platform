<?php

declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use App\Crm\Application\Service\Invoice\RendererInterface;
use App\Crm\Transport\API\Export\ExportRepositoryInterface;
use App\Crm\Transport\API\Export\ServiceExport;
use App\Crm\Transport\API\Export\TimesheetExportInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Dynamically adds all dependencies to the ExportService.
 */
final class ExportServiceCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $definition = $container->findDefinition(ServiceExport::class);

        $taggedRenderer = $container->findTaggedServiceIds(RendererInterface::class);
        foreach ($taggedRenderer as $id => $tags) {
            $definition->addMethodCall('addRenderer', [new Reference($id)]);
        }

        $taggedExporter = $container->findTaggedServiceIds(TimesheetExportInterface::class);
        foreach ($taggedExporter as $id => $tags) {
            $definition->addMethodCall('addTimesheetExporter', [new Reference($id)]);
        }

        $taggedRepository = $container->findTaggedServiceIds(ExportRepositoryInterface::class);
        foreach ($taggedRepository as $id => $tags) {
            $definition->addMethodCall('addExportRepository', [new Reference($id)]);
        }

        $exportDocuments = $container->getParameter('kimai.export.documents');
        if (\is_array($exportDocuments)) {
            $path = \dirname(__DIR__, 3) . DIRECTORY_SEPARATOR;
            foreach ($exportDocuments as $exportPath) {
                if (!is_dir($path . $exportPath)) {
                    continue;
                }

                $definition->addMethodCall('addDirectory', [realpath($path . $exportPath)]);
            }
        }
    }
}
