<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Select the pattern that will be used for calendar entry titles.
 */
final class CalendarTitlePatternType extends AbstractType
{
    public const PATTERN_CUSTOMER = '{customer}';
    public const PATTERN_PROJECT = '{project}';
    public const PATTERN_ACTIVITY = '{activity}';
    public const PATTERN_DESCRIPTION = '{description}';
    public const PATTERN_DURATION = '{duration}';
    public const SPACER = ' - ';
    public const PATTERN_ACTIVITY_DESCRIPTION = self::PATTERN_ACTIVITY . self::SPACER . self::PATTERN_DESCRIPTION;
    public const PATTERN_PROJECT_DESCRIPTION = self::PATTERN_PROJECT . self::SPACER . self::PATTERN_DESCRIPTION;
    public const PATTERN_CUSTOMER_DESCRIPTION = self::PATTERN_CUSTOMER . self::SPACER . self::PATTERN_DESCRIPTION;
    public const PATTERN_PROJECT_CUSTOMER = self::PATTERN_PROJECT . self::SPACER . self::PATTERN_CUSTOMER;
    public const PATTERN_CUSTOMER_PROJECT = self::PATTERN_CUSTOMER . self::SPACER . self::PATTERN_PROJECT;
    public const PATTERN_PROJECT_ACTIVITY = self::PATTERN_PROJECT . self::SPACER . self::PATTERN_ACTIVITY;

    public function __construct(
        private TranslatorInterface $translator
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $customer = $this->translator->trans('customer');
        $project = $this->translator->trans('project');
        $activity = $this->translator->trans('activity');
        $description = $this->translator->trans('description');
        $duration = $this->translator->trans('duration');

        $resolver->setDefaults([
            'label' => 'choice_pattern',
            'choices' => [
                $activity => self::PATTERN_ACTIVITY,
                $project => self::PATTERN_PROJECT,
                $customer => self::PATTERN_CUSTOMER,
                $description => self::PATTERN_DESCRIPTION,
                $duration => self::PATTERN_DURATION,
                $activity . self::SPACER . $description => self::PATTERN_ACTIVITY_DESCRIPTION,
                $project . self::SPACER . $description => self::PATTERN_PROJECT_DESCRIPTION,
                $customer . self::SPACER . $description => self::PATTERN_CUSTOMER_DESCRIPTION,
                $project . self::SPACER . $customer => self::PATTERN_PROJECT_CUSTOMER,
                $customer . self::SPACER . $project => self::PATTERN_CUSTOMER_PROJECT,
                $project . self::SPACER . $activity => self::PATTERN_PROJECT_ACTIVITY,
            ],
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
