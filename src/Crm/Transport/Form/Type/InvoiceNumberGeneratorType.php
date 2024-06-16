<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Type;

use App\Crm\Application\Service\Invoice\ServiceInvoice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Custom form field type to select an invoice number generator.
 */
final class InvoiceNumberGeneratorType extends AbstractType
{
    public function __construct(
        private ServiceInvoice $service
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $renderer = [];
        foreach ($this->service->getNumberGenerator() as $generator) {
            $renderer[$generator->getId()] = $generator->getId();
        }

        $resolver->setDefaults([
            'label' => 'invoice_number_generator',
            'choices' => $renderer,
            'choice_label' => function ($renderer) {
                return 'invoice_number_generator.' . $renderer;
            },
            'search' => false,
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
