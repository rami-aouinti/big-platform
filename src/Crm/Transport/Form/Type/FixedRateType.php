<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Custom form field type to set the fixed rate.
 */
final class FixedRateType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // documentation is for NelmioApiDocBundle
            'documentation' => [
                'type' => 'number',
                'description' => 'Fixed rate',
            ],
            'required' => false,
            'label' => 'fixedRate',
        ]);
    }

    public function getParent(): string
    {
        return MoneyType::class;
    }
}
