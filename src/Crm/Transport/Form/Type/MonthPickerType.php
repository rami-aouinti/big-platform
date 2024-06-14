<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Custom form field type to select a month via picker and select previous and next month.
 *
 * Always falls back to the current month if none or an invalid date is given.
 */
final class MonthPickerType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'widget' => 'single_text',
            'html5' => false,
            'format' => DateType::HTML5_FORMAT,
            'start_date' => new \DateTime(),
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        /** @var \DateTime|null $date */
        $date = $form->getData();

        if ($date === null) {
            $date = $options['start_date'];
        }

        $view->vars['month'] = $date;
        $view->vars['previousMonth'] = (clone $date)->modify('-1 month');
        $view->vars['nextMonth'] = (clone $date)->modify('+1 month');
    }

    public function getParent(): string
    {
        return DateType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'monthpicker';
    }
}
