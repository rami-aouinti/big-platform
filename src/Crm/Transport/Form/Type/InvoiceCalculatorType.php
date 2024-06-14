<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Type;

use App\Crm\Application\Service\Invoice\ServiceInvoice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Custom form field type to select an invoice calculator.
 */
final class InvoiceCalculatorType extends AbstractType
{
    public function __construct(
        private ServiceInvoice $service
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $renderer = [];
        foreach ($this->service->getCalculator() as $calculator) {
            $renderer[$calculator->getId()] = $calculator->getId();
        }

        $resolver->setDefaults([
            'label' => 'invoice_calculator',
            'choices' => $renderer,
            'choice_label' => function ($renderer) {
                return $renderer;
            },
            'help' => 'invoice_calculator.help',
            'translation_domain' => 'invoice-calculator',
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
