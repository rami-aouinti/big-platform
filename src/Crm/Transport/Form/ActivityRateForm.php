<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form;

use App\Crm\Domain\Entity\ActivityRate;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActivityRateForm extends AbstractRateForm
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currency = null;

        if (!empty($options['data'])) {
            /** @var ActivityRate $rate */
            $rate = $options['data'];

            if ($rate->getActivity() !== null && !$rate->getActivity()->isGlobal()) {
                $currency = $rate->getActivity()->getProject()->getCustomer()->getCurrency();
            }
        }

        $this->addFields($builder, $currency);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ActivityRate::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'admin_customer_rate_edit',
            'attr' => [
                'data-form-event' => 'kimai.activityUpdate',
            ],
        ]);
    }
}
