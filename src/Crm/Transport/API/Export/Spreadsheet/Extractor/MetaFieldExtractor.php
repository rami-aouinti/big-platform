<?php

declare(strict_types=1);

namespace App\Crm\Transport\API\Export\Spreadsheet\Extractor;

use App\Crm\Domain\Entity\EntityWithMetaFields;
use App\Crm\Transport\API\Export\Spreadsheet\ColumnDefinition;
use App\Crm\Transport\Event\MetaDisplayEventInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * @internal
 */
final class MetaFieldExtractor implements ExtractorInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    /**
     * @param MetaDisplayEventInterface $value
     * @return ColumnDefinition[]
     * @throws ExtractorException
     */
    public function extract($value): array
    {
        if (!($value instanceof MetaDisplayEventInterface)) {
            throw new ExtractorException('MetaFieldExtractor needs a MetaDisplayEventInterface instance for work');
        }

        $columns = [];

        $this->eventDispatcher->dispatch($value);

        foreach ($value->getFields() as $field) {
            if (!$field->isVisible()) {
                continue;
            }

            $columns[] = new ColumnDefinition(
                $field->getLabel(),
                'string',
                function (EntityWithMetaFields $entityWithMetaFields) use ($field) {
                    $meta = $entityWithMetaFields->getMetaField($field->getName());
                    if ($meta === null) {
                        return null;
                    }

                    return $meta->getValue();
                }
            );
        }

        return $columns;
    }
}
