<?php

declare(strict_types=1);

namespace App\Crm\Application\Reporting\CustomerMonthlyProjects;

use App\Crm\Transport\Form\Type\CustomerType;
use App\Crm\Transport\Form\Type\MonthPickerType;
use App\Crm\Transport\Form\Type\ReportSumType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<CustomerMonthlyProjects>
 */
final class CustomerMonthlyProjectsForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('customer', CustomerType::class, [
            'required' => false,
            'width' => false,
        ]);

        $builder->add('date', MonthPickerType::class, [
            'model_timezone' => $options['timezone'],
            'view_timezone' => $options['timezone'],
            'start_date' => $options['start_date'],
        ]);

        $builder->add('sumType', ReportSumType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CustomerMonthlyProjects::class,
            'timezone' => date_default_timezone_get(),
            'start_date' => new \DateTime(),
            'csrf_protection' => false,
            'method' => 'GET',
        ]);
    }
}
