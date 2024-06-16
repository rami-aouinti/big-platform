<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form;

use App\Crm\Transport\Form\Type\YesNoType;
use App\User\Transport\Form\Type\Console\UserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @package App\Crm\Transport\Form
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
abstract class AbstractRateForm extends AbstractType
{
    protected function addFields(FormBuilderInterface $builder, ?string $currency): void
    {
        $builder
            ->add('user', UserType::class, [
                'required' => false,
            ])
            ->add('rate', MoneyType::class, [
                // documentation is for NelmioApiDocBundle
                'documentation' => [
                    'type' => 'number',
                    'description' => 'The rate (eg. 10.5)',
                ],
                'label' => 'rate',
                'attr' => [
                    'autofocus' => 'autofocus',
                ],
                'currency' => $currency,
                'help' => 'help.rate',
            ])
            ->add('internalRate', MoneyType::class, [
                // documentation is for NelmioApiDocBundle
                'documentation' => [
                    'type' => 'number',
                    'description' => 'The internal rate (eg. 10.0 or 10)',
                ],
                'label' => 'internalRate',
                'currency' => $currency,
                'required' => false,
                'help' => 'help.internalRate',
            ])
            ->add('isFixed', YesNoType::class, [
                'label' => 'fixedRate',
                'help' => 'help.fixedRate',
                'documentation' => [
                    'type' => 'boolean',
                    'description' => 'If "true" each time record gets the same rate, regardless of its duration',
                ],
            ])
        ;
    }
}
