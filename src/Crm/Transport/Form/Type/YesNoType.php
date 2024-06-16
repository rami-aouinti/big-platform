<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Custom form field type to select between Yes and No.
 * @extends AbstractType<bool>
 */
final class YesNoType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'value' => true,
            'false_values' => [null, 0, false, 'false', '', '0'],
            'required' => false,
            'label_attr' => [
                'class' => 'checkbox-switch',
            ],
        ]);
    }

    public function getParent(): string
    {
        return CheckboxType::class;
    }
}
