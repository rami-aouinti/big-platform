<?php

declare(strict_types=1);

namespace App\Crm\Transport\API\Export\Spreadsheet\Extractor;

use App\Crm\Transport\API\Export\Spreadsheet\ColumnDefinition;
use App\Crm\Transport\Event\UserPreferenceDisplayEvent;
use App\User\Domain\Entity\User;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * @internal
 */
final class UserPreferenceExtractor implements ExtractorInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    /**
     * @param UserPreferenceDisplayEvent $value
     * @return ColumnDefinition[]
     * @throws ExtractorException
     */
    public function extract($value): array
    {
        if (!($value instanceof UserPreferenceDisplayEvent)) {
            throw new ExtractorException('UserPreferenceExtractor needs a UserPreferenceDisplayEvent instance for work');
        }

        $columns = [];

        $this->eventDispatcher->dispatch($value);

        foreach ($value->getPreferences() as $field) {
            if (!$field->isEnabled()) {
                continue;
            }

            $columns[] = new ColumnDefinition(
                $field->getLabel(),
                'string',
                function (User $user) use ($field) {
                    $meta = $user->getPreference($field->getName());
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
