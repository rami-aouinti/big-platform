<?php

declare(strict_types=1);

namespace App\Crm\Application\Reporting\MonthlyUserList;

use App\User\Transport\Form\Type\Console\MonthPickerType;
use App\User\Transport\Form\Type\Console\ProjectType;
use App\User\Transport\Form\Type\Console\ReportSumType;
use App\User\Transport\Form\Type\Console\TeamType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<MonthlyUserList>
 */
final class MonthlyUserListForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('date', MonthPickerType::class, [
            'model_timezone' => $options['timezone'],
            'view_timezone' => $options['timezone'],
            'start_date' => $options['start_date'],
        ]);
        $builder->add('team', TeamType::class, [
            'multiple' => false,
            'required' => false,
            'width' => false,
        ]);
        $builder->add('project', ProjectType::class, [
            'multiple' => false,
            'required' => false,
            'width' => false,
        ]);
        $builder->add('sumType', ReportSumType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MonthlyUserList::class,
            'timezone' => date_default_timezone_get(),
            'start_date' => new \DateTime(),
            'csrf_protection' => false,
            'method' => 'GET',
        ]);
    }
}
