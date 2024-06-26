<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Type;

use App\Crm\Domain\Entity\Timesheet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Custom form field type to select if a timesheet is billable.
 * @extends AbstractType<string>
 */
final class TimesheetBillableType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'documentation' => [
                'description' => 'Whether this item should be refundable (yes) or not (no) or if it should be calculated by inherited settings from customer, project and activity (auto).',
            ],
            'label' => 'billable',
            'choices' => [
                'automatic' => Timesheet::BILLABLE_AUTOMATIC,
                'yes' => Timesheet::BILLABLE_YES,
                'no' => Timesheet::BILLABLE_NO,
            ],
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
