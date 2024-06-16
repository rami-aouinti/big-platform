<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Type;

use App\Crm\Transport\Form\DataTransformer\StringToArrayTransformer;
use App\Crm\Transport\Form\Helper\ActivityHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\ReversedTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Select the pattern that will be used when rendering an activity select.
 */
final class ActivityTypePatternType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new ReversedTransformer(new StringToArrayTransformer(ActivityHelper::PATTERN_SPACER)), true);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $name = $this->translator->trans('name');
        $number = $this->translator->trans('activity_number');
        $comment = $this->translator->trans('description');

        $resolver->setDefaults([
            'label' => 'choice_pattern',
            'multiple' => true,
            'choices' => [
                $number => ActivityHelper::PATTERN_NUMBER,
                $name => ActivityHelper::PATTERN_NAME,
                $comment => ActivityHelper::PATTERN_COMMENT,
            ],
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
