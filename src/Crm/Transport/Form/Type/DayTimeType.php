<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

final class DayTimeType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['attr'] = array_merge($view->vars['attr'], [
            'placeholder' => 'hh:mm',
        ]);
    }

    public function getParent(): string
    {
        return TextType::class;
    }
}
