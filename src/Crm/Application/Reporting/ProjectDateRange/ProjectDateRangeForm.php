<?php

declare(strict_types=1);

namespace App\Crm\Application\Reporting\ProjectDateRange;

use App\Crm\Transport\Form\Type\CustomerType;
use App\Crm\Transport\Form\Type\MonthPickerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<ProjectDateRangeQuery>
 */
final class ProjectDateRangeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('customer', CustomerType::class, [
            'required' => false,
            'width' => false,
        ]);

        $builder->add('month', MonthPickerType::class, [
            'required' => true,
            'label' => false,
            'view_timezone' => $options['timezone'],
            'model_timezone' => $options['timezone'],
        ]);

        $builder->add('includeNoWork', CheckboxType::class, [
            'required' => false,
            'label' => 'includeNoWork',
        ]);

        $builder->add('budgetType', ChoiceType::class, [
            'placeholder' => null,
            'required' => false,
            'multiple' => false,
            'expanded' => true,
            'choices' => [
                'all' => null,
                'includeNoBudget' => 'none',
                'includeBudgetType_full' => 'full',
                'includeBudgetType_month' => 'month',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProjectDateRangeQuery::class,
            'timezone' => date_default_timezone_get(),
            'csrf_protection' => false,
            'method' => 'GET',
        ]);
    }
}
