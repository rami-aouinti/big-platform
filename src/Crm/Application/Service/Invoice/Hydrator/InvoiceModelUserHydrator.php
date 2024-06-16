<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Invoice\Hydrator;

use App\Crm\Application\Service\Invoice\InvoiceModel;
use App\Crm\Application\Service\Invoice\InvoiceModelHydrator;

final class InvoiceModelUserHydrator implements InvoiceModelHydrator
{
    public function hydrate(InvoiceModel $model): array
    {
        $user = $model->getUser();

        if ($user === null) {
            return [];
        }

        $values = [
            'user.name' => $user->getUserIdentifier(),
            'user.email' => $user->getEmail(),
            'user.title' => $user->getTitle() ?? '',
            'user.alias' => $user->getAlias() ?? '',
            'user.display' => $user->getDisplayName(),
        ];

        foreach ($user->getPreferences() as $metaField) {
            $values = array_merge($values, [
                'user.meta.' . $metaField->getName() => $metaField->getValue(),
            ]);
        }

        return $values;
    }
}
