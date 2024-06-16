<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType as BaseTimezoneType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<string>
 */
final class TimezoneType extends AbstractType
{
    public function getBlockPrefix(): string
    {
        return 'timezone_type';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => 'timezone',
            'intl' => false,
        ]);
    }

    public function getParent(): string
    {
        return BaseTimezoneType::class;
    }
}
