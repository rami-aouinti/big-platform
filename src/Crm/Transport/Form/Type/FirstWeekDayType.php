<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Custom form field type to select the first of the week.
 */
final class FirstWeekDayType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $choices = [
            'Monday' => 'monday',
            'Sunday' => 'sunday',
        ];

        $resolver->setDefaults([
            'multiple' => false,
            'choices' => $choices,
            'label' => 'first_weekday',
            'translation_domain' => 'system-configuration',
            'search' => false,
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
