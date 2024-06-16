<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form;

use Symfony\Component\Form\FormBuilderInterface;

class TimesheetAdminEditForm extends TimesheetEditForm
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $options['allow_begin_datetime'] = true;
        $options['allow_end_datetime'] = true;
        $options['allow_duration'] = true;

        parent::buildForm($builder, $options);
    }

    protected function showCustomer(array $options, bool $isNew, int $customerCount): bool
    {
        return true;
    }
}
