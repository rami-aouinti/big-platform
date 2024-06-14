<?php

declare(strict_types=1);

namespace App\Reporting\YearByUser;

use App\Crm\Transport\Form\Type\ReportSumType;
use App\Crm\Transport\Form\Type\YearPickerType;
use App\User\Transport\Form\Type\Console\UserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<YearByUser>
 */
final class YearByUserForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('date', YearPickerType::class, [
            'model_timezone' => $options['timezone'],
            'view_timezone' => $options['timezone'],
            'start_date' => $options['start_date'],
        ]);

        if ($options['include_user']) {
            $builder->add('user', UserType::class, [
                'width' => false,
                'include_current_user_if_system_account' => true,
            ]);
        }
        $builder->add('sumType', ReportSumType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => YearByUser::class,
            'timezone' => date_default_timezone_get(),
            'start_date' => new \DateTime(),
            'include_user' => false,
            'csrf_protection' => false,
            'method' => 'GET',
        ]);
    }
}
