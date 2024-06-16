<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Custom form field type to select the initial view, where the user should be redirected to after login.
 */
final class InitialViewType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('required', true);
    }

    public function getParent(): string
    {
        return MenuChoiceType::class;
    }
}
