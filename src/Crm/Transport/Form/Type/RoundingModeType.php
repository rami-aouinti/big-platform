<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Type;

use App\Crm\Application\Service\Timesheet\RoundingService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Custom form field type to select the timesheet mode.
 */
final class RoundingModeType extends AbstractType
{
    public function __construct(
        private RoundingService $service
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $choices = [];

        foreach ($this->service->getRoundingModes() as $mode) {
            $id = $mode->getId();
            $choices[ucfirst($id)] = $id;
        }

        $resolver->setDefaults([
            'choices' => $choices,
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
