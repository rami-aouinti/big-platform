<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @package App\Crm\Transport\Form\Type
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class BillableSearchType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => 'billable',
            'choices' => [
                'all' => null,
                'yes' => true,
                'no' => false,
            ],
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
