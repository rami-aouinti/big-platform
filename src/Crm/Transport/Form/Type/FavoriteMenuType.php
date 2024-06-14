<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Type;

use App\Crm\Transport\Form\DataTransformer\StringToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\ReversedTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Custom form field type to select the favorite menus.
 */
final class FavoriteMenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new ReversedTransformer(new StringToArrayTransformer()));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'multiple' => true,
            'filter_menus' => ['favorites', 'dashboard'],
        ]);
    }

    public function getParent(): string
    {
        return MenuChoiceType::class;
    }
}
