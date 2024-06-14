<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form;

use Symfony\Component\Form\AbstractType;

/**
 * @internal
 */
final class ContractByUserForm extends AbstractType
{
    public function getParent(): string
    {
        return YearByUserForm::class;
    }
}
