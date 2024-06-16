<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form field type to select if something is billable.
 */
final class BillableType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => 'billable',
            'help' => 'help.billable',
        ]);
    }

    public function getParent(): string
    {
        return YesNoType::class;
    }
}
