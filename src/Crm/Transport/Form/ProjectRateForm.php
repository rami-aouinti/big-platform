<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form;

use App\Crm\Domain\Entity\ProjectRate;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @package App\Crm\Transport\Form
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
class ProjectRateForm extends AbstractRateForm
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currency = null;

        if (!empty($options['data'])) {
            /** @var ProjectRate $rate */
            $rate = $options['data'];

            $customer = $rate->getProject()->getCustomer();
            if ($customer !== null) {
                $currency = $customer->getCurrency();
            }
        }

        $this->addFields($builder, $currency);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProjectRate::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'admin_project_rate_edit',
            'attr' => [
                'data-form-event' => 'kimai.projectUpdate',
            ],
        ]);
    }
}
