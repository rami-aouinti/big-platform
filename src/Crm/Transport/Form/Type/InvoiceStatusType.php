<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Type;

use App\Crm\Domain\Entity\Invoice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class InvoiceStatusType
 *
 * @package App\Crm\Transport\Form\Type
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class InvoiceStatusType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => 'status',
            'multiple' => true,
            'choices' => [
                'status.' . Invoice::STATUS_NEW => Invoice::STATUS_NEW,
                'status.' . Invoice::STATUS_PENDING => Invoice::STATUS_PENDING,
                'status.' . Invoice::STATUS_PAID => Invoice::STATUS_PAID,
                'status.' . Invoice::STATUS_CANCELED => Invoice::STATUS_CANCELED,
            ],
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
