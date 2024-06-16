<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Extension;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Converts normal select boxes into javascript enhanced versions.
 */
final class EnhancedChoiceTypeExtension extends AbstractTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        return [EntityType::class, ChoiceType::class];
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        if (isset($options['selectpicker']) && $options['selectpicker'] === false) {
            return;
        }

        // expanded selects are rendered as checkboxes and using the selectpicker
        // would display an empty dropdown
        if (isset($options['expanded']) && $options['expanded'] === true) {
            return;
        }

        $extendedOptions = [
            'class' => 'selectpicker',
        ];

        if ($options['multiple']) {
            $extendedOptions['size'] = 1;
        }

        if ($options['width'] !== false) {
            $extendedOptions['data-width'] = $options['width'];
        }

        if ($options['search'] === false) {
            $extendedOptions['data-disable-search'] = 1;
        }

        // there is a very weird logic in vendor/symfony/twig-bridge/Resources/views/Form/form_div_layout.html.twig
        // in block "block choice_widget_collapsed" that resets "{% set required = false %}", so we fake it into the select
        if ($options['required'] === true && (!\array_key_exists('size', $options['attr']) || $options['attr']['size'] <= 1)) {
            $extendedOptions['required'] = 'required';
            $extendedOptions['placeholder'] = '';
        }

        $view->vars['attr'] = array_merge($view->vars['attr'], $extendedOptions);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined(['selectpicker']);
        $resolver->setAllowedTypes('selectpicker', 'boolean');
        $resolver->setDefault('selectpicker', true);

        $resolver->setDefined(['width']);
        $resolver->setAllowedTypes('width', ['string', 'boolean']);
        $resolver->setDefault('width', '100%');

        $resolver->setDefined(['search']);
        $resolver->setAllowedTypes('search', 'boolean');
        $resolver->setDefault('search', true);
    }
}
