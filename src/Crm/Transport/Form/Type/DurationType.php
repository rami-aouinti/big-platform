<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Type;

use App\Crm\Transport\Form\DataTransformer\DurationStringToSecondsTransformer;
use App\Crm\Transport\Validator\Constraints\Duration as DurationConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Custom form field type to handle duration strings.
 */
final class DurationType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => 'duration',
            'constraints' => [new DurationConstraint()],
            'preset_hours' => null,
            'preset_minutes' => null,
            'toggle' => false,
            'max_hours' => 24,
            'icon' => 'clock',
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $class = 'duration-input';
        if (isset($view->vars['attr']['class'])) {
            $class .= ' ' . $view->vars['attr']['class'];
        }
        $view->vars['attr']['class'] = $class;
        $view->vars['toggle'] = $options['toggle'];

        if ($options['preset_hours'] !== null && $options['preset_minutes'] !== null) {
            $intervalMinutes = (int)$options['preset_minutes'];
            $maxHours = (int)$options['preset_hours'];

            if ($intervalMinutes < 1 || $maxHours < 1) {
                return;
            }

            // we track times for humans and no entry should ever be that long
            if ($maxHours > $options['max_hours']) {
                $maxHours = $options['max_hours'];
            }

            $maxMinutes = $maxHours * 60;
            $presets = [];

            for ($minutes = $intervalMinutes; $minutes <= $maxMinutes; $minutes += $intervalMinutes) {
                $h = (int)($minutes / 60);
                $m = $minutes % 60;
                $interval = new \DateInterval('PT' . $h . 'H' . $m . 'M');
                $presets[] = $interval->format('%h:%I');
            }

            $view->vars['duration_presets'] = $presets;
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new DurationStringToSecondsTransformer());
    }

    public function getParent(): string
    {
        return TextType::class;
    }
}
