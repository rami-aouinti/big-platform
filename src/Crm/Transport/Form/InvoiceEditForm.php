<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form;

use App\Crm\Application\Service\Invoice;
use App\Crm\Transport\Form\Type\DatePickerType;
use App\Crm\Transport\Form\Type\MetaFieldsCollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class InvoiceEditForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $dateTimeOptions = [
            'model_timezone' => $options['timezone'],
            'view_timezone' => $options['timezone'],
        ];

        $builder
            ->add('comment', TextareaType::class, [
                'label' => 'description',
                'required' => false,
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'status.new' => Invoice::STATUS_NEW,
                    'status.pending' => Invoice::STATUS_PENDING,
                    'status.paid' => Invoice::STATUS_PAID,
                    'status.canceled' => Invoice::STATUS_CANCELED,
                ],
                'label' => 'status',
                'required' => true,
            ])
            ->add('paymentDate', DatePickerType::class, array_merge($dateTimeOptions, [
                'label' => 'invoice.payment_date',
                'required' => false,
            ]));

        $builder->add('metaFields', MetaFieldsCollectionType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Invoice::class,
            'timezone' => date_default_timezone_get(),
            'attr' => [
                'data-form-event' => 'kimai.invoiceUpdate',
            ],
        ]);
    }
}
