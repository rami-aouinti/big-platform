<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form field type to edit an optional invoice label.
 */
final class InvoiceLabelType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => 'invoiceLabel',
            'help' => 'help.invoiceLabel',
            'required' => false,
        ]);
    }

    public function getParent(): string
    {
        return TextareaType::class;
    }
}
