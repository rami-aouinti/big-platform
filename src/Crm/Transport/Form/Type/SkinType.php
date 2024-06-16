<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Custom form field type to select the themes skin.
 */
final class SkinType extends AbstractType
{
    public const THEMES = [
        'skin.light' => 'default',
        'skin.dark' => 'dark',
    ];

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => 'skin',
            'search' => false,
            'required' => true,
            'choices' => self::THEMES,
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
